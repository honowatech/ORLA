<?php

namespace App\Models\Informations_personnels;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informations_personnels extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'informations_personnels';
    public $timestamps = true;
    protected $visible = array('id_agent', 'id_client', 'id_coursier', 'telephone2', 'date_naissance', 'lieu_naissance', 'cni', 'date_delivrance', 'lieu_delivrance', 'date_expiration', 'localisation', 'id_quartier');
    public function quartier()
    {
        return $this->belongsTo('App\Models\Quartier\Quartier', 'id_quartier');
    }

}