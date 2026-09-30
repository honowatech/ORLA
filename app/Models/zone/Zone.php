<?php

namespace App\Models\Zone;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'zone';

    public $timestamps = true;

    protected $visible = ['libelle', 'id_ville', 'statut'];

    public function details_zone()
    {
        return $this->hasMany('App\Models\details_zone\Details_zone', 'id_zone');
    }

    public function ville()
    {
        return $this->belongsTo('App\Models\ville\Ville', 'id_ville');
    }

    public function quartiers()
    {
        return $this->hasMany('App\Models\quartier\Quartier', 'id_zone');
    }

    public function montantlivraisoncolis()
    {
        return $this->hasMany('App\Models\montant_livraison\Montant_livraison', 'id_zone_colis');
    }

    public function montantlivraisonlivraison()
    {
        return $this->hasMany('App\Models\montant_livraison\Montant_livraison', 'id_zone_livraison');
    }
}
