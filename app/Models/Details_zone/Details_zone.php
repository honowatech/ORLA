<?php

namespace App\Models\Details_zone;

use App\Models\Coursiers\Coursiers;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Model;

class Details_zone extends Model
{
    protected $table = 'details_zone';

    public $timestamps = true;

    protected $visible = ['id_coursier', 'id_zone'];

    public function livreur()
    {
        return $this->belongsTo(Coursiers::class, 'id_coursier');
    }

    public function zone_affectee()
    {
        return $this->belongsTo(Zone::class, 'id_zone');
    }
}
