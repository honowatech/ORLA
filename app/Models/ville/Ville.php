<?php

namespace App\Models\Ville;

use Illuminate\Database\Eloquent\Model;

class Ville extends Model 
{

    protected $table = 'ville';
    public $timestamps = true;
    protected $visible = array('libelle','code');

    public function zone()
    {
        return $this->hasMany('App\Models\zone\Zone', 'id_ville');
    }

    public function quartiers()
    {
        return $this->hasMany('App\Models\quartier\Quartier', 'id_ville');
    }

    public function vehicules()
    {
        return $this->hasMany('App\Models\Vehicule', 'id_ville');
    }
    
}