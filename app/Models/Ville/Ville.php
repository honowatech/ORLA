<?php

namespace App\Models\Ville;

use App\Models\Quartier\Quartier;
use App\Models\Vehicule;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Model;

class Ville extends Model
{
    protected $table = 'ville';

    public $timestamps = true;

    protected $visible = ['libelle', 'code'];

    public function zone()
    {
        return $this->hasMany(Zone::class, 'id_ville');
    }

    public function quartiers()
    {
        return $this->hasMany(Quartier::class, 'id_ville');
    }

    public function vehicules()
    {
        return $this->hasMany(Vehicule::class, 'id_ville');
    }
}
