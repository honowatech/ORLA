<?php

namespace App\Models\Details_zone;

use Illuminate\Database\Eloquent\Model;

class Details_zone extends Model 
{

    protected $table = 'details_zone';
    public $timestamps = true;
    protected $visible = array('id_coursier', 'id_zone');

    public function livreur()
    {
        return $this->belongsTo('App\Models\coursiers\Coursiers', 'id_coursier');
    }

    public function zone_affectee()
    {
        return $this->belongsTo('App\Models\zone\Zone', 'id_zone');
    }

}