<?php

namespace App\Models\Produits;

use App\Models\Details_commande\Details_commande;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Model;

class Produits extends Model
{
    protected $table = 'produits';

    public $timestamps = true;

    protected $visible = ['noms', 'libelle', 'description', 'statut'];

    public function stock()
    {
        return $this->hasMany(Stock::class, 'id_produit');
    }

    public function details_commande()
    {
        return $this->hasMany(Details_commande::class, 'id_produit');
    }
}
