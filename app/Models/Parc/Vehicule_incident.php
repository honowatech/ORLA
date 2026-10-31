<?php

namespace App\Models\Parc;

use Illuminate\Database\Eloquent\Model;

class Vehicule_incident extends Model
{
    protected $table = 'vehicule_incident';
    public $timestamps = true;
    protected $fillable = ['id_vehicule','id_coursier','type','date','lieu','description','cout','statut','date_resolution','immobilise','id_saver'];

    public const TYPES = [
        'accident' => 'Accident',
        'panne'    => 'Panne',
        'vol'      => 'Vol',
        'amende'   => 'Amende / Contravention',
        'autre'    => 'Autre',
    ];

    public const STATUTS = [
        'ouvert'   => 'Ouvert',
        'en_cours' => 'En traitement',
        'resolu'   => 'Résolu',
    ];

    public const STATUTS_BADGE = [
        'ouvert'   => 'badge-danger',
        'en_cours' => 'badge-warning',
        'resolu'   => 'badge-success',
    ];

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

    public function getTypeLibelleAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatutLibelleAttribute()
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getStatutBadgeAttribute()
    {
        return self::STATUTS_BADGE[$this->statut] ?? 'badge-secondary';
    }
}
