<?php

namespace App\Models\Details_zone;

use App\Models\Coursiers\Coursiers;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Details_zone extends Model
{
    protected $table = 'details_zone';

    public $timestamps = true;

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(Coursiers::class, 'id_coursier');
    }

    public function zone_affectee(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'id_zone');
    }
}
