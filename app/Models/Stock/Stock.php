<?php

namespace App\Models\Stock;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'stock';
    public $timestamps = true;
    protected $visible = array('id_produit', 'id_boutique', 'id_point_relais', 'statut', 'libelle', 'Quantite_en_stock', 'type_gestion', 'qute_changement');

    public function produit()
    {
        return $this->belongsTo('App\Models\Produits\Produits', 'id_produit');
    }

    public function boutique()
    {
        return $this->belongsTo('App\Models\Boutiques\Boutiques', 'id_boutique');
    }

    public function point_relai()
    {
        return $this->belongsTo('App\Models\Point_relais\Point_relais', 'id_point_relais');
    }

}