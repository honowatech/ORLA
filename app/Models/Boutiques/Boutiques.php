<?php

namespace App\Models\Boutiques;

use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Quartier\Quartier;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Model;

class Boutiques extends Model
{
    protected $table = 'boutiques';

    public $timestamps = true;

    protected $visible = ['libelle', 'id_client', 'quartier', 'statut'];

    public function client()
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function quartier_boutique()
    {
        return $this->belongsTo(Quartier::class, 'id_quartier');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'id_boutique');
    }

    public function commandes()
    {
        return $this->hasMany(Commandes::class, 'id_boutique');
    }
}
