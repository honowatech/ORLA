<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    protected $table = 'super_admin_client';

    protected $casts = [
        'date_fin' => 'datetime',
        'date_dernier_paiement' => 'datetime',
        'statut' => 'boolean',
    ];

    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'adresse', 'telephone', 'cni', 'telephone_secondaire', 'id_abonnement', 'date_fin', 'date_dernier_paiement', 'statut'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'id_client');
    }

    public function abonnement(): BelongsTo
    {
        return $this->belongsTo(Abonnement::class, 'id_abonnement');
    }
}
