<?php

namespace App\Models\Coursiers;

use Illuminate\Database\Eloquent\Model;

class Coursiers extends Model
{
    protected $table = 'coursiers';

    public $timestamps = true;

    protected $visible = ['noms', 'prenoms', 'id_utilisateur', 'statut', 'telephone'];

    public function coursier_utilisateur()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_utilisateur');
    }

    public function zone_coursier()
    {
        return $this->hasMany('App\Models\details_zone\Details_zone', 'id_coursier');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_utilisateur');
    }

    public function vehicules()
    {
        return $this->hasMany('App\Models\Vehicule', 'id_coursier');
    }

    public function point_relais()
    {
        return $this->hasMany('App\Models\point_relais\Point_relais', 'id_coursier');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_coursier');
    }

    public function infos_perso()
    {
        return $this->hasOne('App\Models\informations_personnels\Informations_personnels', 'id_coursier');
    }
}
