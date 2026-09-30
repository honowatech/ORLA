<?php

namespace App\Models\Agents;

use App\Models\Commandes\Commandes;
use App\Models\Informations_personnels\Informations_personnels;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    protected $table = 'agents';

    public $timestamps = true;

    protected $visible = ['noms', 'prenoms', 'id_utilisateur', 'statut', 'telephone'];

    public function compte_agent()
    {
        return $this->belongsTo(Users::class, 'id_utilisateur');
    }

    public function info_perso()
    {
        return $this->hasOne(Informations_personnels::class, 'id_agent');
    }

    public function commandes()
    {
        return $this->hasMany(Commandes::class, 'id_agent');
    }
}
