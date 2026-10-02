<?php

namespace App\Tenancy\Exceptions;

use RuntimeException;

/**
 * Levée lorsqu'un modèle cloisonné est interrogé ou créé sans entreprise courante
 * (hors contexte Super Admin). Signale une erreur de programmation, pas une erreur
 * utilisateur : utiliser CurrentEntreprise::runAs() ou runWithoutTenancy().
 */
class TenantNonResoluException extends RuntimeException
{
    public function __construct(string $contexte)
    {
        parent::__construct(
            "Aucune entreprise courante n'est définie pour « {$contexte} ». "
            .'Résolvez le tenant (middleware « entreprise ») ou utilisez CurrentEntreprise::runAs() / runWithoutTenancy().'
        );
    }
}
