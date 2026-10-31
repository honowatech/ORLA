<?php

namespace App\Models\Concerns;

use App\Models\Entreprise;
use App\Tenancy\EntrepriseCourante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Donnée métier propre à une entreprise.
 *
 * - Toutes les requêtes sont filtrées sur l'entreprise courante.
 * - Sans entreprise courante, une requête HTTP ne renvoie rien (fermé par
 *   défaut) ; seuls la console et EntrepriseCourante::sansFiltre() voient tout.
 * - entreprise_id est rempli automatiquement à la création.
 */
trait AppartientAEntreprise
{
    public static function bootAppartientAEntreprise(): void
    {
        static::addGlobalScope('entreprise', function (Builder $query) {
            $model = $query->getModel();
            $courante = app(EntrepriseCourante::class);

            if ($courante->filtreDesactive()) {
                return;
            }

            if ($id = $courante->id()) {
                $model->appliquerFiltreEntreprise($query, $id);
            } elseif ($model->filtreStrictSansEntreprise() && ! app()->runningInConsole()) {
                $query->whereRaw('1 = 0');
            }
        });

        static::creating(function (Model $model) {
            $courante = app(EntrepriseCourante::class);
            if (empty($model->entreprise_id) && ! $courante->filtreDesactive()) {
                $model->entreprise_id = $courante->idOuEchec();
            }
        });
    }

    protected function appliquerFiltreEntreprise(Builder $query, int $id): void
    {
        $query->where($this->qualifyColumn('entreprise_id'), $id);
    }

    /**
     * Sans entreprise courante, ne rien renvoyer plutôt que tout renvoyer.
     */
    public function filtreStrictSansEntreprise(): bool
    {
        return true;
    }

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }
}
