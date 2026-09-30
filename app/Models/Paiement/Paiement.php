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

    protected $visible = ['montant', 'mode_paiement', 'id_saver', 'id_client', 'date_paiement', 'date_commandes'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function saver(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'id_saver');
    }
}
