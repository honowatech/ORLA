<?php

namespace App\Models\Informations_personnels;

use Illuminate\Database\Eloquent\Model;

class Informations_personnels extends Model
{
    protected $table = 'informations_personnels';

    public $timestamps = true;

    protected $visible = ['id_agent', 'id_client', 'id_coursier', 'telephone2', 'date_naissance', 'lieu_naissance', 'cni', 'date_delivrance', 'lieu_delivrance', 'date_expiration', 'localisation', 'id_quartier'];

    public function quartier()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier');
    }
}
