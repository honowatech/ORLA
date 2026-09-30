<?php

namespace App\Models\Zone;

use App\Models\Details_zone\Details_zone;
use App\Models\Montant_livraison\Montant_livraison;
use App\Models\Quartier\Quartier;
use App\Models\Ville\Ville;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $table = 'zone';

    public $timestamps = true;

    protected $visible = ['libelle', 'id_ville', 'statut'];

    public function details_zone(): HasMany
    {
        return $this->hasMany(Details_zone::class, 'id_zone');
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'id_ville');
    }

    public function quartiers(): HasMany
    {
        return $this->hasMany(Quartier::class, 'id_zone');
    }

    public function montantlivraisoncolis(): HasMany
    {
        return $this->hasMany(Montant_livraison::class, 'id_zone_colis');
    }

    public function montantlivraisonlivraison(): HasMany
    {
        return $this->hasMany(Montant_livraison::class, 'id_zone_livraison');
    }
}
