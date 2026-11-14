<?php

namespace App\Actions\Commandes;

use App\Models\Activity;
use App\Models\commandes\Commandes;

/**
 * Journal d'activité du coursier après une tentative de changement de statut.
 *
 * Extrait du contrôleur Coursier, partagé avec le générateur de la démo.
 */
class NoterActiviteLivraison
{
    private const TITRES = [
        'livre' => 'Livraison terminée',
        'annulee' => 'Livraison annulée',
        'echoue' => 'Livraison échouée',
        'encours' => 'Livraison En cours',
    ];

    public function handle(Commandes $commande, string $statut, $idUser, bool $reussi): Activity
    {
        $activity = new Activity;
        $activity->lien = route('Coursiercommandes.show', $commande->id);
        $activity->texte_lien = 'Voir la commande';
        $activity->jour = now();
        $activity->heure = now()->format('H:i:s');
        $activity->id_user = $idUser;

        if (! $reussi) {
            $activity->title = "Echec de l'action";
            $activity->color = 'danger';
            $activity->message = 'Le changement de statut a échoué';
        } elseif ($statut == 'encours') {
            $activity->title = self::TITRES['encours'];
            $activity->color = 'dark';
            $activity->message = 'Livraison mise en cours avec <b class="text-success">succès</b>';
        } else {
            $activity->title = self::TITRES[$statut] ?? 'Statut modifié';
            $activity->color = $statut == 'livre' ? 'success' : 'danger';
            $activity->message = $activity->title.' avec <b class="text-success">succès</b>';
        }
        $activity->save();

        return $activity;
    }
}
