<?php

namespace App\Models\Parc;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Vehicule_maintenance extends Model
{
    protected $table = 'vehicule_maintenance';
    public $timestamps = true;
    protected $fillable = ['id_vehicule','type','libelle','description','date_prevue','date_realisation','kilometrage','cout','prestataire','prochaine_date','prochain_kilometrage','statut','id_saver'];

    public const TYPES = [
        'vidange'     => 'Vidange',
        'pneus'       => 'Pneumatiques',
        'freins'      => 'Freins',
        'revision'    => 'Révision périodique',
        'reparation'  => 'Réparation',
        'carrosserie' => 'Carrosserie',
        'controle'    => 'Contrôle / Diagnostic',
        'autre'       => 'Autre',
    ];

    public const STATUTS = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'realise'  => 'Réalisé',
        'annule'   => 'Annulé',
    ];

    public const STATUTS_BADGE = [
        'planifie' => 'badge-info',
        'en_cours' => 'badge-warning',
        'realise'  => 'badge-success',
        'annule'   => 'badge-secondary',
    ];

    public function vehicule()
    {
        return $this->belongsTo('App\Models\Vehicule', 'id_vehicule');
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

    /** Un entretien planifié dont la date prévue est dépassée. */
    public function getEnRetardAttribute()
    {
        return in_array($this->statut, ['planifie', 'en_cours'])
            && $this->date_prevue != null
            && Carbon::parse($this->date_prevue)->lt(Carbon::now()->startOfDay());
    }
}
