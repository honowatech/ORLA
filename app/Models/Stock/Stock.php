<?php

namespace App\Models\Stock;

use App\Models\Boutiques\Boutiques;
use App\Models\Point_relais\Point_relais;
use App\Models\Produits\Produits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $table = 'stock';

    public $timestamps = true;

    protected $visible = ['id_produit', 'id_boutique', 'id_point_relais', 'statut', 'libelle', 'Quantite_en_stock', 'type_gestion', 'qute_changement'];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produits::class, 'id_produit');
    }

    public function boutique(): BelongsTo
    {
        return $this->belongsTo(Boutiques::class, 'id_boutique');
    }

    public function point_relai(): BelongsTo
    {
        return $this->belongsTo(Point_relais::class, 'id_point_relai');
    }
}
