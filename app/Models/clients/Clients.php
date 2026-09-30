<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $table = 'clients';

    public $timestamps = true;

    protected $visible = ['noms', 'Prenoms', 'type_client', 'id_utilisateur', 'statut', 'telephone'];

    public function type__client()
    {
        return $this->belongsTo('App\Models\typeClient\TypeClient', 'type_client');
    }

    public function Utilisateur_lie()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_utilisateur');
    }

    public function boutiques()
    {
        return $this->hasMany('App\Models\boutiques\Boutiques', 'id_client');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_client');
    }

    public function infos_perso()
    {
        return $this->hasOne('App\Models\informations_personnels\Informations_personnels', 'id_client');
    }

    public function paiements()
    {
        return $this->hasMany('App\Models\paiement\Paiement', 'id_client');
    }
}
