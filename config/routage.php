<?php

/*
|--------------------------------------------------------------------------
| Paramètres de l'optimiseur d'itinéraires (tournées de livraison)
|--------------------------------------------------------------------------
*/
return [

    // Multiplicateur de durée selon l'état de la route (impraticable = tronçon exclu).
    'coefficient_etat_route' => [
        'bon' => 1.0,
        'moyen' => 1.25,
        'degrade' => 1.6,
        'impraticable' => null,
    ],

    // Multiplicateur de durée selon l'heure de passage (trafic). Clé : "HH:MM-HH:MM".
    'coefficient_trafic' => [
        '06:30-09:00' => 1.4,
        '12:00-14:00' => 1.15,
        '16:30-19:30' => 1.5,
    ],

    // Vitesse moyenne par défaut (km/h) si le type de véhicule n'en précise pas.
    'vitesse_defaut' => 30,

    // Facteur appliqué à la distance à vol d'oiseau (haversine) pour estimer la distance routière.
    'facteur_detour' => 1.3,

    // Distance estimée (km) entre deux quartiers sans coordonnées ni tronçon.
    'distance_defaut' => [
        'meme_quartier' => 0.5,
        'meme_zone' => 3,
        'zones_differentes' => 7,
    ],

    // Temps de manutention à chaque arrêt (minutes).
    'temps_service_minutes' => 5,

    // Pénalité (coût par minute) appliquée aux retards sur la date de livraison promise.
    'penalite_retard_par_minute' => 50,

    // Nombre max d'itérations d'amélioration locale (2-opt / or-opt).
    'iterations_max' => 500,
];
