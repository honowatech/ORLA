<?php

namespace App\Demo\Generator;

use App\Actions\Commandes\EnregistrerCommande;
use App\Models\Activity;
use App\Models\commandes\Commandes;
use App\Models\SuperAdmin\Client as AgencyAccount;
use App\Models\users\Users;
use Carbon\CarbonInterface;
use RuntimeException;

/**
 * Contrôle final d'un bac : un bac incohérent est jeté, jamais servi.
 */
class SandboxCheck
{
    public function __construct(
        private EnregistrerCommande $enregistrer,
    ) {}

    /** @return string[] les anomalies trouvées */
    public function problems(DemoAgency $agency, CarbonInterface $now): array
    {
        $problems = [];

        if ($agency->refused > 0) {
            $problems[] = $agency->refused.' action(s) refusée(s) pendant le rejeu';
        }

        foreach (config('demo.profiles') as $slug => $profile) {
            $user = Users::where('email', $slug.'@'.config('demo.email_domain'))->first();
            if ($user === null || $user->statut != 1 || $user->type_utilisateur?->libelle !== $profile['type']) {
                $problems[] = "profil « $slug » sans utilisateur actif du bon type";
            }
        }

        if (Users::where('email', 'not like', '%@'.config('demo.email_domain'))->exists()) {
            $problems[] = 'utilisateur hors du domaine réservé';
        }

        $account = AgencyAccount::where('statut', 1)->first();
        if ($account === null || $account->date_fin === null || $now->greaterThan($account->date_fin)) {
            $problems[] = "fiche d'abonnement absente ou échue : l'agence serait bloquée";
        }

        foreach ([Commandes::class, Activity::class, Users::class] as $model) {
            if ($model::where('created_at', '>', $now)->exists()) {
                $problems[] = class_basename($model).' créé après « maintenant »';
            }
        }

        $commandes = Commandes::get();
        if ($commandes->isEmpty()) {
            $problems[] = 'aucune commande';
        }
        if (! Commandes::whereNotNull('id_agent')->exists()) {
            $problems[] = "aucune commande confiée à l'agent : son profil serait vide";
        }
        foreach ($commandes as $c) {
            $id = "commande {$c->id}";
            if (! in_array($c->statut, ['attente', 'attribue', 'encours', 'livre', 'annulee', 'echoue'])) {
                $problems[] = "$id : statut {$c->statut}";
            }
            if (in_array($c->statut, ['attribue', 'encours', 'livre', 'echoue']) && $c->id_coursier === null) {
                $problems[] = "$id : {$c->statut} sans coursier";
            }
            if (in_array($c->statut, ['livre', 'annulee', 'echoue']) && $c->date_livre === null) {
                $problems[] = "$id : terminée sans date";
            }
            $dates = array_filter([$c->created_at, $c->date_mise_encours, $c->date_livre]);
            $times = array_map(fn ($d) => strtotime((string) $d), $dates);
            $sorted = $times;
            sort($sorted);
            if ($times !== $sorted || max($times) > $now->getTimestamp()) {
                $problems[] = "$id : dates dans le désordre ou futures";
            }
            if ($c->id_montant_livraison != $this->enregistrer->tarifDeZone($c->id_quartier_colis, $c->id_quartier_livraison)) {
                $problems[] = "$id : tarif de zone incohérent";
            }
        }

        return $problems;
    }

    public function assert(DemoAgency $agency, CarbonInterface $now): void
    {
        $problems = $this->problems($agency, $now);
        if ($problems !== []) {
            throw new RuntimeException('Bac démo incohérent : '.implode(' ; ', array_slice($problems, 0, 10)));
        }
    }
}
