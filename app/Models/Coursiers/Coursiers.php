<?php

namespace App\Models\Coursiers;

use App\Models\Commandes\Commandes;
use App\Models\Details_zone\Details_zone;
use App\Models\Informations_personnels\Informations_personnels;
use App\Models\Point_relais\Point_relais;
use App\Models\Users\Users;
use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Coursiers extends Model
{
    protected $table = 'coursiers';

    public $timestamps = true;

    protected $visible = ['noms', 'prenoms', 'id_utilisateur', 'statut', 'telephone'];

    public function coursier_utilisateur(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'id_utilisateur');
    }

    public function zone_coursier(): HasMany
    {
        return $this->hasMany(Details_zone::class, 'id_coursier');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'id_utilisateur');
    }

    public function vehicules(): HasMany
    {
        return $this->hasMany(Vehicule::class, 'id_coursier');
    }

    public function point_relais(): HasMany
    {
        return $this->hasMany(Point_relais::class, 'id_coursier');
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commandes::class, 'id_coursier');
    }

    public function infos_perso(): HasOne
    {
        return $this->hasOne(Informations_personnels::class, 'id_coursier');
    }
}
