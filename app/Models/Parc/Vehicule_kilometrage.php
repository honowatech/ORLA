<?php

namespace App\Models\Parc;

use Illuminate\Database\Eloquent\Model;

class Vehicule_kilometrage extends Model
{
    protected $table = 'vehicule_kilometrage';
    public $timestamps = true;
    protected $fillable = ['id_vehicule','date','kilometrage','source','description','id_saver'];

    public const SOURCES = [
        'manuel'      => 'Relevé manuel',
        'carburant'   => 'Plein de carburant',
        'maintenance' => 'Entretien',
        'attribution' => 'Attribution coursier',
        'incident'    => 'Incident',
    ];

    public function vehicule()
    {
        return $this->belongsTo('App\Models\Vehicule', 'id_vehicule');
    }

    public function enregistreur()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_saver');
    }

    public function getSourceLibelleAttribute()
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }
}
