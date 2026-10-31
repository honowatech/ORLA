<?php

namespace App\Services\Routage;

use App\Models\commandes\Commandes;
use App\Models\quartier\Quartier;
use App\Models\tournee\Tournee;
use App\Models\tournee\Tournee_etape;
use App\Models\Troncon_route;
use App\Models\Type_vehicule;
use App\Models\Vehicule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Pont entre la base de données (quartiers, tronçons, commandes, véhicules)
 * et l'optimiseur d'itinéraires.
 */
class TourneeService
{
    /** Construit le graphe routier d'une ville à partir des quartiers et tronçons actifs. */
    public function reseauPourVille(int $idVille): ReseauRoutier
    {
        $reseau = new ReseauRoutier();
        foreach (Quartier::where('id_ville', $idVille)->get() as $q) {
            $reseau->ajouterQuartier(
                (int) $q->id,
                $q->libelle,
                $q->id_zone !== null ? (int) $q->id_zone : null,
                $q->latitude !== null ? (float) $q->latitude : null,
                $q->longitude !== null ? (float) $q->longitude : null,
            );
        }
        foreach (Troncon_route::where('id_ville', $idVille)->where('statut', 1)->get() as $t) {
            $fermeture = ($t->heure_debut_fermeture && $t->heure_fin_fermeture)
                ? [substr($t->heure_debut_fermeture, 0, 5), substr($t->heure_fin_fermeture, 0, 5)]
                : null;
            $reseau->ajouterTroncon(
                (int) $t->id_quartier_depart,
                (int) $t->id_quartier_arrivee,
                (float) $t->distance_km,
                $t->duree_minutes !== null ? (float) $t->duree_minutes : null,
                $t->etat_route,
                (bool) $t->bidirectionnel,
                $fermeture,
                $t->types_vehicule_interdits ?? [],
            );
        }
        return $reseau;
    }

    /** Contraintes issues du véhicule choisi (ou d'un type de véhicule). */
    public function contraintesPourVehicule(?Vehicule $vehicule, ?Type_vehicule $type = null): ContraintesVehicule
    {
        $type = $type ?? $vehicule?->type;
        if ($type === null) {
            return new ContraintesVehicule(vitesseKmh: (float) config('routage.vitesse_defaut', 30));
        }
        return new ContraintesVehicule(
            idType: (int) $type->id,
            vitesseKmh: (float) ($type->vitesse_moyenne ?: config('routage.vitesse_defaut', 30)),
            coutKm: (float) $type->cout_km,
            coutHeure: (float) $type->cout_heure,
            plageHoraire: $type->plageHoraire(),
            capaciteColis: $type->capacite_colis !== null ? (int) $type->capacite_colis : null,
        );
    }

    /**
     * Transforme des commandes en arrêts (enlèvement au quartier du colis,
     * livraison au quartier de livraison).
     *
     * @param Collection<int, Commandes> $commandes
     * @return Arret[]
     */
    public function arretsPourCommandes(Collection $commandes, bool $avecEnlevement = true): array
    {
        $arrets = [];
        foreach ($commandes as $c) {
            $nom = trim(($c->nom_client ?? '') !== '' ? $c->nom_client : ($c->client->noms ?? 'Client'));
            if ($avecEnlevement && $c->id_quartier_colis && $c->id_quartier_colis != $c->id_quartier_livraison) {
                $arrets[] = new Arret((int) $c->id, Arret::ENLEVEMENT, (int) $c->id_quartier_colis, null, 'Enlèvement n°'.$c->id.' — '.$nom);
            }
            if ($c->id_quartier_livraison) {
                $echeance = $c->date_livraison ? strtotime($c->date_livraison) : null;
                $arrets[] = new Arret((int) $c->id, Arret::LIVRAISON, (int) $c->id_quartier_livraison, $echeance, 'Livraison n°'.$c->id.' — '.$nom);
            }
        }
        return $arrets;
    }

