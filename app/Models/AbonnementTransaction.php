<?php

namespace App\Models;

use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Info_transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Transaction d'abonnement d'une entreprise (ancienne table super_admin_transaction).
 * Statuts : waiting, success, failed, cancelled. Méthodes : mobile, bank, application.
 */
class AbonnementTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'abonnement_transactions';

    protected $fillable = [
        'entreprise_id',
        'id_client',
        'id_abonnement',
        'methode',
        'montant',
        'nbre_abonnement',
        'date_debut',
        'date_fin',
        'statut',
        'reference',
        'payment_id',
        'payload',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'payload' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (AbonnementTransaction $transaction) {
            $transaction->reference ??= (string) Str::uuid();
            // Colonne historique conservée jusqu'au nettoyage du schéma.
            $transaction->id_client ??= $transaction->entreprise_id;
        });
    }

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Alias historique utilisé par les vues de la console Super Admin.
     */
    public function client(): BelongsTo
    {
        return $this->entreprise();
    }

    public function abonnement(): BelongsTo
    {
        return $this->belongsTo(Abonnement::class, 'id_abonnement');
    }

    public function infos(): HasMany
    {
        return $this->hasMany(Info_transaction::class, 'id_transaction');
    }
}
