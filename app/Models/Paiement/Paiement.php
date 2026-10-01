<?php

namespace App\Models\Paiement;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model 
{

    protected $table = 'paiement';
    public $timestamps = true;
    protected $visible = array('montant', 'mode_paiement', 'id_saver', 'id_client', 'date_paiement', 'date_commandes');
    public function client()
    {
        return $this->belongsTo('App\Models\Clients\Clients', 'id_client');
    }
    public function saver()
    {
        return $this->belongsTo('App\Models\User', 'id_saver');
    }

}