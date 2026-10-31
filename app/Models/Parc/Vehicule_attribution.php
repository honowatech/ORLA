<?php

namespace App\Models\Parc;

use Illuminate\Database\Eloquent\Model;

class Vehicule_attribution extends Model
{
    protected $table = 'vehicule_attribution';
    public $timestamps = true;
    protected $fillable = ['id_vehicule','id_coursier','date_debut','date_fin','kilometrage_debut','kilometrage_fin','id_saver'];

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

    public function getDistanceAttribute()
    {
        if ($this->kilometrage_debut === null || $this->kilometrage_fin === null) {
            return null;
        }
        return max(0, $this->kilometrage_fin - $this->kilometrage_debut);
    }
}
