<?php

namespace App\Models\Produits;

use Illuminate\Database\Eloquent\Model;

class Produits extends Model
{
    protected $table = 'produits';

    public $timestamps = true;

    protected $visible = ['noms', 'libelle', 'description', 'statut'];

    public function stock()
    {
        return $this->hasMany('App\Models\stock\Stock', 'id_produit');
    }

    public function details_commande()
    {
        return $this->hasMany('App\Models\details_commande\Details_commande', 'id_produit');
    }
}
