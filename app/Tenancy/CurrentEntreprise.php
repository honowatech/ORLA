<?php

namespace App\Tenancy;

use App\Models\Entreprise;
use Closure;

/**
 * Entreprise courante de la requête (ou du contexte console).
 *
 * Enregistrée en singleton : résolue par le middleware SetCurrentEntreprise à
 * partir de l'utilisateur connecté, lue par le scope global EntrepriseScope.
 */
class CurrentEntreprise
{
    private ?Entreprise $entreprise = null;

    private bool $bypass = false;

    public function set(Entreprise $entreprise): void
    {
        $this->entreprise = $entreprise;
    }

    public function get(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function id(): ?int
    {
        return $this->entreprise?->getKey();
    }

    public function has(): bool
    {
        return $this->entreprise !== null;
    }

    public function forget(): void
    {
        $this->entreprise = null;
    }

    /**
     * Désactive le cloisonnement (console Super Admin, maintenance).
     */
    public function bypass(bool $bypass = true): void
    {
        $this->bypass = $bypass;
    }

    public function isBypassed(): bool
    {
        return $this->bypass;
    }

    /**
     * Exécute un traitement dans le contexte d'une entreprise donnée, puis
     * restaure l'état précédent (seeders, console, actions Super Admin).
     */
    public function runAs(Entreprise $entreprise, Closure $callback): mixed
    {
        [$precedente, $bypassPrecedent] = [$this->entreprise, $this->bypass];
        $this->entreprise = $entreprise;
        $this->bypass = false;

        try {
            return $callback($entreprise);
        } finally {
            $this->entreprise = $precedente;
            $this->bypass = $bypassPrecedent;
        }
    }

    /**
     * Exécute un traitement sans aucun cloisonnement, puis restaure l'état précédent.
     */
    public function runWithoutTenancy(Closure $callback): mixed
    {
        [$precedente, $bypassPrecedent] = [$this->entreprise, $this->bypass];
        $this->entreprise = null;
        $this->bypass = true;

        try {
            return $callback();
        } finally {
            $this->entreprise = $precedente;
            $this->bypass = $bypassPrecedent;
        }
    }
}
