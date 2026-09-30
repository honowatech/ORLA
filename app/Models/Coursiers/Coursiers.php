<?php

namespace App\Models\Coursiers;

use App\Models\Commandes\Commandes;
use App\Models\Details_zone\Details_zone;
use App\Models\Informations_personnels\Informations_personnels;
use App\Models\Point_relais\Point_relais;
use App\Models\Users\Users;
use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Model;

class Coursiers extends Model
{
    protected $table = 'coursiers';

    public $timestamps = true;

    protected $visible = ['noms', 'prenoms', 'id_utilisateur', 'statut', 'telephone'];

    public function coursier_utilisateur()
    {
        return $this->belongsTo(Users::class, 'id_utilisateur');
    }

    public function zone_coursier()
    {
        return $this->hasMany(Details_zone::class, 'id_coursier');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'id_utilisateur');
    }

    public function vehicules()
    {
        return $this->hasMany(Vehicule::class, 'id_coursier');
    }

    public function point_relais()
    {
        return $this->hasMany(Point_relais::class, 'id_coursier');
    }

    public function commandes()
    {
        return $this->hasMany(Commandes::class, 'id_coursier');
    }

    public function infos_perso()
    {
        return $this->hasOne(Informations_personnels::class, 'id_coursier');
    }
}
