<?php

namespace Tests\Unit\Routage;

use App\Services\Routage\Arret;
use App\Services\Routage\ContraintesVehicule;
use App\Services\Routage\Horaire;
use App\Services\Routage\ReseauRoutier;
use App\Services\Routage\RouteOptimizer;
use PHPUnit\Framework\TestCase;

class RouteOptimizerTest extends TestCase
{
    private const TYPE_CAMION = 9;

    /** Réseau linéaire 1-2-3-4-5-6 + raccourci 1-6 fermé le matin + 2-5 interdit aux camions. */
    private function reseau(): ReseauRoutier
    {
        $r = new ReseauRoutier();
        $coords = [1 => [3.86, 11.51], 2 => [3.87, 11.52], 3 => [3.88, 11.53], 4 => [3.89, 11.54], 5 => [3.90, 11.55], 6 => [3.91, 11.56]];
        foreach ($coords as $id => [$lat, $lng]) {
            $r->ajouterQuartier($id, 'Q'.$id, 1, $lat, $lng);
        }
        $r->ajouterQuartier(7, 'Q7 isolé', 2, 3.95, 11.60);
        $r->ajouterTroncon(1, 2, 2);
        $r->ajouterTroncon(2, 3, 2);
        $r->ajouterTroncon(3, 4, 2, null, 'degrade');
        $r->ajouterTroncon(4, 5, 2);
        $r->ajouterTroncon(5, 6, 2);
        $r->ajouterTroncon(1, 6, 4, null, 'bon', true, ['08:00', '10:00']);
        $r->ajouterTroncon(2, 5, 3, null, 'bon', true, null, [self::TYPE_CAMION]);
        return $r;
    }

    private function optimiseur(array $config = []): RouteOptimizer
    {
        return new RouteOptimizer($this->reseau(), $config);
    }

    public function test_plus_court_chemin_prend_le_raccourci_quand_il_est_ouvert(): void
    {
        $opt = $this->optimiseur();
        $moto = new ContraintesVehicule(idType: 1);
        $chemin = $opt->plusCourtChemin(1, 6, strtotime('2026-11-02 07:00'), $moto);
        $this->assertSame([1, 6], $chemin['chemin']);
        $this->assertEqualsWithDelta(4.0, $chemin['distance'], 0.001);
        $this->assertFalse($chemin['estime']);
    }

    public function test_tronçon_fermé_est_évité_pendant_sa_plage_de_fermeture(): void
    {
        $opt = $this->optimiseur();
        $moto = new ContraintesVehicule(idType: 1);
        $camion = new ContraintesVehicule(idType: self::TYPE_CAMION);
        // la moto contourne par le raccourci 2-5, le camion (interdit sur 2-5) suit toute la ligne
        $this->assertSame([1, 2, 5, 6], $opt->plusCourtChemin(1, 6, strtotime('2026-11-02 08:30'), $moto)['chemin']);
        $this->assertSame([1, 2, 3, 4, 5, 6], $opt->plusCourtChemin(1, 6, strtotime('2026-11-02 08:30'), $camion)['chemin']);
    }

    public function test_tronçon_interdit_au_type_de_véhicule_est_contourné(): void
    {
        $opt = $this->optimiseur();
        $heure = strtotime('2026-11-02 11:00');
        $camion = $opt->plusCourtChemin(2, 5, $heure, new ContraintesVehicule(idType: self::TYPE_CAMION));
        $moto = $opt->plusCourtChemin(2, 5, $heure, new ContraintesVehicule(idType: 1));
        $this->assertSame([2, 5], $moto['chemin']);
        $this->assertSame([2, 3, 4, 5], $camion['chemin']);
    }

    public function test_etat_de_route_dégradé_allonge_la_durée(): void
    {
        $opt = $this->optimiseur();
        $heure = strtotime('2026-11-02 11:00');
        $v = new ContraintesVehicule(vitesseKmh: 60);
        $bon = $opt->plusCourtChemin(4, 5, $heure, $v);        // 2 km bon état → 2 min
        $degrade = $opt->plusCourtChemin(3, 4, $heure, $v);    // 2 km dégradé → 2 × 1.6 min
        $this->assertEqualsWithDelta(2.0, $bon['duree'], 0.01);
        $this->assertEqualsWithDelta(3.2, $degrade['duree'], 0.01);
    }

    public function test_trafic_aux_heures_de_pointe_est_pris_en_compte(): void
    {
        $opt = $this->optimiseur(['coefficient_trafic' => ['06:30-09:00' => 1.5]]);
        $v = new ContraintesVehicule(vitesseKmh: 60);
        $pointe = $opt->plusCourtChemin(4, 5, strtotime('2026-11-02 07:30'), $v);
        $creux = $opt->plusCourtChemin(4, 5, strtotime('2026-11-02 11:00'), $v);
        $this->assertEqualsWithDelta(3.0, $pointe['duree'], 0.01);
        $this->assertEqualsWithDelta(2.0, $creux['duree'], 0.01);
    }

