<?php

namespace App\Models\Ville;

use App\Models\Quartier\Quartier;
use App\Models\Vehicule;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ville extends Model
{
    protected $table = 'ville';

    public $timestamps = true;

    protected $visible = ['libelle', 'code'];

    public function zone(): HasMany
    {
        return $this->hasMany(Zone::class, 'id_ville');
    }

    public function quartiers(): HasMany
    {
        return $this->hasMany(Quartier::class, 'id_ville');
    }

    public function vehicules(): HasMany
    {
        return $this->hasMany(Vehicule::class, 'id_ville');
    }
}
