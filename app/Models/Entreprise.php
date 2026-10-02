<?php

namespace App\Models;

use App\Models\Quartier\Quartier;
use App\Models\SuperAdmin\Abonnement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Entreprise de livraison hébergée sur la plateforme (tenant).
 *
 * Remplace l'ancienne « licence d'instance » super_admin_client. Le champ
 * `statut` (booléen : actif / suspendu) est la seule décision du Super Admin ;
 * l'état d'abonnement (essai, actif, grâce, expiré) est calculé à chaque requête
 * par App\Services\Abonnement\AbonnementService.
 */
class Entreprise extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'entreprises';

    /**
     * `statut` est volontairement absent : il se modifie par suspendre() / activer().
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'telephone',
        'telephone_secondaire',
        'adresse',
        'cni',
        'logo_path',
        'pays',
        'devise',
        'prefixe_telephone',
        'essai_fin',
        'id_abonnement',
        'date_fin',
        'date_dernier_paiement',
        'id_quartier_siege',
        'tarif_defaut',
        'parametres',
    ];

    protected $casts = [
        'statut' => 'boolean',
        'essai_fin' => 'datetime',
        'date_fin' => 'datetime',
        'date_dernier_paiement' => 'datetime',
        'tarif_defaut' => 'decimal:2',
        'parametres' => 'array',
    ];

    protected $attributes = [
        'statut' => true,
    ];

    protected static function booted(): void
    {
        static::saving(function (Entreprise $entreprise) {
            if (blank($entreprise->slug)) {
                $entreprise->slug = static::slugUnique($entreprise->name ?? 'entreprise', $entreprise->getKey());
            }
        });
    }

    public function abonnement(): BelongsTo
    {
        return $this->belongsTo(Abonnement::class, 'id_abonnement');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AbonnementTransaction::class, 'entreprise_id');
    }

    public function utilisateurs(): HasMany
    {
        return $this->hasMany(User::class, 'entreprise_id');
    }

    public function quartierSiege(): BelongsTo
    {
        return $this->belongsTo(Quartier::class, 'id_quartier_siege');
    }

    public function estSuspendue(): bool
    {
        return ! $this->statut;
    }

    public function suspendre(): void
    {
        $this->forceFill(['statut' => false])->save();
    }

    public function activer(): void
    {
        $this->forceFill(['statut' => true])->save();
    }

    /**
     * Génère un slug unique à partir d'un nom, en ignorant éventuellement un enregistrement.
     */
    public static function slugUnique(string $nom, int|string|null $ignorerId = null): string
    {
        $base = Str::slug($nom) ?: 'entreprise';
        $slug = $base;
        $suffixe = 2;

        while (static::withTrashed()->where('slug', $slug)->when($ignorerId, fn ($q) => $q->whereKeyNot($ignorerId))->exists()) {
            $slug = "{$base}-{$suffixe}";
            $suffixe++;
        }

        return $slug;
    }
}
