<?php

namespace App\Services\Routage;

/**
 * Résultat d'une planification : séquence d'étapes horodatées + totaux.
 */
class ResultatItineraire
{
    /**
     * @var array<int, array{
     *   arret: Arret, ordre:int, heure_arrivee:int, heure_depart:int,
     *   distance_km:float, duree_minutes:float, attente_minutes:float, retard_minutes:float,
     *   chemin:int[], estime:bool
     * }>
     */
    public array $etapes = [];

    public float $distanceKm = 0;
    public float $dureeMinutes = 0;      // trajet + attentes + manutention
    public float $attenteMinutes = 0;
    public float $retardMinutes = 0;
    public float $cout = 0;              // coût d'exploitation (km + heures)
    public float $coutPenalise = 0;      // coût + pénalités de retard : fonction objectif
    public int $heureDepart = 0;
    public int $heureFin = 0;
    /** @var string[] */
    public array $avertissements = [];

    public function toArray(): array
    {
        return [
            'distance_km' => round($this->distanceKm, 2),
            'duree_minutes' => round($this->dureeMinutes),
            'attente_minutes' => round($this->attenteMinutes),
            'retard_minutes' => round($this->retardMinutes),
            'cout' => round($this->cout),
            'cout_penalise' => round($this->coutPenalise),
            'heure_depart' => date('Y-m-d H:i', $this->heureDepart),
            'heure_fin' => date('Y-m-d H:i', $this->heureFin),
            'avertissements' => $this->avertissements,
            'etapes' => array_map(fn ($e) => [
                'ordre' => $e['ordre'],
                'id_commande' => $e['arret']->idCommande,
                'type' => $e['arret']->type,
                'id_quartier' => $e['arret']->idQuartier,
                'libelle' => $e['arret']->libelle,
                'heure_arrivee' => date('Y-m-d H:i', $e['heure_arrivee']),
                'distance_km' => round($e['distance_km'], 2),
                'duree_minutes' => round($e['duree_minutes']),
                'attente_minutes' => round($e['attente_minutes']),
                'retard_minutes' => round($e['retard_minutes']),
                'chemin' => $e['chemin'],
                'estime' => $e['estime'],
            ], $this->etapes),
        ];
    }
}
