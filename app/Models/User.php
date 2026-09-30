<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Agents\Agents;
use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Coursiers\Coursiers;
use App\Models\Paiement\Paiement;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use App\Models\Ville\Ville;
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
        'name',
        'email',
        'password',
        'noms',
        'id_type_utilisateur',
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
        return $this->belongsTo(TypeUtilisateur::class, 'id_type_utilisateur');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class, 'id_ville');
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
}
