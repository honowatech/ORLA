<?php

namespace App\Models\Paiement;

use App\Models\Clients\Clients;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $table = 'paiement';

    public $timestamps = true;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function saver(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'id_saver');
    }
}
