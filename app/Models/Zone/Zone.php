<?php

namespace App\Models\Zone;

use App\Models\Details_zone\Details_zone;
use App\Models\Montant_livraison\Montant_livraison;
use App\Models\Quartier\Quartier;
use App\Models\Ville\Ville;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'zone';

    public $timestamps = true;

    protected $visible = ['libelle', 'id_ville', 'statut'];

    public function details_zone()
    {
        return $this->hasMany(Details_zone::class, 'id_zone');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class, 'id_ville');
    }

    public function quartiers()
    {
        return $this->hasMany(Quartier::class, 'id_zone');
    }

    public function montantlivraisoncolis()
    {
        return $this->hasMany(Montant_livraison::class, 'id_zone_colis');
    }

    public function montantlivraisonlivraison()
    {
        return $this->hasMany(Montant_livraison::class, 'id_zone_livraison');
    }
}
