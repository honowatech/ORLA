<?php

namespace App\Services\Routage;

/**
 * Optimiseur de tournées de livraison.
 *
 * 1. Plus court chemin entre deux quartiers par Dijkstra dépendant du temps :
 *    chaque tronçon est pondéré par sa durée, corrigée par l'état de la route
 *    et le trafic à l'heure de passage ; les tronçons fermés ou interdits au
 *    type de véhicule sont ignorés. Sans tronçon, la distance est estimée
 *    (vol d'oiseau × facteur de détour, ou valeur par défaut selon la zone).
 * 2. Ordonnancement des arrêts : construction gloutonne (plus proche voisin
 *    pondéré par les échéances) en respectant la précédence enlèvement →
 *    livraison, puis amélioration locale (relocalisation / 2-opt) tant que le
 *    coût pénalisé diminue.
 * 3. Simulation horodatée : attentes imposées par la plage horaire du
 *    véhicule, retards par rapport aux dates de livraison promises, coûts.
 */
class RouteOptimizer
{
    private array $config;

    /** cache des plus courts chemins : "a|b|heure" => résultat */
    private array $cacheChemins = [];

    public function __construct(private ReseauRoutier $reseau, array $config = [])
    {
        $this->config = array_replace_recursive(self::configDefaut(), $config);
    }

    public static function configDefaut(): array
    {
        return [
            'coefficient_etat_route' => ['bon' => 1.0, 'moyen' => 1.25, 'degrade' => 1.6, 'impraticable' => null],
            'coefficient_trafic' => [],
            'vitesse_defaut' => 30,
            'facteur_detour' => 1.3,
            'distance_defaut' => ['meme_quartier' => 0.5, 'meme_zone' => 3, 'zones_differentes' => 7],
            'temps_service_minutes' => 5,
            'penalite_retard_par_minute' => 50,
            'iterations_max' => 500,
        ];
    }

    // ------------------------------------------------------------------
    //  API publique
    // ------------------------------------------------------------------

    /**
     * Planifie la meilleure tournée pour un ensemble d'arrêts.
     *
     * @param Arret[] $arrets
     */
    public function optimiser(array $arrets, int $quartierDepart, int $heureDepart, ContraintesVehicule $vehicule): ResultatItineraire
    {
        $arrets = array_values($arrets);
        if (empty($arrets)) {
            $vide = new ResultatItineraire();
            $vide->heureDepart = $vide->heureFin = $heureDepart;
            return $vide;
        }

        $sequence = $this->construireGlouton($arrets, $quartierDepart, $heureDepart, $vehicule);
        $sequence = $this->ameliorer($sequence, $quartierDepart, $heureDepart, $vehicule);

        $resultat = $this->evaluer($sequence, $quartierDepart, $heureDepart, $vehicule);
        $this->ajouterAvertissements($resultat, $vehicule);
        return $resultat;
    }

    /**
     * Simule la tournée dans l'ordre donné (sert de référence « non optimisée »).
     *
     * @param Arret[] $arrets
     */
    public function evaluer(array $arrets, int $quartierDepart, int $heureDepart, ContraintesVehicule $vehicule): ResultatItineraire
    {
        $r = new ResultatItineraire();
        $r->heureDepart = $heureDepart;
        $t = $heureDepart;
        $courant = $quartierDepart;
        $service = (float) $this->config['temps_service_minutes'] * 60;
        $penalite = (float) $this->config['penalite_retard_par_minute'];

        foreach (array_values($arrets) as $i => $arret) {
            $attente = 0;
            $ouverture = $vehicule->prochaineOuverture($t);
            if ($ouverture > $t) {
                $attente = ($ouverture - $t) / 60;
                $t = $ouverture;
            }

            $leg = $this->plusCourtChemin($courant, $arret->idQuartier, $t, $vehicule);
            $t += (int) round($leg['duree'] * 60);
            $arrivee = $t;

            $retard = 0;
            if (!$arret->estEnlevement() && $arret->echeance !== null && $arrivee > $arret->echeance) {
                $retard = ($arrivee - $arret->echeance) / 60;
            }

            $t += (int) $service;

            $r->etapes[] = [
                'arret' => $arret,
                'ordre' => $i + 1,
                'heure_arrivee' => $arrivee,
                'heure_depart' => $t,
                'distance_km' => $leg['distance'],
                'duree_minutes' => $leg['duree'],
                'attente_minutes' => $attente,
                'retard_minutes' => $retard,
                'chemin' => $leg['chemin'],
                'estime' => $leg['estime'],
            ];

            $r->distanceKm += $leg['distance'];
            $r->attenteMinutes += $attente;
            $r->retardMinutes += $retard;
            $courant = $arret->idQuartier;
        }

        $r->heureFin = $t;
        $r->dureeMinutes = ($t - $heureDepart) / 60;
        $r->cout = $r->distanceKm * $vehicule->coutKm + ($r->dureeMinutes / 60) * $vehicule->coutHeure;
        $r->coutPenalise = $r->cout + $r->retardMinutes * $penalite
            // sans coûts paramétrés, on minimise tout de même la durée puis la distance
            + ($vehicule->coutKm == 0 && $vehicule->coutHeure == 0 ? $r->dureeMinutes + $r->distanceKm * 0.1 : 0);

        return $r;
    }

