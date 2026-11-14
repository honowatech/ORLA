<?php

namespace App\Actions\Commandes;

use App\Models\commandes\Commandes;

/**
 * L'administrateur confie une commande à un agent routeur, qui ne voit que
 * les commandes qu'on lui a confiées.
 *
 * Extrait de Admin\CommandesController::attribuate_agent, partagé avec le
 * générateur de la démo.
 */
class ConfierCommandeAgent
{
    public function handle(Commandes $commande, $idAgent): void
    {
        $commande->id_agent = $idAgent;
        $commande->save();
    }
}
