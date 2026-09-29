<?php

namespace App\Models\Point_relais;

use Illuminate\Database\Eloquent\Model;

class Point_relais extends Model 
{

    protected $table = 'point_relais';
    public $timestamps = true;
    protected $fillable = array('id_quartier');
    protected $visible = array('libelle', 'id_coursier', 'quartier', 'statut');

    public function quartier()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier');
    }

    public function coursier()
    {
        return $this->belongsTo('App\Models\coursiers\Coursiers', 'id_coursier');
    }

    public function stock()
    {
        return $this->hasMany('App\Models\stock\Stock', 'id_point_relais');
    }

    public function commandes()
    {
        return $this->hasMany('App\Models\commandes\Commandes', 'id_point_relais');
    }

}