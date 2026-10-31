<?php

namespace App\Models\tournee;

use Illuminate\Database\Eloquent\Model;

class Tournee extends Model
{
    protected $table = 'tournee';
    public $timestamps = true;

    protected $fillable = [
        'id_ville', 'id_coursier', 'id_vehicule', 'id_quartier_depart', 'id_saver', 'date_depart',
        'date_fin_estimee', 'distance_totale_km', 'duree_totale_minutes', 'cout_total',
        'distance_reference_km', 'duree_reference_minutes', 'cout_reference', 'avertissements', 'statut',
    ];

    protected $casts = [
        'avertissements' => 'array',
        'date_depart' => 'datetime',
        'date_fin_estimee' => 'datetime',
    ];

    public const STATUTS = [
        'planifiee' => 'Planifiée',
        'encours' => 'En cours',
        'terminee' => 'Terminée',
        'annulee' => 'Annulée',
    ];

    public function etapes()
    {
        return $this->hasMany('App\Models\tournee\Tournee_etape', 'id_tournee')->orderBy('ordre');
    }

    public function coursier()
    {
        return $this->belongsTo('App\Models\coursiers\Coursiers', 'id_coursier');
    }

    public function vehicule()
    {
        return $this->belongsTo('App\Models\Vehicule', 'id_vehicule');
    }

    public function ville()
    {
        return $this->belongsTo('App\Models\ville\Ville', 'id_ville');
    }

    public function quartier_depart()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier_depart');
    }

    public function enregistreur()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_saver');
    }

    /** Économie (en %) de l'itinéraire optimisé par rapport à l'ordre de saisie. */
    public function gainPourcent(string $champ = 'duree'): float
    {
        [$ref, $opt] = match ($champ) {
            'distance' => [$this->distance_reference_km, $this->distance_totale_km],
            'cout' => [$this->cout_reference, $this->cout_total],
            default => [$this->duree_reference_minutes, $this->duree_totale_minutes],
        };
        if ($ref <= 0) {
            return 0;
        }
        return round(($ref - $opt) / $ref * 100, 1);
    }
}
