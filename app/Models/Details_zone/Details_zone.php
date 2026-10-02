<?php

namespace App\Models\Details_zone;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Details_zone extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'details_zone';
    public $timestamps = true;
    protected $visible = array('id_coursier', 'id_zone');

    public function livreur()
    {
        return $this->belongsTo('App\Models\Coursiers\Coursiers', 'id_coursier');
    }

    public function zone_affectee()
    {
        return $this->belongsTo('App\Models\Zone\Zone', 'id_zone');
    }

}