<?php

namespace App\Services\Routage;

/**
 * Un arrêt de tournée : enlèvement d'un colis ou livraison d'une commande.
 */
class Arret
{
    public const ENLEVEMENT = 'enlevement';
    public const LIVRAISON = 'livraison';

    public function __construct(
        public int $idCommande,
        public string $type,
        public int $idQuartier,
        /** Date limite de livraison (timestamp) — uniquement pour les livraisons */
        public ?int $echeance = null,
        public string $libelle = '',
    ) {
    }

    public function estEnlevement(): bool
    {
        return $this->type === self::ENLEVEMENT;
    }

    public function cle(): string
    {
        return $this->type.'#'.$this->idCommande;
    }
}
