<?php

namespace App\Models\Zone;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model 
{

    protected $table = 'zone';
    public $timestamps = true;
    protected $visible = array('libelle', 'id_ville', 'statut');

    public function details_zone()
    {
        return $this->hasMany('App\Models\Details_zone\Details_zone', 'id_zone');
    }

    public function ville()
    {
        return $this->belongsTo('App\Models\Ville\Ville', 'id_ville');
    }

    public function quartiers()
    {
        return $this->hasMany('App\Models\Quartier\Quartier', 'id_zone');
    }

    public function montantlivraisoncolis()
    {
        return $this->hasMany('App\Models\Montant_livraison\Montant_livraison', 'id_zone_colis');
    }
    public function montantlivraisonlivraison()
    {
        return $this->hasMany('App\Models\Montant_livraison\Montant_livraison', 'id_zone_livraison');
    }

}