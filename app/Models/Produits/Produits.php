<?php

namespace App\Models\Produits;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produits extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'produits';
    public $timestamps = true;
    protected $visible = array('noms', 'libelle', 'description', 'statut');

    public function stock()
    {
        return $this->hasMany('App\Models\Stock\Stock', 'id_produit');
    }

    public function details_commande()
    {
        return $this->hasMany('App\Models\Details_commande\Details_commande', 'id_produit');
    }

}