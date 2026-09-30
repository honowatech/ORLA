<?php

namespace App\Models\Informations_personnels;

use App\Models\Quartier\Quartier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Informations_personnels extends Model
{
    protected $table = 'informations_personnels';

    public $timestamps = true;

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class, 'id_quartier');
    }
}