    /**
     * Plus court chemin (en durée) entre deux quartiers au départ à l'instant $heure.
     *
     * @return array{distance:float, duree:float, chemin:int[], estime:bool}
     */
    public function plusCourtChemin(int $de, int $vers, int $heure, ContraintesVehicule $vehicule): array
    {
        if ($de === $vers) {
            return ['distance' => 0.0, 'duree' => 0.0, 'chemin' => [$de], 'estime' => false];
        }
        // granularité du cache : le quart d'heure (le trafic varie lentement)
        $cle = $de.'|'.$vers.'|'.intdiv($heure, 900).'|'.($vehicule->idType ?? 0).'|'.$vehicule->vitesseKmh;
        if (isset($this->cacheChemins[$cle])) {
            return $this->cacheChemins[$cle];
        }

        $resultat = $this->dijkstra($de, $vers, $heure, $vehicule) ?? $this->estimerDirect($de, $vers, $heure, $vehicule);
        return $this->cacheChemins[$cle] = $resultat;
    }

    // ------------------------------------------------------------------
    //  Plus court chemin
    // ------------------------------------------------------------------

    private function dijkstra(int $de, int $vers, int $heureDepart, ContraintesVehicule $vehicule): ?array
    {
        if (empty($this->reseau->voisins($de))) {
            return null;
        }
        $file = new \SplPriorityQueue();
        $file->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        $meilleur = [$de => 0.0];     // durée cumulée (minutes)
        $distance = [$de => 0.0];
        $precedent = [];
        $visite = [];
        $file->insert($de, 0);

        while (!$file->isEmpty()) {
            $item = $file->extract();
            $noeud = $item['data'];
            if (isset($visite[$noeud])) {
                continue;
            }
            $visite[$noeud] = true;
            if ($noeud === $vers) {
                break;
            }
            $heureNoeud = $heureDepart + (int) round($meilleur[$noeud] * 60);
            foreach ($this->reseau->voisins($noeud) as $voisin => $troncon) {
                $duree = $this->dureeTroncon($troncon, $heureNoeud, $vehicule);
                if ($duree === null) {
                    continue;
                }
                $total = $meilleur[$noeud] + $duree;
                if (!isset($meilleur[$voisin]) || $total < $meilleur[$voisin]) {
                    $meilleur[$voisin] = $total;
                    $distance[$voisin] = $distance[$noeud] + $troncon['distance'];
                    $precedent[$voisin] = $noeud;
                    $file->insert($voisin, -$total);
                }
            }
        }

        if (!isset($meilleur[$vers])) {
            return null;
        }
        $chemin = [$vers];
        $n = $vers;
        while (isset($precedent[$n])) {
            $n = $precedent[$n];
            array_unshift($chemin, $n);
        }
        return ['distance' => $distance[$vers], 'duree' => $meilleur[$vers], 'chemin' => $chemin, 'estime' => false];
    }

