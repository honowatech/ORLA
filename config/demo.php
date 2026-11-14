<?php

/*
|--------------------------------------------------------------------------
| Démo sans mot de passe
|--------------------------------------------------------------------------
|
| Chaque visiteur reçoit son propre bac à sable : une base SQLite à part,
| déjà remplie, dans laquelle il choisit un profil au lieu de saisir un mot
| de passe. Les données de production ne sont jamais ouvertes à la démo.
| Voir docs/demo.md.
|
*/

return [

    // Désactivée par défaut : les pages répondent 404 tant qu'elle l'est.
    'enabled' => (bool) env('DEMO_ENABLED', false),

    // Dossier des bacs (fichiers SQLite) et du modèle de schéma.
    'path' => env('DEMO_PATH', storage_path('demo')),

    // Durée d'inactivité (prolongée à chaque requête) et durée maximale.
    'idle_minutes' => (int) env('DEMO_IDLE_MINUTES', 120),
    'max_hours' => (int) env('DEMO_MAX_HOURS', 8),

    // Réserve de bacs prêts et plafond de bacs ouverts.
    'pool_size' => (int) env('DEMO_POOL_SIZE', 3),
    'pool_max_age_hours' => (int) env('DEMO_POOL_MAX_AGE_HOURS', 6),
    'max_active' => (int) env('DEMO_MAX_ACTIVE', 100),

    // Réserve vide : construire un bac pendant la requête du visiteur.
    'build_on_demand' => (bool) env('DEMO_BUILD_ON_DEMAND', true),

    // Débit d'entrée par adresse IP.
    'throttle' => [
        'attempts' => (int) env('DEMO_THROTTLE_ATTEMPTS', 3),
        'minutes' => (int) env('DEMO_THROTTLE_MINUTES', 10),
    ],

    // Historique généré et commandes déjà planifiées.
    'history_days' => (int) env('DEMO_HISTORY_DAYS', 60),
    'ahead_days' => (int) env('DEMO_AHEAD_DAYS', 7),

    // Fuseau du marché visé : il décide du « jour » d'un bac.
    'timezone' => env('DEMO_TIMEZONE', 'Africa/Douala'),

    // Domaine réservé et non livrable (RFC 2606) des utilisateurs démo.
    'email_domain' => 'demo.invalid',

    // « Créer mon compte » : vide, le bouton n'apparaît pas.
    'signup_url' => env('DEMO_SIGNUP_URL'),

    /*
    | Profils proposés : slug => libellé du type d'utilisateur, titre, texte.
    | Le client n'envoie qu'un slug ; le serveur cherche l'utilisateur dans
    | le bac attribué.
    */
    'profiles' => [
        'administrateur' => [
            'type' => 'Super Admin',
            'title' => 'Administrateur',
            'text' => "Tableau de bord, commandes, coursiers, zones et tarifs : l'agence entière.",
        ],
        'routeur' => [
            'type' => 'Agent',
            'title' => 'Agent routeur',
            'text' => 'Saisir les commandes du jour et les attribuer aux coursiers.',
        ],
        'coursier' => [
            'type' => 'Coursier',
            'title' => 'Coursier',
            'text' => 'Sa tournée du jour : commandes à récupérer, à livrer, son activité.',
        ],
        'client' => [
            'type' => 'Client',
            'title' => 'Client entreprise',
            'text' => 'Une boutique qui passe ses commandes et suit ses livraisons.',
        ],
    ],

];
