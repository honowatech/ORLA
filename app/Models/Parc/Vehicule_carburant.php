<?php

namespace App\Models\Parc;

use Illuminate\Database\Eloquent\Model;

class Vehicule_carburant extends Model
{
    protected $table = 'vehicule_carburant';
    public $timestamps = true;
    protected $fillable = ['id_vehicule','id_coursier','date','litres','montant','kilometrage','plein_complet','station','description','id_saver'];

    public function vehicule()
    {
        return $this->belongsTo('App\Models\Vehicule', 'id_vehicule');
    }

    public function coursier()
    {
        return $this->belongsTo('App\Models\coursiers\Coursiers', 'id_coursier');
    }

    public function enregistreur()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_saver');
    }

    public function getPrixLitreAttribute()
    {
        return $this->litres > 0 ? round($this->montant / $this->litres, 2) : null;
    }
}
