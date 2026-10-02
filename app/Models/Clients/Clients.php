<?php

namespace App\Models\Clients;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'clients';
    public $timestamps = true;
    protected $visible = array('noms', 'Prenoms', 'type_client', 'id_utilisateur', 'statut', 'telephone');

    public function type__client()
    {
        return $this->belongsTo('App\Models\TypeClient\TypeClient', 'type_client');
    }

    public function Utilisateur_lie()
    {
        return $this->belongsTo('App\Models\User', 'id_utilisateur');
    }

    public function boutiques()
    {
        return $this->hasMany('App\Models\Boutiques\Boutiques', 'id_client');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\Commandes\Commandes', 'id_client');
    }

    public function infos_perso()
    {
        return $this->hasOne('App\Models\Informations_personnels\Informations_personnels', 'id_client');
    }
    public function paiements()
    {
        return $this->hasMany('App\Models\Paiement\Paiement', 'id_client');
    }

}