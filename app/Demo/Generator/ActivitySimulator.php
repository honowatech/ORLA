<?php

namespace App\Demo\Generator;

use App\Actions\Commandes\ChangerStatutCommande;
use App\Actions\Commandes\ConfierCommandeAgent;
use App\Actions\Commandes\EnregistrerCommande;
use App\Actions\Commandes\NoterActiviteLivraison;
use App\Demo\DemoClock;
use App\Models\commandes\Commandes;
use App\Models\montant_livraison\Montant_livraison;
use App\Models\quartier\Quartier;
use Carbon\CarbonInterface;

/**
 * Activité de l'agence, planifiée d'abord (tous les tirages faits d'avance),
 * puis rejouée dans l'ordre chronologique par les actions métier, l'horloge
 * réglée sur chaque événement. Ce qui est encore à venir n'est pas rejoué :
 * l'agence s'arrête là où en est la journée.
 */
class ActivitySimulator
{
    /** Commandes moyennes par jour ouvré, en fin de période. */
    private const DAILY_ORDERS = 14;

    private const WEEKDAY_FACTOR = [1 => 0.9, 2 => 1.0, 3 => 1.0, 4 => 1.05, 5 => 1.25, 6 => 1.35, 7 => 0.35];

    public function __construct(
        private EnregistrerCommande $enregistrer,
        private ChangerStatutCommande $statut,
        private NoterActiviteLivraison $activite,
        private ConfierCommandeAgent $confier,
    ) {}

    public function run(DemoAgency $agency, CarbonInterface $now, int $aheadDays): void
    {
        $agency->refused = 0;
        $events = $this->plan($agency, $now, $aheadDays);

        usort($events, fn ($a, $b) => [$a['at']->getTimestamp(), $a['seq']] <=> [$b['at']->getTimestamp(), $b['seq']]);

        $commandes = [];
        foreach ($events as $event) {
            if ($event['at']->greaterThan($now)) {
                break;
            }
            DemoClock::at($event['at']);
            $this->play($agency, $event, $commandes);
        }
    }

    /**
     * @return array<int, array{at: CarbonInterface, seq: int, type: string, order: int, data?: array}>
     */
    private function plan(DemoAgency $agency, CarbonInterface $now, int $aheadDays): array
    {
        $tz = config('demo.timezone');
        $rng = $agency->rng;
        $today = $now->copy()->setTimezone($tz)->startOfDay();
        $days = (int) $agency->historyStart->diffInDays($today);

        $events = [];
        $seq = 0;
        $order = 0;

        for ($d = 0; $d <= $days; $d++) {
            $day = $agency->historyStart->copy()->addDays($d);
            $growth = 0.75 + 0.35 * ($days > 0 ? $d / $days : 1);
            $monthEnd = $day->day >= 25 ? 1.2 : 1.0;
            $expected = self::DAILY_ORDERS * self::WEEKDAY_FACTOR[$day->dayOfWeekIso] * $growth * $monthEnd;
            $count = max(0, (int) round($expected * $rng->getFloat(0.8, 1.2)));

            for ($k = 0; $k < $count; $k++) {
                $order++;
                $created = $day->copy()->setTime(7, 30)->addMinutes($rng->getInt(0, 450));

                // Livraison le jour même, le lendemain, ou planifiée quelques jours plus tard.
                $roll = $rng->getFloat(0, 1);
                if ($roll < 0.70) {
                    $delivery = $created->copy()->addMinutes($rng->getInt(120, 360));
                } elseif ($roll < 0.92 || $aheadDays < 2) {
                    $delivery = $created->copy()->addDay()->setTime($rng->getInt(9, 16), $rng->getInt(0, 3) * 15);
                } else {
                    $delivery = $created->copy()->addDays($rng->getInt(2, $aheadDays))->setTime($rng->getInt(9, 16), 0);
                }
                if ($delivery->hour >= 19) {
                    $delivery = $delivery->copy()->addDay()->setTime(10, 0);
                }

                $data = $this->orderData($agency, $delivery);
                $events[] = ['at' => $created->copy()->utc(), 'seq' => $seq++, 'type' => 'create', 'order' => $order, 'data' => $data];

                // La direction confie presque toutes les commandes à l'agent routeur.
                if ($agency->chance(0.9)) {
                    $events[] = ['at' => $created->copy()->addMinutes($rng->getInt(2, 8))->utc(), 'seq' => $seq++, 'type' => 'agent', 'order' => $order];
                }

                // 2 % annulées par l'agence avant attribution.
                if ($agency->chance(0.02)) {
                    $events[] = ['at' => $created->copy()->addMinutes($rng->getInt(20, 90))->utc(), 'seq' => $seq++, 'type' => 'status', 'order' => $order, 'statut' => 'annulee'];

                    continue;
                }

                $assigned = $created->copy()->addMinutes($rng->getInt(10, 80));
                $events[] = ['at' => $assigned->utc(), 'seq' => $seq++, 'type' => 'assign', 'order' => $order,
                    'coursier' => $this->coursierFor($agency, $data['id_quartier_livraison'])];

                $start = $delivery->copy()->subMinutes($rng->getInt(30, 75));
                if ($start->lessThan($assigned)) {
                    $start = $assigned->copy()->addMinutes(5);
                }
                $events[] = ['at' => $start->utc(), 'seq' => $seq++, 'type' => 'courier', 'order' => $order, 'statut' => 'encours'];

                $outcome = $rng->getFloat(0, 1);
                $final = $outcome < 0.88 ? 'livre' : ($outcome < 0.94 ? 'annulee' : 'echoue');
                $end = $delivery->copy()->addMinutes($rng->getInt(-20, 40));
                if ($end->lessThanOrEqualTo($start)) {
                    $end = $start->copy()->addMinutes(15);
                }
                $events[] = ['at' => $end->utc(), 'seq' => $seq++, 'type' => 'courier', 'order' => $order, 'statut' => $final];
            }
        }

        return $events;
    }

