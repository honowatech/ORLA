<?php

namespace App\Services\Commandes;

use App\Enums\CommandeStatut;
use App\Models\Commandes\Commandes;

/**
 * Change le statut d'une commande en respectant le cycle de vie
 * (CommandeStatut) et en datant la mise en cours et la fin de livraison.
 */
class ChangerStatutCommande
{
    /**
     * @return bool false si la transition n'est pas autorisée (rien n'est modifié)
     */
    public function __invoke(Commandes $commande, string $nouveauStatut): bool
    {
        $actuel = CommandeStatut::tryFrom((string) $commande->statut);
        $nouveau = CommandeStatut::tryFrom($nouveauStatut);

        if ($actuel === null || $nouveau === null || ! $actuel->peutPasserA($nouveau)) {
            return false;
        }

        $commande->statut = $nouveau->value;
        if ($nouveau === CommandeStatut::EnCours) {
            $commande->date_mise_encours = now();
        }
        if ($nouveau->estTermine()) {
            $commande->date_mise_encours ??= now();
            $commande->date_livre = now();
        }
        $commande->save();

        return true;
    }
}
