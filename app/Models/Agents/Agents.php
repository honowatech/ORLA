<?php

namespace App\Models\Agents;

use Illuminate\Database\Eloquent\Model;

class Agents extends Model 
{

    protected $table = 'agents';
    public $timestamps = true;
    protected $visible = array('noms', 'prenoms', 'id_utilisateur', 'statut', 'telephone');

    public function compte_agent()
    {
        return $this->belongsTo('App\Models\User', 'id_utilisateur');
    }

    public function info_perso()
    {
        return $this->hasOne('App\Models\Informations_personnels\Informations_personnels', 'id_agent');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\Commandes\Commandes', 'id_agent');
    }
}