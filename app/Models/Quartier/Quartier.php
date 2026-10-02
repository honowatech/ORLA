<?php

namespace App\Models\Quartier;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quartier extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'quartier';
    public $timestamps = true;
    protected $visible = array('id_zone','libelle','id_ville');

    public function zone()
    {
        return $this->belongsTo('App\Models\Zone\Zone', 'id_zone');
    }
    public function ville()
    {
        return $this->belongsTo('App\Models\Ville\Ville', 'id_ville');
    }
    public function boutiques()
    {
        return $this->hasMany('App\Models\Boutiques\Boutiques', 'id_quartier');
    }

    public function point_relais()
    {
        return $this->hasMany('App\Models\Point_relais\Point_relais', 'id_quartier');
    }

    public function commandes_pointdepart()
    {
        return $this->hasMany('App\Models\Commandes\Commandes', 'id_quartier_colis');
    }

    public function commandes_pointarrivee()
    {
        return $this->hasMany('App\Models\Commandes\Commandes', 'id_quartier_livraison');
    }

}