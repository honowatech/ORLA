<?php

/*
|--------------------------------------------------------------------------
| Plateforme multi-entreprises (SaaS)
|--------------------------------------------------------------------------
|
| Paramètres communs à toutes les entreprises hébergées : durée d'essai,
| rigueur du cloisonnement des données, entreprise de rattachement des
| données historiques et valeurs par défaut propres au marché cible.
|
*/

return [

    // Nombre de jours d'essai gratuit offerts à une nouvelle entreprise (0 = paiement immédiat).
    'essai_jours' => (int) env('SAAS_ESSAI_JOURS', 14),

    // En mode strict, interroger un modèle cloisonné sans entreprise courante lève une exception.
    'tenancy_strict' => (bool) env('SAAS_TENANCY_STRICT', true),

    // Entreprise créée lors de la migration pour accueillir les données existantes.
    'entreprise_legacy' => [
        'nom' => env('SAAS_ENTREPRISE_LEGACY_NOM', 'Speedex'),
        'slug' => env('SAAS_ENTREPRISE_LEGACY_SLUG', 'speedex'),
    ],

    // Tarif de livraison appliqué par défaut aux nouvelles paires de zones.
    'tarif_defaut' => (float) env('SAAS_TARIF_DEFAUT', 1000),

    'devise' => env('SAAS_DEVISE', 'XAF'),
    'pays' => env('SAAS_PAYS', 'CM'),
    'prefixe_telephone' => env('SAAS_PREFIXE_TELEPHONE', '+237'),

    // Format des numéros nationaux (sans indicatif) par pays.
    'telephone_regex' => [
        'CM' => '/^[62][0-9]{8}$/',
    ],

];
