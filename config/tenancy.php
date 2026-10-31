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

];
