<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;
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
        return $this->belongsTo('App\Models\coursiers\Coursiers', 'id_coursier');
    }
    public function ville()
    {
        return $this->belongsTo('App\Models\ville\Ville', 'id_ville');
    }
}