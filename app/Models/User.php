<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'noms',
        'email',
        'password',
        'telephone',
        'id_type_utilisateur',
        'id_agent',
        'id_coursier',
        'id_client',
        'id_ville',
        'statut',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function type_utilisateur()
    {
        return $this->belongsTo('App\Models\TypeUtilisateur\TypeUtilisateur', 'id_type_utilisateur');
    }

    public function ville()
    {
        return $this->belongsTo('App\Models\Ville\Ville', 'id_ville');
    }

    public function client_utilisateur()
    {
        return $this->belongsTo('App\Models\Clients\Clients', 'id_client');
    }

    public function coursier_utilisateur()
    {
        return $this->belongsTo('App\Models\Coursiers\Coursiers', 'id_coursier');
    }

    public function agent_utilisateur()
    {
        return $this->belongsTo('App\Models\Agents\Agents', 'id_agent');
    }

    public function commandes_enregistrees()
    {
        return $this->hasMany('App\Models\Commandes\Commandes', 'id_saver');
    }

    public function paiements_enregistres()
    {
        return $this->hasMany('App\Models\Paiement\Paiement', 'id_saver');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'id_user');
    }
}
