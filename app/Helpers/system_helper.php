<?php
    if (!function_exists('filter')) {
        function filter($good_profil,$user,$utilitie = 'middleware') {
            if(!auth()->check()){
                    return 'home';
            }
            $min_libelle = strtolower($user->type_utilisateur->libelle) == 'agent' ? 'routeur' : strtolower($user->type_utilisateur->libelle);
            $min_libelle = strtolower($user->type_utilisateur->libelle) == 'super admin' ? 'admin' : $min_libelle;
            // Les agents (« routeur ») et superviseurs partagent le tableau de bord admin :
            // les routes home.routeur / home.superviseur_ville n'ont jamais existé.
            $route = [
                        'admin' =>'home.admin',
                        'coursier' =>'home.coursier',
                        'routeur' =>'home.admin',
                        'client' =>'home.client',
                        'superviseur_ville' =>'home.admin',
                    ];
            if ($user->statut == 0) {
                if ( $utilitie == 'middleware') {
                    return 'home.error';
                }
            }
            if (!in_array($min_libelle, $good_profil)) {
                // dd($min_libelle, $good_profil);
                if ( $utilitie == 'middleware') {
                    return $route[$min_libelle];
                }
            }
            return 'true';
        }
    }
    if (!function_exists('Dossier')) {
        function Dossier($use_type) {
            // Les vues « multi » s'appuient sur le gabarit admin pour les agents et
            // superviseurs (les dossiers routeur/ et superviseur_ville/ n'existent pas).
            $dossier = [
                        'ADMIN' =>'admin',
                        'SUPER ADMIN' =>'admin',
                        'COURSIER' =>'coursier',
                        'AGENT' =>'admin',
                        'CLIENT' =>'client',
                        'SUPERVISEUR_VILLE' =>'admin',
                    ];
            return $dossier[strtoupper($use_type)];
        }
    }
?>