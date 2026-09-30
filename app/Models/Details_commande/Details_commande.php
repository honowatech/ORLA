<?php

namespace App\Models\Details_commande;

use App\Models\Commandes\Commandes;
use App\Models\Produits\Produits;
use Illuminate\Database\Eloquent\Model;

class Details_commande extends Model
{
    protected $table = 'details_commande';

    public $timestamps = true;

    protected $visible = ['id_commande', 'id_produit', 'nom_produit', 'quantite', 'prix'];

    public function commande()
    {
        return $this->belongsTo(Commandes::class, 'id_commande');
    }

    public function produit()
    {
        return $this->belongsTo(Produits::class, 'id_produit');
    }
}
