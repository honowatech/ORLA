<?php

namespace Tests\Feature;

use App\Services\Commandes\TarifLivraison;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreeDesDonnees;
use Tests\TestCase;

class TarifLivraisonTest extends TestCase
{
    use CreeDesDonnees;
    use RefreshDatabase;

    private TarifLivraison $tarif;

    /** @var array<string, int> */
    private array $q = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->tarif = new TarifLivraison;
        $centre = DB::table('zone')->insertGetId(['libelle' => 'Centre', 'id_ville' => 1, 'statut' => 1]);
        $nord = DB::table('zone')->insertGetId(['libelle' => 'Nord', 'id_ville' => 1, 'statut' => 1]);
        $sud = DB::table('zone')->insertGetId(['libelle' => 'Sud', 'id_ville' => 1, 'statut' => 1]);
        foreach (['akwa' => $centre, 'bonanjo' => $centre, 'bonaberi' => $nord, 'logbaba' => $sud, 'sans_zone' => null] as $nom => $zone) {
            $this->q[$nom] = DB::table('quartier')->insertGetId(['libelle' => $nom, 'id_ville' => 1, 'id_zone' => $zone]);
        }
        DB::table('montant_livraison')->insert(['id_zone_colis' => $centre, 'id_zone_livraison' => $nord, 'montant' => 1500]);
    }

    public function test_meme_zone_ou_zone_inconnue_donne_le_tarif_par_defaut(): void
    {
        $this->assertEquals(1000, $this->tarif->montant($this->q['akwa'], $this->q['bonanjo']));
        $this->assertEquals(1000, $this->tarif->montant($this->q['akwa'], $this->q['sans_zone']));
        $this->assertEquals(1000, $this->tarif->montant(null, $this->q['akwa']));
        $this->assertNull($this->tarif->grille($this->q['akwa'], $this->q['bonanjo']));
    }

    public function test_zones_differentes_utilisent_la_grille_dans_les_deux_sens(): void
    {
        $this->assertEquals(1500, $this->tarif->montant($this->q['akwa'], $this->q['bonaberi']));
        $this->assertEquals(1500, $this->tarif->montant($this->q['bonaberi'], $this->q['akwa']));
        $this->assertNotNull($this->tarif->grille($this->q['bonaberi'], $this->q['akwa']));
    }

    public function test_sans_grille_entre_deux_zones_il_n_y_a_pas_de_tarif(): void
    {
        $this->assertNull($this->tarif->montant($this->q['akwa'], $this->q['logbaba']));
    }

    public function test_le_point_d_entree_ajax_renvoie_le_tarif(): void
    {
        $admin = $this->staff('admin@example.com', 1);

        $this->actingAs($admin)->post(route('montant'), ['id_depart' => $this->q['akwa'], 'id_arrivee' => $this->q['bonaberi']])
            ->assertOk()->assertSee('1500');
    }
}
