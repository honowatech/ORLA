<?php

namespace App\Models;

use App\Models\Coursiers\Coursiers;
use App\Models\Ville\Ville;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;

    protected $table = 'vehicule';

    public $timestamps = true;

    protected $visible = ['immatriculation', 'couleur', 'id_type', 'id_coursier', 'marque', 'modele', 'description', 'statut', 'id_ville'];

    protected $fillable = ['immatriculation', 'couleur', 'id_type', 'id_coursier', 'marque', 'modele', 'description', 'statut', 'id_ville'];

    public function type()
    {
        return $this->belongsTo(Type_vehicule::class, 'id_type');
    }

    public function coursier()
    {
        return $this->belongsTo(Coursiers::class, 'id_coursier');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class, 'id_ville');
    }
}
