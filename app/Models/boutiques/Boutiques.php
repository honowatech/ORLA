<?php

namespace App\Models\Boutiques;

use Illuminate\Database\Eloquent\Model;

class Boutiques extends Model
{
    protected $table = 'boutiques';

    public $timestamps = true;

    protected $visible = ['libelle', 'id_client', 'quartier', 'statut'];

    public function client()
    {
        return $this->belongsTo('App\Models\clients\Clients', 'id_client');
    }

    public function quartier_boutique()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier');
    }

    public function stock()
    {
        return $this->hasMany('App\Models\stock\Stock', 'id_boutique');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_boutique');
    }
}
