<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use BelongsToEntreprise, HasFactory;
    protected $table = 'vehicule';
    public $timestamps = true;
    protected $visible = array('immatriculation','couleur','id_type','id_coursier','marque','modele','description','statut','id_ville');
    protected $fillable = array('immatriculation','couleur','id_type','id_coursier','marque','modele','description','statut','id_ville');


    public function type()
    {
        return $this->belongsTo('App\Models\Type_vehicule', 'id_type');
    }
    public function coursier()
    {
        return $this->belongsTo('App\Models\Coursiers\Coursiers', 'id_coursier');
    }
    public function ville()
    {
        return $this->belongsTo('App\Models\Ville\Ville', 'id_ville');
    }
}