    /** Durée (minutes) de parcours d'un tronçon à un instant donné, ou null s'il est inutilisable. */
    private function dureeTroncon(array $troncon, int $heure, ContraintesVehicule $vehicule): ?float
    {
        $coefEtat = $this->config['coefficient_etat_route'][$troncon['etat']] ?? 1.0;
        if ($coefEtat === null) {
            return null; // impraticable
        }
        if ($vehicule->idType !== null && in_array($vehicule->idType, $troncon['interdits'], true)) {
            return null;
        }
        if ($troncon['fermeture'] !== null && Horaire::dansPlage($heure, $troncon['fermeture'][0], $troncon['fermeture'][1])) {
            return null;
        }
        $base = $troncon['duree'] ?? ($troncon['distance'] / $vehicule->vitesseKmh * 60);
        return $base * $coefEtat * Horaire::coefficientTrafic($heure, $this->config['coefficient_trafic']);
    }

    /** Estimation sans tronçon : vol d'oiseau × détour, ou distance par défaut. */
    private function estimerDirect(int $de, int $vers, int $heure, ContraintesVehicule $vehicule): array
    {
        $distance = $this->reseau->distanceHaversine($de, $vers);
        if ($distance !== null) {
            $distance *= (float) $this->config['facteur_detour'];
        } else {
            $defauts = $this->config['distance_defaut'];
            $distance = $this->reseau->memeZone($de, $vers) ? $defauts['meme_zone'] : $defauts['zones_differentes'];
        }
        $duree = $distance / $vehicule->vitesseKmh * 60 * Horaire::coefficientTrafic($heure, $this->config['coefficient_trafic']);
        return ['distance' => (float) $distance, 'duree' => $duree, 'chemin' => [$de, $vers], 'estime' => true];
    }

    // ------------------------------------------------------------------
    //  Ordonnancement
    // ------------------------------------------------------------------

    /** @param Arret[] $arrets */
    private function construireGlouton(array $arrets, int $quartierDepart, int $heureDepart, ContraintesVehicule $vehicule): array
    {
        $restants = $arrets;
        $sequence = [];
        $enleves = [];
        $courant = $quartierDepart;
        $t = $heureDepart;

        while (!empty($restants)) {
            $meilleurIdx = null;
            $meilleurScore = INF;
            $meilleurLeg = null;
            foreach ($restants as $idx => $arret) {
                if (!$arret->estEnlevement() && !isset($enleves[$arret->idCommande]) && $this->aEnlevement($arrets, $arret->idCommande)) {
                    continue; // colis pas encore récupéré
                }
                $leg = $this->plusCourtChemin($courant, $arret->idQuartier, $t, $vehicule);
                $score = $leg['duree'] + $leg['distance'];
                if (!$arret->estEnlevement() && $arret->echeance !== null) {
                    $marge = ($arret->echeance - ($t + $leg['duree'] * 60)) / 60; // minutes de marge
                    if ($marge < 0) {
                        $score -= 1e6;                      // déjà en retard : priorité absolue
                    } elseif ($marge < 60) {
                        $score -= (60 - $marge) * 2;        // urgence : favoriser les échéances proches
                    }
                }
                if ($score < $meilleurScore) {
                    $meilleurScore = $score;
                    $meilleurIdx = $idx;
                    $meilleurLeg = $leg;
                }
            }
            if ($meilleurIdx === null) {
                // précédence impossible à satisfaire (livraison sans enlèvement) : on prend le premier restant
                $meilleurIdx = array_key_first($restants);
                $meilleurLeg = $this->plusCourtChemin($courant, $restants[$meilleurIdx]->idQuartier, $t, $vehicule);
            }
            $choisi = $restants[$meilleurIdx];
            unset($restants[$meilleurIdx]);
            $sequence[] = $choisi;
            if ($choisi->estEnlevement()) {
                $enleves[$choisi->idCommande] = true;
            }
            $t = $vehicule->prochaineOuverture($t) + (int) round($meilleurLeg['duree'] * 60) + (int) $this->config['temps_service_minutes'] * 60;
            $courant = $choisi->idQuartier;
        }
        return $sequence;
    }

