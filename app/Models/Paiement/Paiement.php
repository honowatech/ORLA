<?php

namespace App\Models\Paiement;

use App\Models\Clients\Clients;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $table = 'paiement';

    public $timestamps = true;

    protected $visible = ['montant', 'mode_paiement', 'id_saver', 'id_client', 'date_paiement', 'date_commandes'];

    public function client()
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function saver()
    {
        return $this->belongsTo(Users::class, 'id_saver');
    }
}
