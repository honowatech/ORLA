<?php

namespace App\Actions\Commandes;

use App\Models\commandes\Commandes;

/**
 * Cycle de vie d'une commande : attribution d'un coursier et changement de statut.
 *
 * Extrait des contrôleurs Admin, Client et Coursier, qui le partagent avec le
 * générateur de la démo. Les dates (mise en cours, livraison) sont celles de
 * l'horloge courante.
 */
class ChangerStatutCommande
{
    public const STATUTS = ['attente', 'attribue', 'encours', 'livre', 'annulee', 'echoue'];

    /**
     * Statuts que l'on peut atteindre depuis le statut actuel.
     *
     * attente  → attribue, annulee, echoue
     * attribue → encours, livre, annulee, echoue
     * encours  → livre, annulee, echoue
     * Un statut final (livre, annulee, echoue, supprime) n'en a aucun.
     */
    public function transitionsPossibles(?string $statutActuel): array
    {
        $depart = match ($statutActuel) {
            'attente' => 1,
            'attribue' => 2,
            'encours' => 3,
            default => null,
        };
        if ($depart === null) {
            return [];
        }

        $possibles = [];
        for ($i = $depart; $i < count(self::STATUTS); $i++) {
            if ($statutActuel == 'attente' && in_array($i, [2, 3])) {
                continue;
            }
            $possibles[] = self::STATUTS[$i];
        }

        return $possibles;
    }

    /**
     * Le coursier ne peut être choisi que tant que la commande est en attente.
     */
    public function peutAttribuer(Commandes $commande): bool
    {
        return $commande->statut == 'attente';
    }

    /**
     * Applique le statut (et le coursier s'il est donné) et enregistre.
     * Renvoie false, sans rien enregistrer, si le changement est interdit.
     */
    public function handle(Commandes $commande, string $statut, $idCoursier = null): bool
    {
        if ($idCoursier !== null) {
            if (! $this->peutAttribuer($commande)) {
                return false;
            }
            $commande->id_coursier = $idCoursier;
        }

        if (! in_array($statut, $this->transitionsPossibles($commande->statut))) {
            return false;
        }

        $commande->statut = $statut;
        if (in_array($statut, ['livre', 'echoue', 'annulee'])) {
            if ($commande->date_mise_encours == null) {
                $commande->date_mise_encours = now();
            }
            $commande->date_livre = now();
        }
        if ($statut == 'encours') {
            $commande->date_mise_encours = now();
        }
        $commande->save();

        return true;
    }
}
