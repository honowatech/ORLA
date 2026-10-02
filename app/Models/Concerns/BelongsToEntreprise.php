<?php

namespace App\Models\Concerns;

use App\Models\Entreprise;
use App\Models\Scopes\EntrepriseScope;
use App\Tenancy\CurrentEntreprise;
use App\Tenancy\Exceptions\TenantNonResoluException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * À appliquer à tout modèle dont la table porte une colonne entreprise_id.
 *
 * - lectures filtrées par EntrepriseScope ;
 * - entreprise_id renseigné automatiquement à la création depuis l'entreprise courante ;
 * - un modèle peut déclarer `protected static bool $tenancyStrict = false;` pour
 *   tolérer l'absence d'entreprise courante en lecture (cas de User).
 *
 * entreprise_id ne doit jamais figurer dans $fillable : il est déduit du contexte.
 */
trait BelongsToEntreprise
{
    public static function bootBelongsToEntreprise(): void
    {
        static::addGlobalScope(new EntrepriseScope);

        static::creating(function (Model $modele) {
            if ($modele->getAttribute('entreprise_id') !== null) {
                return;
            }

            $courante = app(CurrentEntreprise::class);

            if ($courante->has()) {
                $modele->setAttribute('entreprise_id', $courante->id());

                return;
            }

            if (config('saas.tenancy_strict', true)) {
                throw new TenantNonResoluException(static::class.' (création)');
            }
        });
    }

    public static function tenancyIsStrict(): bool
    {
        return property_exists(static::class, 'tenancyStrict') ? static::$tenancyStrict : true;
    }

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Vrai si l'enregistrement appartient à l'entreprise courante.
     */
    public function appartientAEntrepriseCourante(): bool
    {
        $id = app(CurrentEntreprise::class)->id();

        return $id !== null && (int) $this->getAttribute('entreprise_id') === $id;
    }
}
