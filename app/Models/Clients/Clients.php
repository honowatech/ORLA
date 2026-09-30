<?php

namespace App\Models\Clients;

use App\Models\Boutiques\Boutiques;
use App\Models\Commandes\Commandes;
use App\Models\Informations_personnels\Informations_personnels;
use App\Models\Paiement\Paiement;
use App\Models\TypeClient\TypeClient;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Clients extends Model
{
    protected $table = 'clients';

    public $timestamps = true;

    protected $visible = ['noms', 'Prenoms', 'type_client', 'id_utilisateur', 'statut', 'telephone'];

    public function type__client(): BelongsTo
    {
        return $this->belongsTo(TypeClient::class, 'type_client');
    }

    public function Utilisateur_lie(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'id_utilisateur');
    }

    public function boutiques(): HasMany
    {
        return $this->hasMany(Boutiques::class, 'id_client');
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commandes::class, 'id_client');
    }

    public function infos_perso(): HasOne
    {
        return $this->hasOne(Informations_personnels::class, 'id_client');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'id_client');
    }
}
