<?php

namespace App\Tenancy;

use App\Models\Entreprise;
use RuntimeException;

/**
 * Entreprise (tenant) de la requête en cours.
 *
 * Renseignée par le middleware IdentifierEntreprise à partir de l'utilisateur
 * connecté, puis lue par le trait AppartientAEntreprise pour filtrer toutes
 * les requêtes Eloquent.
 */
class EntrepriseCourante
{
    protected ?int $id = null;

    protected ?Entreprise $entreprise = null;

    protected int $sansFiltre = 0;

    public function definir(Entreprise|int|null $entreprise): void
    {
        if ($entreprise instanceof Entreprise) {
            $this->entreprise = $entreprise;
            $this->id = $entreprise->getKey();
        } else {
            $this->entreprise = null;
            $this->id = $entreprise;
        }
    }

    public function oublier(): void
    {
        $this->definir(null);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function idOuEchec(): int
    {
        return $this->id ?? throw new RuntimeException("Aucune entreprise courante : impossible d'enregistrer une donnée métier.");
    }

    public function entreprise(): ?Entreprise
    {
        if ($this->entreprise === null && $this->id !== null) {
            $this->entreprise = Entreprise::find($this->id);
        }

        return $this->entreprise;
    }

    public function estDefinie(): bool
    {
        return $this->id !== null;
    }

    /**
     * Exécute $callback avec une entreprise donnée, puis restaure le contexte.
     * À utiliser pour les tâches planifiées, files d'attente et webhooks.
     */
    public function pour(Entreprise|int $entreprise, callable $callback): mixed
    {
        [$id, $instance] = [$this->id, $this->entreprise];
        $this->definir($entreprise);

        try {
            return $callback();
        } finally {
            [$this->id, $this->entreprise] = [$id, $instance];
        }
    }

    /**
     * Exécute $callback sans filtre d'entreprise (Super Admin, maintenance).
     */
    public function sansFiltre(callable $callback): mixed
    {
        $this->sansFiltre++;

        try {
            return $callback();
        } finally {
            $this->sansFiltre--;
        }
    }

    public function filtreDesactive(): bool
    {
        return $this->sansFiltre > 0;
    }
}
