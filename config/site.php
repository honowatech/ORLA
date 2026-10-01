<?php

/*
|--------------------------------------------------------------------------
| Site vitrine public
|--------------------------------------------------------------------------
|
| Identité et coordonnées affichées sur les pages publiques (accueil,
| fonctionnalités, tarifs, FAQ, contact) et dans les balises SEO.
|
*/

return [

    'nom' => env('SITE_NOM', env('APP_NAME', 'ORLA')),
    'baseline' => env('SITE_BASELINE', 'Logiciel de gestion de livraison pour entreprises de coursiers'),
    'url' => rtrim((string) env('APP_URL', 'http://localhost'), '/'),

    'contact' => [
        'email' => env('SITE_CONTACT_EMAIL'),
        'telephone' => env('SITE_CONTACT_TEL'),
    ],

    'editeur' => [
        'nom' => 'Honowa Technologies',
        'url' => 'https://honowa.com',
    ],

    'reseaux' => [
        'facebook' => null,
        'linkedin' => null,
        'whatsapp' => null,
    ],

    // Image Open Graph par défaut (1200 × 630), relative à public/.
    'image_og' => '/images/site/og-cover.jpg',

    // Balise <script> complète d'un outil de mesure d'audience (Plausible, GA4…), chargée en différé.
    'analytics_script' => env('SITE_ANALYTICS_SCRIPT'),

];
