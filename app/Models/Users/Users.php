<?php

namespace App\Models\Users;

use App\Models\Activity;
use App\Models\Agents\Agents;
use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Coursiers\Coursiers;
use App\Models\Paiement\Paiement;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use App\Models\Ville\Ville;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';

    public $timestamps = true;

    protected $visible = ['noms', 'email', 'password', 'id_type_utilisateur', 'statut'];

    public function type_utilisateur()
    {
        return $this->belongsTo(TypeUtilisateur::class, 'id_type_utilisateur');
    }

    public function client_utilisateur()
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function coursier_utilisateur()
    {
        return $this->belongsTo(Coursiers::class, 'id_coursier');
    }

    public function agent_utilisateur()
    {
        return $this->belongsTo(Agents::class, 'id_agent');
    }

    public function commandes_enregistrees()
    {
        return $this->hasMany(Commandes::class, 'id_saver');
    }

    public function paiements_enregistres()
    {
        return $this->hasMany(Paiement::class, 'id_saver');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'id_user');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class, 'id_ville');
    }
}
