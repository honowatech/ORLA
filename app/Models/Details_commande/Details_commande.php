<?php

namespace App\Models\Details_commande;

use App\Models\Commandes\Commandes;
use App\Models\Produits\Produits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Details_commande extends Model
{
    protected $table = 'details_commande';

    public $timestamps = true;

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commandes::class, 'id_commande');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produits::class, 'id_produit');
    }
}