    /**
     * Lance une simulation complète.
     *
     * @param Collection<int, Commandes> $commandes
     * @return array{optimise: ResultatItineraire, reference: ResultatItineraire, reseau: ReseauRoutier, vehicule: ContraintesVehicule}
     */
    public function simuler(int $idVille, Collection $commandes, int $idQuartierDepart, int $heureDepart, ?Vehicule $vehicule, bool $avecEnlevement = true): array
    {
        $reseau = $this->reseauPourVille($idVille);
        $contraintes = $this->contraintesPourVehicule($vehicule);
        $optimiseur = new RouteOptimizer($reseau, config('routage', []));
        $arrets = $this->arretsPourCommandes($commandes, $avecEnlevement);

        // référence : ordre chronologique des dates de livraison promises (pratique courante)
        $reference = $arrets;
        usort($reference, function (Arret $a, Arret $b) {
            $ea = $a->echeance ?? PHP_INT_MAX;
            $eb = $b->echeance ?? PHP_INT_MAX;
            if ($a->idCommande === $b->idCommande) {
                return $a->estEnlevement() ? -1 : 1;
            }
            return $ea <=> $eb;
        });
        // les enlèvements d'une commande doivent précéder sa livraison dans la référence
        $reference = $this->reordonnerPrecedence($reference);

        return [
            'optimise' => $optimiseur->optimiser($arrets, $idQuartierDepart, $heureDepart, $contraintes),
            'reference' => $optimiseur->evaluer($reference, $idQuartierDepart, $heureDepart, $contraintes),
            'reseau' => $reseau,
            'vehicule' => $contraintes,
        ];
    }

    /** Enregistre la tournée optimisée et attribue les commandes au coursier. */
    public function enregistrer(array $simulation, int $idVille, int $idCoursier, ?int $idVehicule, int $idQuartierDepart, int $idSaver): Tournee
    {
        /** @var ResultatItineraire $opt */
        $opt = $simulation['optimise'];
        /** @var ResultatItineraire $ref */
        $ref = $simulation['reference'];

        return DB::transaction(function () use ($opt, $ref, $idVille, $idCoursier, $idVehicule, $idQuartierDepart, $idSaver) {
            $tournee = Tournee::create([
                'id_ville' => $idVille,
                'id_coursier' => $idCoursier,
                'id_vehicule' => $idVehicule,
                'id_quartier_depart' => $idQuartierDepart,
                'id_saver' => $idSaver,
                'date_depart' => date('Y-m-d H:i:s', $opt->heureDepart),
                'date_fin_estimee' => date('Y-m-d H:i:s', $opt->heureFin),
                'distance_totale_km' => round($opt->distanceKm, 2),
                'duree_totale_minutes' => round($opt->dureeMinutes),
                'cout_total' => round($opt->cout),
                'distance_reference_km' => round($ref->distanceKm, 2),
                'duree_reference_minutes' => round($ref->dureeMinutes),
                'cout_reference' => round($ref->cout),
                'avertissements' => $opt->avertissements,
                'statut' => 'planifiee',
            ]);

            foreach ($opt->etapes as $e) {
                Tournee_etape::create([
                    'id_tournee' => $tournee->id,
                    'id_commande' => $e['arret']->idCommande,
                    'id_quartier' => $e['arret']->idQuartier,
                    'type_etape' => $e['arret']->type,
                    'ordre' => $e['ordre'],
                    'heure_arrivee_estimee' => date('Y-m-d H:i:s', $e['heure_arrivee']),
                    'distance_etape_km' => round($e['distance_km'], 2),
                    'duree_etape_minutes' => round($e['duree_minutes']),
                    'attente_minutes' => round($e['attente_minutes']),
                    'retard_minutes' => round($e['retard_minutes']),
                    'itineraire' => $e['chemin'],
                ]);
            }

            $idsCommandes = array_unique(array_map(fn ($e) => $e['arret']->idCommande, $opt->etapes));
            Commandes::whereIn('id', $idsCommandes)->update(['id_coursier' => $idCoursier, 'statut' => 'attribue']);

            return $tournee;
        });
    }

    /** @param Arret[] $sequence */
    private function reordonnerPrecedence(array $sequence): array
    {
        $resultat = [];
        $enleves = [];
        $enAttente = [];
        foreach ($sequence as $a) {
            if ($a->estEnlevement()) {
                $resultat[] = $a;
                $enleves[$a->idCommande] = true;
                if (isset($enAttente[$a->idCommande])) {
                    $resultat[] = $enAttente[$a->idCommande];
                    unset($enAttente[$a->idCommande]);
                }
            } elseif ($this->possedeEnlevement($sequence, $a->idCommande) && !isset($enleves[$a->idCommande])) {
                $enAttente[$a->idCommande] = $a;
            } else {
                $resultat[] = $a;
            }
        }
        return array_merge($resultat, array_values($enAttente));
    }

    private function possedeEnlevement(array $sequence, int $idCommande): bool
    {
        foreach ($sequence as $a) {
            if ($a->idCommande === $idCommande && $a->estEnlevement()) {
                return true;
            }
        }
        return false;
    }
}
