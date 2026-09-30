<?php

namespace App\Models\Quartier;

use Illuminate\Database\Eloquent\Model;

class Quartier extends Model
{
    protected $table = 'quartier';

    public $timestamps = true;

    protected $visible = ['id_zone', 'libelle', 'id_ville'];

    public function zone()
    {
        return $this->belongsTo('App\Models\zone\Zone', 'id_zone');
    }

    public function ville()
    {
        return $this->belongsTo('App\Models\ville\Ville', 'id_ville');
    }

    public function boutiques()
    {
        return $this->hasMany('App\Models\boutiques\Boutiques', 'id_quartier');
    }

    public function point_relais()
    {
        return $this->hasMany('App\Models\point_relais\Point_relais', 'id_quartier');
    }

    public function commandes_pointdepart()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_quartier_colis');
    }

    public function commandes_pointarrivee()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_quartier_livraison');
    }
}
