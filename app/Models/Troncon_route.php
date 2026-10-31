<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Troncon_route extends Model
{
    protected $table = 'troncon_route';
    public $timestamps = true;

    protected $fillable = [
        'id_ville', 'id_quartier_depart', 'id_quartier_arrivee', 'distance_km', 'duree_minutes',
        'etat_route', 'bidirectionnel', 'heure_debut_fermeture', 'heure_fin_fermeture',
        'types_vehicule_interdits', 'description', 'statut',
    ];

    protected $casts = [
        'types_vehicule_interdits' => 'array',
        'bidirectionnel' => 'boolean',
        'statut' => 'boolean',
    ];

    public const ETATS = [
        'bon' => 'Bon état',
        'moyen' => 'État moyen',
        'degrade' => 'Dégradée',
        'impraticable' => 'Impraticable',
    ];

    public function ville()
    {
        return $this->belongsTo('App\Models\ville\Ville', 'id_ville');
    }

    public function quartier_depart()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier_depart');
    }

    public function quartier_arrivee()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier_arrivee');
    }
}
