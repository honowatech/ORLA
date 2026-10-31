<?php

namespace App\Models\Concerns;

use App\Models\Entreprise;
use App\Tenancy\EntrepriseCourante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Donnée métier propre à une entreprise.
 *
 * - Toutes les requêtes sont filtrées sur l'entreprise courante.
 * - Sans entreprise courante, une requête HTTP ne renvoie rien (fermé par
 *   défaut) ; seuls la console et EntrepriseCourante::sansFiltre() voient tout.
 * - entreprise_id est rempli automatiquement à la création.
 * - les liens vers d'autres données (config tenancy.liens) doivent viser la
 *   même entreprise : sinon l'enregistrement est refusé comme une erreur de saisie.
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

        static::saving(function (Model $model) {
            $model->verifierLiensEntreprise();
        });

        static::creating(function (Model $model) {
            $courante = app(EntrepriseCourante::class);
            if (empty($model->entreprise_id) && ! $courante->filtreDesactive()) {
                $model->entreprise_id = $courante->idOuEchec();
            }
        });
    }

    public function verifierLiensEntreprise(): void
    {
        $courante = app(EntrepriseCourante::class);
        $entrepriseId = $this->entreprise_id ?? $courante->id();
        if ($courante->filtreDesactive() || $entrepriseId === null) {
            return;
        }

        $partagees = config('tenancy.tables_partagees', []);
        $erreurs = [];

        foreach (config('tenancy.liens.'.$this->getTable(), []) as $colonne => $table) {
            $valeur = $this->getAttribute($colonne);
            if ($valeur === null || $valeur === '' || ! $this->isDirty($colonne)) {
                continue;
            }

            $existe = DB::table($table)
                ->where('id', $valeur)
                ->where(function ($q) use ($entrepriseId, $table, $partagees) {
                    $q->where('entreprise_id', $entrepriseId);
                    if (in_array($table, $partagees, true)) {
                        $q->orWhereNull('entreprise_id');
                    }
                })
                ->exists();

            if (! $existe) {
                $erreurs[$colonne] = 'La donnée sélectionnée est introuvable.';
            }
        }

        if ($erreurs) {
            throw ValidationException::withMessages($erreurs);
        }
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
