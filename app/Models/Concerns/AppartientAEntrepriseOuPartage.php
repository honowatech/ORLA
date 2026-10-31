<?php

namespace App\Models\Concerns;

use App\Tenancy\EntrepriseCourante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Référentiel partagé (villes, types de véhicule) : une ligne sans
 * entreprise_id est commune à toutes les entreprises et en lecture seule pour
 * elles ; chaque entreprise peut ajouter ses propres lignes.
 */
trait AppartientAEntrepriseOuPartage
{
    use AppartientAEntreprise;

    public static function bootAppartientAEntrepriseOuPartage(): void
    {
        $proteger = function (Model $model) {
            $courante = app(EntrepriseCourante::class);
            if ($courante->estDefinie() && ! $courante->filtreDesactive() && $model->getOriginal('entreprise_id') === null) {
                throw new RuntimeException('Cette donnée est commune à toutes les entreprises et ne peut pas être modifiée.');
            }
        };

        static::updating($proteger);
        static::deleting($proteger);
    }

    protected function appliquerFiltreEntreprise(Builder $query, int $id): void
    {
        $colonne = $this->qualifyColumn('entreprise_id');
        $query->where(fn (Builder $q) => $q->where($colonne, $id)->orWhereNull($colonne));
    }

    public function estPartage(): bool
    {
        return $this->entreprise_id === null;
    }
}
