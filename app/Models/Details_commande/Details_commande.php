<?php

namespace App\Models\Details_commande;

use Illuminate\Database\Eloquent\Model;

class Details_commande extends Model 
{

    protected $table = 'details_commande';
    public $timestamps = true;
    protected $visible = array('id_commande', 'id_produit', 'nom_produit', 'quantite', 'prix');

    public function commande()
    {
        return $this->belongsTo('App\Models\Commandes\Commandes', 'id_commande');
    }

    public function produit()
    {
        return $this->belongsTo('App\Models\Produits\Produits', 'id_produit');
    }

}