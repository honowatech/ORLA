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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Users extends Model
{
    protected $table = 'users';

    public $timestamps = true;

    protected $visible = ['noms', 'email', 'password', 'id_type_utilisateur', 'statut'];

    public function type_utilisateur(): BelongsTo
    {
        return $this->belongsTo(TypeUtilisateur::class, 'id_type_utilisateur');
    }

    public function client_utilisateur(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function coursier_utilisateur(): BelongsTo
    {
        return $this->belongsTo(Coursiers::class, 'id_coursier');
    }

    public function agent_utilisateur(): BelongsTo
    {
        return $this->belongsTo(Agents::class, 'id_agent');
    }

    public function commandes_enregistrees(): HasMany
    {
        return $this->hasMany(Commandes::class, 'id_saver');
    }

    public function paiements_enregistres(): HasMany
    {
        return $this->hasMany(Paiement::class, 'id_saver');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'id_user');
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'id_ville');
    }
}
