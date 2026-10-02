<?php

namespace App\Models\Boutiques;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boutiques extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'boutiques';
    public $timestamps = true;
    protected $visible = array('libelle', 'id_client', 'quartier', 'statut');

    public function client()
    {
        return $this->belongsTo('App\Models\Clients\Clients', 'id_client');
    }

    public function quartier_boutique()
    {
        return $this->belongsTo('App\Models\Quartier\Quartier', 'id_quartier');
    }

    public function stock()
    {
        return $this->hasMany('App\Models\Stock\Stock', 'id_boutique');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\Commandes\Commandes', 'id_boutique');
    }

}