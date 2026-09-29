<?php

namespace Paiement;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model 
{

    protected $table = 'paiement';
    public $timestamps = true;
    protected $visible = array('montant', 'mode_paiement', 'id_saver', 'id_client', 'date_paiement', 'date_commandes');

}