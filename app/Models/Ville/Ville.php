<?php

namespace App\Models\Ville;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Référentiel de villes partagé par toutes les entreprises (pas de cloisonnement).
 */
class Ville extends Model
{
    use HasFactory;

    protected $table = 'ville';
    public $timestamps = true;
    protected $visible = array('libelle','code');

    public function zone()
    {
        return $this->hasMany('App\Models\Zone\Zone', 'id_ville');
    }

    public function quartiers()
    {
        return $this->hasMany('App\Models\Quartier\Quartier', 'id_ville');
    }

    public function vehicules()
    {
        return $this->hasMany(\App\Models\Vehicule::class, 'id_ville');
    }
    
}