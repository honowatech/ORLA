<?php

namespace App\Models\Quartier;

use App\Models\Boutiques\Boutiques;
use App\Models\Commandes\Commandes;
use App\Models\Point_relais\Point_relais;
use App\Models\Ville\Ville;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Model;

class Quartier extends Model
{
    protected $table = 'quartier';

    public $timestamps = true;

    protected $visible = ['id_zone', 'libelle', 'id_ville'];

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'id_zone');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class, 'id_ville');
    }

    public function boutiques()
    {
        return $this->hasMany(Boutiques::class, 'id_quartier');
    }

    public function point_relais()
    {
        return $this->hasMany(Point_relais::class, 'id_quartier');
    }

    public function commandes_pointdepart()
    {
        return $this->hasMany(Commandes::class, 'id_quartier_colis');
    }

    public function commandes_pointarrivee()
    {
        return $this->hasMany(Commandes::class, 'id_quartier_livraison');
    }
}
