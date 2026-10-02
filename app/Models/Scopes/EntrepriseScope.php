<?php

namespace App\Models\Scopes;

use App\Tenancy\CurrentEntreprise;
use App\Tenancy\Exceptions\TenantNonResoluException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Restreint toute requête d'un modèle cloisonné à l'entreprise courante.
 *
 * - bypass actif (Super Admin, maintenance) : aucun filtre ;
 * - aucune entreprise courante : exception pour un modèle strict, aucun filtre sinon
 *   (User est non strict car le fournisseur d'authentification le charge avant la
 *   résolution du tenant) ;
 * - entreprise courante : WHERE <table>.entreprise_id = <id>.
 */
class EntrepriseScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $courante = app(CurrentEntreprise::class);

        if ($courante->isBypassed()) {
            return;
        }

        if (! $courante->has()) {
            if ($model::tenancyIsStrict() && config('saas.tenancy_strict', true)) {
                throw new TenantNonResoluException($model::class);
            }

            return;
        }

        $builder->where($model->qualifyColumn('entreprise_id'), $courante->id());
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenancy', fn (Builder $builder) => $builder->withoutGlobalScope($this));
    }
}
