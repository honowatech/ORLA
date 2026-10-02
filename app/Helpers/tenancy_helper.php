<?php

use App\Models\Entreprise;
use App\Tenancy\CurrentEntreprise;
use App\Tenancy\Exceptions\TenantNonResoluException;

if (! function_exists('entreprise')) {
    /**
     * Entreprise courante de la requête, ou null hors contexte tenant.
     */
    function entreprise(): ?Entreprise
    {
        return app(CurrentEntreprise::class)->get();
    }
}

if (! function_exists('entreprise_id')) {
    /**
     * Identifiant de l'entreprise courante ; lève une exception s'il n'y en a pas.
     */
    function entreprise_id(): int
    {
        return app(CurrentEntreprise::class)->id()
            ?? throw new TenantNonResoluException('entreprise_id()');
    }
}
