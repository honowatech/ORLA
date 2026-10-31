<?php

namespace App\Models\Parc;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Vehicule_document extends Model
{
    protected $table = 'vehicule_document';
    public $timestamps = true;
    protected $fillable = ['id_vehicule','type','libelle','numero','fournisseur','date_debut','date_expiration','montant','fichier','description','id_saver'];

    public const TYPES = [
        'assurance'        => 'Assurance',
        'visite_technique' => 'Visite technique',
        'carte_grise'      => 'Carte grise',
        'vignette'         => 'Vignette / Taxe',
        'permis_transport' => 'Permis de transport',
        'autre'            => 'Autre',
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

    /** Nombre de jours restants avant expiration (négatif si expiré). */
    public function getJoursRestantsAttribute()
    {
        if ($this->date_expiration == null) {
            return null;
        }
        return (int) Carbon::now()->startOfDay()->diffInDays(Carbon::parse($this->date_expiration), false);
    }

    public function getEstExpireAttribute()
    {
        return $this->jours_restants !== null && $this->jours_restants < 0;
    }

    /** valide, bientot (expire sous 30 jours), expire. */
    public function getEtatAttribute()
    {
        $jours = $this->jours_restants;
        if ($jours === null) return 'valide';
        if ($jours < 0) return 'expire';
        if ($jours <= 30) return 'bientot';
        return 'valide';
    }

    public function getEtatBadgeAttribute()
    {
        return ['valide' => 'badge-success', 'bientot' => 'badge-warning', 'expire' => 'badge-danger'][$this->etat];
    }

    public function getEtatLibelleAttribute()
    {
        return ['valide' => 'Valide', 'bientot' => 'À renouveler', 'expire' => 'Expiré'][$this->etat];
    }
}