    public function test_quartier_sans_tronçon_est_estimé_à_vol_d_oiseau(): void
    {
        $opt = $this->optimiseur(['facteur_detour' => 1.3]);
        $chemin = $opt->plusCourtChemin(6, 7, strtotime('2026-11-02 11:00'), new ContraintesVehicule());
        $this->assertTrue($chemin['estime']);
        $this->assertSame([6, 7], $chemin['chemin']);
        $this->assertGreaterThan(5, $chemin['distance']);
        $this->assertLessThan(12, $chemin['distance']);
    }

    public function test_plage_horaire_du_véhicule_impose_une_attente(): void
    {
        $opt = $this->optimiseur();
        $camion = new ContraintesVehicule(idType: self::TYPE_CAMION, plageHoraire: ['06:00', '20:00']);
        $arrets = [new Arret(1, Arret::LIVRAISON, 3)];
        $res = $opt->optimiser($arrets, 1, strtotime('2026-11-02 05:00'), $camion);
        $this->assertEqualsWithDelta(60, $res->attenteMinutes, 0.5);
        $this->assertSame('06:00', date('H:i', $res->etapes[0]['heure_arrivee'] - (int) round($res->etapes[0]['duree_minutes'] * 60)));
        $this->assertNotEmpty(array_filter($res->avertissements, fn ($a) => str_contains($a, 'patienter')));
    }

    public function test_enlèvement_précède_toujours_la_livraison_de_la_même_commande(): void
    {
        $opt = $this->optimiseur();
        $depart = strtotime('2026-11-02 10:00');
        $arrets = [
            new Arret(10, Arret::LIVRAISON, 2, $depart + 7200),
            new Arret(10, Arret::ENLEVEMENT, 6),
            new Arret(11, Arret::LIVRAISON, 5, $depart + 7200),
            new Arret(11, Arret::ENLEVEMENT, 3),
            new Arret(12, Arret::LIVRAISON, 4, $depart + 7200),
        ];
        $res = $opt->optimiser($arrets, 1, $depart, new ContraintesVehicule());
        $this->assertCount(5, $res->etapes);
        $positions = [];
        foreach ($res->etapes as $e) {
            $positions[$e['arret']->cle()] = $e['ordre'];
        }
        $this->assertLessThan($positions['livraison#10'], $positions['enlevement#10']);
        $this->assertLessThan($positions['livraison#11'], $positions['enlevement#11']);
    }

    public function test_optimisation_ne_fait_jamais_pire_que_l_ordre_de_référence(): void
    {
        $opt = $this->optimiseur();
        $depart = strtotime('2026-11-02 10:00');
        $v = new ContraintesVehicule(coutKm: 100, coutHeure: 1500);
        // ordre volontairement mauvais : aller-retour permanent
        $arrets = [
            new Arret(1, Arret::LIVRAISON, 6),
            new Arret(2, Arret::LIVRAISON, 2),
            new Arret(3, Arret::LIVRAISON, 5),
            new Arret(4, Arret::LIVRAISON, 3),
            new Arret(5, Arret::LIVRAISON, 4),
        ];
        $reference = $opt->evaluer($arrets, 1, $depart, $v);
        $optimise = $opt->optimiser($arrets, 1, $depart, $v);
        $this->assertLessThanOrEqual($reference->coutPenalise, $optimise->coutPenalise);
        $this->assertLessThan($reference->distanceKm, $optimise->distanceKm);
        // la meilleure séquence sur une ligne est 2,3,4,5,6
        $this->assertSame([2, 3, 4, 5, 6], array_map(fn ($e) => $e['arret']->idQuartier, $optimise->etapes));
    }

    public function test_livraison_urgente_est_servie_avant_les_autres(): void
    {
        $opt = $this->optimiseur(['penalite_retard_par_minute' => 1000]);
        $depart = strtotime('2026-11-02 10:00');
        $v = new ContraintesVehicule(vitesseKmh: 30, coutKm: 100, coutHeure: 1500);
        $arrets = [
            new Arret(1, Arret::LIVRAISON, 2, null),
            new Arret(2, Arret::LIVRAISON, 6, $depart + 20 * 60), // échéance serrée au bout de la ligne
        ];
        $res = $opt->optimiser($arrets, 1, $depart, $v);
        $this->assertSame(2, $res->etapes[0]['arret']->idCommande);
    }

    public function test_horaire_gère_les_plages_à_cheval_sur_minuit(): void
    {
        $this->assertTrue(Horaire::dansPlage(strtotime('2026-11-02 23:30'), '22:00', '06:00'));
        $this->assertTrue(Horaire::dansPlage(strtotime('2026-11-02 03:00'), '22:00', '06:00'));
        $this->assertFalse(Horaire::dansPlage(strtotime('2026-11-02 12:00'), '22:00', '06:00'));
        $this->assertSame('1h30', Horaire::formatDuree(90));
        $this->assertSame('12 min', Horaire::formatDuree(12.4));
    }

    public function test_résultat_vide_sans_arrêts(): void
    {
        $res = $this->optimiseur()->optimiser([], 1, strtotime('2026-11-02 10:00'), new ContraintesVehicule());
        $this->assertSame([], $res->etapes);
        $this->assertSame(0.0, $res->distanceKm);
    }
}
