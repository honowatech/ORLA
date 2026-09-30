<?php

namespace App\Models\Informations_personnels;

use App\Models\Quartier\Quartier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Informations_personnels extends Model
{
    protected $table = 'informations_personnels';

    public $timestamps = true;

    protected $visible = ['id_agent', 'id_client', 'id_coursier', 'telephone2', 'date_naissance', 'lieu_naissance', 'cni', 'date_delivrance', 'lieu_delivrance', 'date_expiration', 'localisation', 'id_quartier'];

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class, 'id_quartier');
    }
}