    /** Tous les tirages d'une commande, faits à la planification. */
    private function orderData(DemoAgency $agency, CarbonInterface $delivery): array
    {
        $rng = $agency->rng;
        $quartiers = $agency->quartiers;

        if ($agency->chance(0.55)) {
            $shop = $agency->pick($agency->shops);
            $client = $shop['client'];
            $colis = Quartier::find($shop['boutique']->id_quartier);
            $lines = [];
            $total = 0;
            foreach ($this->sample($agency, $shop['produits'], $rng->getInt(1, min(3, count($shop['produits'])))) as [$produit, $prix]) {
                $qty = $rng->getInt(1, 3);
                $lines[] = [$produit, $qty];
                $total += $qty * $prix;
            }
            $type = 'entreprise';
            // Les commandes de la boutique du profil Client sont souvent saisies par elle-même.
            $saver = $shop['user'] !== null && $agency->chance(0.6) ? $shop['user']->id : $agency->agentUser->id;
            $nom = trim($client->noms.' '.$client->Prenoms);
            $boutique = $shop['boutique']->id;
            $recuperer = $agency->chance(0.85) ? $total : null;
            $contactColis = $client->telephone;
        } else {
            $client = $agency->pick($agency->individuals);
            $colis = $agency->pick(array_values($quartiers));
            $lines = [];
            $type = 'simple';
            $saver = $agency->agentUser->id;
            $nom = $client->noms.' '.$client->Prenoms;
            $boutique = null;
            $recuperer = $agency->chance(0.35) ? $rng->getInt(4, 60) * 500 : null;
            $contactColis = $client->telephone;
        }

        do {
            $livraison = $agency->pick(array_values($quartiers));
        } while ($livraison->id === $colis->id);

        [$first, $last] = $agency->name();
        $contactLivraison = $agency->phone();

        return [
            'id_client' => $client->id,
            'telephone' => $client->telephone,
            'nom_client' => $nom,
            'id_boutique' => $boutique,
            'id_saver' => $saver,
            'type_commande' => $type,
            'adresse_colis' => $contactColis.'*/*'.$colis->libelle.'*/*'.$agency->pick(DemoCatalogue::LANDMARKS),
            'id_quartier_colis' => $colis->id,
            'adresse_livraison' => $contactLivraison.'*/*'.$livraison->libelle.'*/*'.$agency->pick(DemoCatalogue::LANDMARKS)."*/*$first $last",
            'id_quartier_livraison' => $livraison->id,
            'montant_recuperer' => $recuperer,
            'date_livraison' => $delivery->copy()->utc(),
            'description' => $agency->pick(DemoCatalogue::PARCEL_DESCRIPTIONS),
            'mode_de_paiement' => $agency->chance(0.7) ? 'coursier' : 'speedex',
            'lines' => $lines,
        ];
    }

    private function play(DemoAgency $agency, array $event, array &$commandes): void
    {
        if ($event['type'] === 'create') {
            $data = $event['data'];
            $lines = $data['lines'];
            unset($data['lines']);
            $data['montant_livraison'] = $this->price($data['id_quartier_colis'], $data['id_quartier_livraison']);
            $commandes[$event['order']] = $this->enregistrer->handle($data, $lines)->id;

            return;
        }

        $commande = Commandes::findOrFail($commandes[$event['order']]);

        if ($event['type'] === 'agent') {
            $this->confier->handle($commande, $agency->agentUser->id_agent);

            return;
        }

        if ($event['type'] === 'assign') {
            $ok = $this->statut->handle($commande, 'attribue', $event['coursier']['coursier']->id);
        } elseif ($event['type'] === 'courier') {
            $ok = $this->statut->handle($commande, $event['statut']);
            $this->activite->handle($commande, $event['statut'], $this->courierUserId($commande), $ok);
        } else {
            $ok = $this->statut->handle($commande, $event['statut']);
        }

        if (! $ok) {
            $agency->refused++;
        }
    }

    /** Tarif que le formulaire aurait proposé : celui de la paire de zones, sinon 1 000 XAF. */
    private function price(int $colis, int $livraison): float
    {
        $id = $this->enregistrer->tarifDeZone($colis, $livraison);

        return $id === null ? 1000 : (float) Montant_livraison::findOrFail($id)->montant;
    }

    private function coursierFor(DemoAgency $agency, int $idQuartier): array
    {
        $zone = Quartier::find($idQuartier)?->id_zone;
        $candidates = array_values(array_filter($agency->coursiers, fn ($c) => in_array($zone, $c['zones'])));

        return $agency->pick($candidates ?: $agency->coursiers);
    }

    private function courierUserId(Commandes $commande): ?int
    {
        return $commande->coursier?->id_utilisateur;
    }

    private function sample(DemoAgency $agency, array $items, int $n): array
    {
        $keys = $agency->rng->pickArrayKeys($items, $n);

        return array_map(fn ($k) => $items[$k], $keys);
    }
}
