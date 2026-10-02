<?php

namespace App\Models\Montant_livraison;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Montant_livraison extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'montant_livraison';
    public $timestamps = true;
    protected $fillable = array('id_zone_colis');
    protected $visible = array('id_zone_livraison', 'montant');

    public function commandes()
    {
        return $this->hasMany('App\Models\Commandes\Commandes', 'id_montant_livraison');
    }

    public function zone_depart()
    {
        return $this->belongsTo('App\Models\Zone\Zone', 'id_zone_colis');
    }

    public function zone_arrivee()
    {
        return $this->belongsTo('App\Models\Zone\Zone', 'id_zone_livraison');
    }

}