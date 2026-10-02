<?php

namespace App\Enums;

/**
 * État d'abonnement d'une entreprise, calculé à chaque requête (aucun cron
 * n'est disponible sur l'hébergement cible).
 */
enum EtatAbonnement: string
{
    case Suspendue = 'suspendue';
    case Essai = 'essai';
    case Active = 'active';
    case Grace = 'grace';
    case Expiree = 'expiree';

    public function permetAcces(): bool
    {
        return match ($this) {
            self::Essai, self::Active, self::Grace => true,
            self::Suspendue, self::Expiree => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Suspendue => 'Espace suspendu',
            self::Essai => 'Période d\'essai',
            self::Active => 'Abonnement actif',
            self::Grace => 'Période de grâce',
            self::Expiree => 'Abonnement expiré',
        };
    }
}