    /** Recherche locale : relocalisation d'un arrêt et inversion de segment (2-opt). */
    private function ameliorer(array $sequence, int $quartierDepart, int $heureDepart, ContraintesVehicule $vehicule): array
    {
        $n = count($sequence);
        if ($n < 3) {
            return $sequence;
        }
        $meilleurCout = $this->evaluer($sequence, $quartierDepart, $heureDepart, $vehicule)->coutPenalise;
        $iterations = 0;
        $max = (int) $this->config['iterations_max'];
        $ameliore = true;

        while ($ameliore && $iterations < $max) {
            $ameliore = false;
            // 1) relocalisation (or-opt à 1 élément)
            for ($i = 0; $i < $n && !$ameliore; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    if ($i === $j) {
                        continue;
                    }
                    $candidat = $sequence;
                    $elem = array_splice($candidat, $i, 1)[0];
                    array_splice($candidat, $j, 0, [$elem]);
                    if (!$this->precedenceRespectee($candidat)) {
                        continue;
                    }
                    $cout = $this->evaluer($candidat, $quartierDepart, $heureDepart, $vehicule)->coutPenalise;
                    if ($cout < $meilleurCout - 1e-6) {
                        $sequence = $candidat;
                        $meilleurCout = $cout;
                        $ameliore = true;
                        break;
                    }
                }
            }
            // 2) 2-opt : inversion d'un segment
            for ($i = 0; $i < $n - 1 && !$ameliore; $i++) {
                for ($j = $i + 1; $j < $n; $j++) {
                    $candidat = $sequence;
                    $segment = array_reverse(array_slice($candidat, $i, $j - $i + 1));
                    array_splice($candidat, $i, $j - $i + 1, $segment);
                    if (!$this->precedenceRespectee($candidat)) {
                        continue;
                    }
                    $cout = $this->evaluer($candidat, $quartierDepart, $heureDepart, $vehicule)->coutPenalise;
                    if ($cout < $meilleurCout - 1e-6) {
                        $sequence = $candidat;
                        $meilleurCout = $cout;
                        $ameliore = true;
                        break;
                    }
                }
            }
            $iterations++;
        }
        return $sequence;
    }

    /** @param Arret[] $sequence */
    private function precedenceRespectee(array $sequence): bool
    {
        $vus = [];
        foreach ($sequence as $arret) {
            if ($arret->estEnlevement()) {
                $vus[$arret->idCommande] = true;
            } elseif (!isset($vus[$arret->idCommande]) && $this->aEnlevement($sequence, $arret->idCommande)) {
                return false;
            }
        }
        return true;
    }

    private array $cacheEnlevement = [];

    /** @param Arret[] $arrets */
    private function aEnlevement(array $arrets, int $idCommande): bool
    {
        if (!isset($this->cacheEnlevement[$idCommande])) {
            $this->cacheEnlevement[$idCommande] = false;
            foreach ($arrets as $a) {
                if ($a->idCommande === $idCommande && $a->estEnlevement()) {
                    $this->cacheEnlevement[$idCommande] = true;
                    break;
                }
            }
        }
        return $this->cacheEnlevement[$idCommande];
    }

    private function ajouterAvertissements(ResultatItineraire $r, ContraintesVehicule $vehicule): void
    {
        $estimes = array_filter($r->etapes, fn ($e) => $e['estime'] && $e['distance_km'] > 0);
        if (!empty($estimes)) {
            $r->avertissements[] = count($estimes).' trajet(s) estimé(s) sans tronçon de route renseigné : la distance et la durée sont approximatives.';
        }
        if ($r->attenteMinutes > 0) {
            $r->avertissements[] = 'Le véhicule doit patienter '.Horaire::formatDuree($r->attenteMinutes).' en raison de sa plage horaire autorisée ('.implode(' → ', $vehicule->plageHoraire ?? []).').';
        }
        foreach ($r->etapes as $e) {
            if ($e['retard_minutes'] > 0) {
                $r->avertissements[] = 'Livraison de la commande n°'.$e['arret']->idCommande.' en retard de '.Horaire::formatDuree($e['retard_minutes']).' par rapport à la date promise.';
            }
        }
        if ($vehicule->capaciteColis !== null) {
            $nbCommandes = count(array_unique(array_map(fn ($e) => $e['arret']->idCommande, $r->etapes)));
            if ($nbCommandes > $vehicule->capaciteColis) {
                $r->avertissements[] = 'Capacité du véhicule dépassée : '.$nbCommandes.' commandes pour '.$vehicule->capaciteColis.' maximum.';
            }
        }
    }
}
