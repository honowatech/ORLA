<?php

namespace App\Models\Montant_livraison;

use App\Models\Commandes\Commandes;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Montant_livraison extends Model
{
    protected $table = 'montant_livraison';

    public $timestamps = true;

    protected $fillable = ['id_zone_colis'];

    protected $visible = ['id_zone_livraison', 'montant'];

    public function commandes(): HasMany
    {
        return $this->hasMany(Commandes::class, 'id_montant_livraison');
    }

    public function zone_depart(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'id_zone_colis');
    }

    public function zone_arrivee(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'id_zone_livraison');
    }
}
