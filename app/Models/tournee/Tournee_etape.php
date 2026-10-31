<?php

namespace App\Models\tournee;

use Illuminate\Database\Eloquent\Model;

class Tournee_etape extends Model
{
    protected $table = 'tournee_etape';
    public $timestamps = true;

    protected $fillable = [
        'id_tournee', 'id_commande', 'id_quartier', 'type_etape', 'ordre', 'heure_arrivee_estimee',
        'distance_etape_km', 'duree_etape_minutes', 'attente_minutes', 'retard_minutes', 'itineraire',
    ];

    protected $casts = [
        'itineraire' => 'array',
        'heure_arrivee_estimee' => 'datetime',
    ];

    public function tournee()
    {
        return $this->belongsTo('App\Models\tournee\Tournee', 'id_tournee');
    }

    public function commande()
    {
        return $this->belongsTo('App\Models\commandes\Commandes', 'id_commande');
    }

    public function quartier()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier');
    }
}
