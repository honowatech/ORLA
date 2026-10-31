<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Essai gratuit
    |--------------------------------------------------------------------------
    |
    | Nombre de jours d'essai offerts à une entreprise qui s'inscrit. Pendant
    | l'essai, toutes les fonctionnalités sont accessibles.
    |
    */

    'essai_jours' => (int) env('TENANCY_ESSAI_JOURS', 14),

    /*
    | Le créateur d'une entreprise doit confirmer son email avant d'accéder à
    | son espace. Nécessite un envoi d'emails configuré (MAIL_*).
    */

    'verifier_email' => (bool) env('TENANCY_VERIFIER_EMAIL', true),

    /*
    |--------------------------------------------------------------------------
    | Tables métier
    |--------------------------------------------------------------------------
    |
    | « tables » : propres à chaque entreprise (entreprise_id obligatoire).
    | « tables_partagees » : référentiels où entreprise_id vide = commun à toutes.
    |
    | Les règles de validation unique/exists sur ces tables sont limitées à
    | l'entreprise courante, sauf les colonnes de « unicite_globale ».
    |
    */

    'tables' => [
        'users', 'agents', 'coursiers', 'clients', 'informations_personnels',
        'zone', 'quartier', 'details_zone', 'montant_livraison',
        'vehicule',
        'boutiques', 'point_relais', 'produits', 'stock',
        'commandes', 'details_commande', 'paiement', 'activity',
    ],

    'tables_partagees' => ['ville', 'type_vehicule'],

    // L'email sert à se connecter sans choisir son entreprise : il reste unique sur toute la plateforme.
    'unicite_globale' => [
        'users' => ['email'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Liens entre données métier
    |--------------------------------------------------------------------------
    |
    | Pour chaque table : colonne => table référencée. À l'enregistrement, la
    | ligne référencée doit appartenir à la même entreprise (ou être commune) :
    | impossible d'attribuer une commande au livreur d'une autre entreprise.
    |
    */

    'liens' => [
        'users' => ['id_agent' => 'agents', 'id_coursier' => 'coursiers', 'id_client' => 'clients', 'id_ville' => 'ville'],
        'agents' => ['id_utilisateur' => 'users'],
        'coursiers' => ['id_utilisateur' => 'users'],
        'clients' => ['id_utilisateur' => 'users'],
        'informations_personnels' => ['id_agent' => 'agents', 'id_client' => 'clients', 'id_coursier' => 'coursiers', 'id_quartier' => 'quartier'],
        'zone' => ['id_ville' => 'ville'],
        'quartier' => ['id_zone' => 'zone', 'id_ville' => 'ville'],
        'details_zone' => ['id_coursier' => 'coursiers', 'id_zone' => 'zone'],
        'montant_livraison' => ['id_zone_colis' => 'zone', 'id_zone_livraison' => 'zone'],
        'vehicule' => ['id_type' => 'type_vehicule', 'id_coursier' => 'coursiers', 'id_ville' => 'ville'],
        'boutiques' => ['id_client' => 'clients', 'id_quartier' => 'quartier'],
        'point_relais' => ['id_coursier' => 'coursiers', 'id_quartier' => 'quartier'],
        'stock' => ['id_produit' => 'produits', 'id_boutique' => 'boutiques', 'id_point_relais' => 'point_relais'],
        'commandes' => [
            'id_client' => 'clients', 'id_boutique' => 'boutiques', 'id_coursier' => 'coursiers',
            'id_point_relais' => 'point_relais', 'id_saver' => 'users', 'id_agent' => 'agents',
            'id_quartier_colis' => 'quartier', 'id_quartier_livraison' => 'quartier',
            'id_montant_livraison' => 'montant_livraison',
        ],
        'details_commande' => ['id_commande' => 'commandes', 'id_produit' => 'produits'],
        'paiement' => ['id_saver' => 'users', 'id_client' => 'clients'],
        'activity' => ['id_user' => 'users'],
    ],

];
