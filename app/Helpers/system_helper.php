<?php

if (! function_exists('filter')) {
    function filter($good_profil, $user, $utilitie = 'middleware')
    {
        if (! auth()->check()) {
            return 'home';
        }
        $min_libelle = strtolower($user->type_utilisateur->libelle) == 'agent' ? 'routeur' : strtolower($user->type_utilisateur->libelle);
        $min_libelle = strtolower($user->type_utilisateur->libelle) == 'super admin' ? 'admin' : $min_libelle;
        $route = [
            'admin' => 'home.admin',
            'coursier' => 'home.coursier',
            'routeur' => 'home.routeur',
            'client' => 'home.client',
            'superviseur_ville' => 'home.superviseur_ville',
        ];
        if ($user->statut == 0) {
            if ($utilitie == 'middleware') {
                return 'home.error';
            }
        }
        if (! in_array($min_libelle, $good_profil)) {
            // dd($min_libelle, $good_profil);
            if ($utilitie == 'middleware') {
                return $route[$min_libelle];
            }
        }

        return 'true';
    }
}
if (! function_exists('Dossier')) {
    function Dossier($use_type)
    {
        $dossier = [
            'ADMIN' => 'admin',
            'SUPER ADMIN' => 'admin',
            'COURSIER' => 'coursier',
            'AGENT' => 'routeur',
            'CLIENT' => 'client',
            'SUPERVISEUR_VILLE' => 'superviseur_ville',
        ];

        return $dossier[strtoupper($use_type)];
    }
}
