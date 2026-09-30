<?php

namespace App\Models\Point_relais;

use App\Models\Commandes\Commandes;
use App\Models\Coursiers\Coursiers;
use App\Models\Quartier\Quartier;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Model;

class Point_relais extends Model
{
    protected $table = 'point_relais';

    public $timestamps = true;

    protected $fillable = ['id_quartier'];

    protected $visible = ['libelle', 'id_coursier', 'quartier', 'statut'];

    public function quartier()
    {
        return $this->belongsTo(Quartier::class, 'id_quartier');
    }

    public function coursier()
    {
        return $this->belongsTo(Coursiers::class, 'id_coursier');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'id_point_relais');
    }

    public function commandes()
    {
        return $this->hasMany(Commandes::class, 'id_point_relais');
    }
}
