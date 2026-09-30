<?php

namespace App\Models\Agents;

use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    protected $table = 'agents';

    public $timestamps = true;

    protected $visible = ['noms', 'prenoms', 'id_utilisateur', 'statut', 'telephone'];

    public function compte_agent()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_utilisateur');
    }

    public function info_perso()
    {
        return $this->hasOne('App\Models\informations_personnels\Informations_personnels', 'id_agent');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_agent');
    }
}
