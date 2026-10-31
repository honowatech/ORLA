<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class Users extends Model 
{
    use \App\Models\Concerns\AppartientAEntreprise;

    protected static function booted(): void
    {
        static::creating(fn ($user) => static::verifierSiCreeParUnAdministrateur($user));
    }

    /**
     * Un compte créé par un utilisateur déjà connecté de l'entreprise (livreur,
     * agent, client) est considéré comme vérifié : seul le créateur d'une
     * entreprise doit confirmer son email lui-même.
     */
    public static function verifierSiCreeParUnAdministrateur($user): void
    {
        if (auth()->check() && empty($user->email_verified_at)) {
            $user->email_verified_at = now();
        }
    }


    protected $table = 'users';
    public $timestamps = true;
    protected $visible = array('noms', 'email', 'password', 'id_type_utilisateur', 'statut');

    public function type_utilisateur()
    {
        return $this->belongsTo('App\Models\typeUtilisateur\TypeUtilisateur', 'id_type_utilisateur');
    }

    public function client_utilisateur()
    {
        return $this->belongsTo('App\Models\clients\Clients', 'id_client');
    }

    public function coursier_utilisateur()
    {
        return $this->belongsTo('App\Models\coursiers\Coursiers', 'id_coursier');
    }

    public function agent_utilisateur()
    {
        return $this->belongsTo('App\Models\agents\Agents', 'id_agent');
    }

    public function commandes_enregistrees()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_saver');
    }
    public function paiements_enregistres()
    {
        return $this->hasMany('App\Models\paiement\Paiement', 'id_saver');
    }
    public function activities()
    {
        return $this->hasMany('App\Models\activity', 'id_user');
    }
    public function ville()
    {
        return $this->belongsTo('App\Models\ville\Ville', 'id_ville');
    }

}