<?php

namespace App\Models\Coursiers;

use Illuminate\Database\Eloquent\Model;

class Coursiers extends Model 
{

    protected $table = 'coursiers';
    public $timestamps = true;
    protected $visible = array('noms', 'prenoms', 'id_utilisateur', 'statut', 'telephone');

    public function coursier_utilisateur()
    {
        return $this->belongsTo('App\Models\User', 'id_utilisateur');
    }

    public function zone_coursier()
    {
        return $this->hasMany('App\Models\Details_zone\Details_zone', 'id_coursier');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_utilisateur');
    }


    public function vehicules()
    {
        return $this->hasMany('App\Models\Vehicule', 'id_coursier');
    }
    
    public function point_relais()
    {
        return $this->hasMany('App\Models\Point_relais\Point_relais', 'id_coursier');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\Commandes\Commandes', 'id_coursier');
    }

    public function infos_perso()
    {
        return $this->hasOne('App\Models\Informations_personnels\Informations_personnels', 'id_coursier');
    }

}