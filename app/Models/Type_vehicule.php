<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type_vehicule extends Model
{
    use HasFactory;
    protected $table = 'type_vehicule';
    public $timestamps = true;
    protected $visible = array('libelle','description','statut');
    protected $fillable = array('libelle','description','statut');


    public function vehicule()
    {
        return $this->HasMany('App\Models\Vehicule', 'id_type');
    }
}
