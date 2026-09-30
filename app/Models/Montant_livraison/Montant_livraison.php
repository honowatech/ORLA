<?php

namespace App\Models\Montant_livraison;

use App\Models\Commandes\Commandes;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Model;

class Montant_livraison extends Model
{
    protected $table = 'montant_livraison';

    public $timestamps = true;

    protected $fillable = ['id_zone_colis'];

    protected $visible = ['id_zone_livraison', 'montant'];

    public function commandes()
    {
        return $this->hasMany(Commandes::class, 'id_montant_livraison');
    }

    public function zone_depart()
    {
        return $this->belongsTo(Zone::class, 'id_zone_colis');
    }

    public function zone_arrivee()
    {
        return $this->belongsTo(Zone::class, 'id_zone_livraison');
    }
}
