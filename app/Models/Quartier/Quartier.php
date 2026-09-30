<?php

namespace App\Models\Quartier;

use App\Models\Boutiques\Boutiques;
use App\Models\Commandes\Commandes;
use App\Models\Point_relais\Point_relais;
use App\Models\Ville\Ville;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quartier extends Model
{
    protected $table = 'quartier';

    public $timestamps = true;

    protected $visible = ['id_zone', 'libelle', 'id_ville'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'id_zone');
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'id_ville');
    }

    public function boutiques(): HasMany
    {
        return $this->hasMany(Boutiques::class, 'id_quartier');
    }

    public function point_relais(): HasMany
    {
        return $this->hasMany(Point_relais::class, 'id_quartier');
    }

    public function commandes_pointdepart(): HasMany
    {
        return $this->hasMany(Commandes::class, 'id_quartier_colis');
    }

    public function commandes_pointarrivee(): HasMany
    {
        return $this->hasMany(Commandes::class, 'id_quartier_livraison');
    }
}
