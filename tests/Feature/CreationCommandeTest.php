<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreeDesDonnees;
use Tests\TestCase;

class CreationCommandeTest extends TestCase
{
    use CreeDesDonnees;
    use RefreshDatabase;

    public function test_un_client_cree_une_commande_au_tarif_calcule_par_le_serveur(): void
    {
        $alice = $this->client('alice@example.com');
        $centre = DB::table('zone')->insertGetId(['libelle' => 'Centre', 'id_ville' => 1, 'statut' => 1]);
        $nord = DB::table('zone')->insertGetId(['libelle' => 'Nord', 'id_ville' => 1, 'statut' => 1]);
        $akwa = DB::table('quartier')->insertGetId(['libelle' => 'Akwa', 'id_ville' => 1, 'id_zone' => $centre]);
        $bonaberi = DB::table('quartier')->insertGetId(['libelle' => 'Bonaberi', 'id_ville' => 1, 'id_zone' => $nord]);
        $grille = DB::table('montant_livraison')->insertGetId(['id_zone_colis' => $centre, 'id_zone_livraison' => $nord, 'montant' => 1500]);

        $this->actingAs($alice)->post(route('Clientcommandes.store'), [
            'type_commande' => 'simple',
            'contact_colis' => '690000001', 'lieu_collecte' => 'Akwa', 'ville_collecte' => 1, 'id_quartier_colis' => $akwa,
            'contact_livraison' => '690000002', 'lieu_livraison' => 'Bonaberi', 'ville_livraison' => 1, 'id_quartier_livraison' => $bonaberi,
            'montant_livraison' => 50, // ignoré : recalculé par le serveur
            'date' => '2026-10-01', 'time' => '10:00',
            'description' => 'Colis de test fragile', 'mode_de_paiement' => 'coursier',
        ])->assertRedirect();

        $commande = DB::table('commandes')->first();
        $this->assertNotNull($commande);
        $this->assertEquals($alice->id_client, $commande->id_client);
        $this->assertEquals(1500, $commande->montant_livraison);
        $this->assertEquals($grille, $commande->id_montant_livraison);
        $this->assertSame('attente', $commande->statut);
    }
}
