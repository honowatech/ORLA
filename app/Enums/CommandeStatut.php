<?php

namespace App\Enums;

/**
 * Cycle de vie d'une commande :
 * attente -> attribue -> encours -> livre, avec annulation ou échec possibles
 * tant que la commande n'est pas terminée.
 */
enum CommandeStatut: string
{
    case Attente = 'attente';
    case Attribue = 'attribue';
    case EnCours = 'encours';
    case Livre = 'livre';
    case Annulee = 'annulee';
    case Echoue = 'echoue';
    case Supprime = 'supprime';

    /**
     * Statuts vers lesquels la commande peut passer.
     *
     * @return list<self>
     */
    public function transitionsPossibles(): array
    {
        return match ($this) {
            self::Attente => [self::Attribue, self::Annulee, self::Echoue],
            self::Attribue => [self::EnCours, self::Livre, self::Annulee, self::Echoue],
            self::EnCours => [self::Livre, self::Annulee, self::Echoue],
            default => [],
        };
    }

    public function peutPasserA(self $statut): bool
    {
        return in_array($statut, $this->transitionsPossibles(), true);
    }

    /** Livraison terminée, quelle qu'en soit l'issue. */
    public function estTermine(): bool
    {
        return in_array($this, [self::Livre, self::Annulee, self::Echoue], true);
    }

    public function libelle(): string
    {
        return match ($this) {
            self::Attente => 'En attente',
            self::Attribue => 'Attribuée',
            self::EnCours => 'En cours',
            self::Livre => 'Livrée',
            self::Annulee => 'Annulée',
            self::Echoue => 'Echouée',
            self::Supprime => 'Supprimée',
        };
    }
}
