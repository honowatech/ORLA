<?php

namespace Tests\Feature\Tenancy;

use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Coursiers\Coursiers;
use App\Models\Entreprise;
use App\Models\Informations_personnels\Informations_personnels;
use App\Models\Quartier\Quartier;
use App\Models\User;
use App\Models\Zone\Zone;
use App\Tenancy\CurrentEntreprise;
use Database\Seeders\ReferentielSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Deux entreprises abonnées : un administrateur ne voit et n'atteint que les
 * données de la sienne, à travers les contrôleurs existants (non modifiés).
 */
class IsolationTest extends TestCase
{
    use RefreshDatabase;

    private Entreprise $alpha;

    private Entreprise $beta;

    private User $adminAlpha;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferentielSeeder::class);

        $this->alpha = Entreprise::factory()->abonnee()->create(['name' => 'Alpha Express']);
        $this->beta = Entreprise::factory()->abonnee()->create(['name' => 'Beta Livraison']);
        $this->adminAlpha = User::factory()->admin()->create(['entreprise_id' => $this->alpha->id]);
    }

    public function test_la_liste_des_clients_ne_montre_que_ceux_de_l_entreprise(): void
    {
        Clients::factory()->create(['entreprise_id' => $this->alpha->id, 'noms' => 'ClientAlpha']);
        $clientBeta = Clients::factory()->create(['entreprise_id' => $this->beta->id, 'noms' => 'ClientBeta']);

        $this->actingAs($this->adminAlpha)->get('/admin/clients')
            ->assertOk()
            ->assertSee('ClientAlpha')
            ->assertDontSee('ClientBeta');

        $this->actingAs($this->adminAlpha)->get("/admin/clients/{$clientBeta->id}")->assertNotFound();
    }

    public function test_la_liste_des_commandes_ne_montre_que_celles_de_l_entreprise(): void
    {
        $this->commandePour($this->alpha, 'CommandeAlpha');
        $commandeBeta = $this->commandePour($this->beta, 'CommandeBeta');

        $this->actingAs($this->adminAlpha)->get('/admin/commandes')
            ->assertOk()
            ->assertSee('CommandeAlpha')
            ->assertDontSee('CommandeBeta');

        $this->actingAs($this->adminAlpha)->get("/admin/commandes/{$commandeBeta->id}")->assertNotFound();
    }

    public function test_les_fiches_coursier_et_zone_d_une_autre_entreprise_sont_introuvables(): void
    {
        $coursierBeta = $this->coursierPour($this->beta);
        $zoneBeta = Zone::factory()->create(['entreprise_id' => $this->beta->id, 'id_ville' => 1]);
        $coursierAlpha = $this->coursierPour($this->alpha);

        $this->actingAs($this->adminAlpha)->get("/multi/coursiers/{$coursierAlpha->id}")->assertOk();
        $this->actingAs($this->adminAlpha)->get("/multi/coursiers/{$coursierBeta->id}")->assertNotFound();
        $this->actingAs($this->adminAlpha)->get("/multi/zone/{$zoneBeta->id}")->assertNotFound();
    }

    public function test_les_requetes_sont_cloisonnees_dans_le_contexte_de_chaque_entreprise(): void
    {
        Coursiers::factory()->count(3)->create(['entreprise_id' => $this->alpha->id]);
        Coursiers::factory()->count(2)->create(['entreprise_id' => $this->beta->id]);

        $courante = app(CurrentEntreprise::class);

        $this->assertSame(3, $courante->runAs($this->alpha, fn () => Coursiers::count()));
        $this->assertSame(2, $courante->runAs($this->beta, fn () => Coursiers::count()));
        $this->assertSame(5, $courante->runWithoutTenancy(fn () => Coursiers::count()));
    }

    public function test_un_enregistrement_cree_depuis_l_espace_appartient_a_l_entreprise(): void
    {
        $this->actingAs($this->adminAlpha)->get('/admin/commandes')->assertOk();

        // Le contrôleur crée à la volée le quartier « Speedex » : il doit être rattaché à Alpha.
        $this->assertDatabaseHas('quartier', ['libelle' => 'Speedex', 'entreprise_id' => $this->alpha->id]);
        $this->assertDatabaseMissing('quartier', ['libelle' => 'Speedex', 'entreprise_id' => $this->beta->id]);
    }

    /**
     * Un coursier et son compte utilisateur, liés dans les deux sens (la fiche coursier
     * lit la ville du compte).
     */
    private function coursierPour(Entreprise $entreprise): Coursiers
    {
        $coursier = Coursiers::factory()->create(['entreprise_id' => $entreprise->id]);
        $compte = User::factory()->coursier()->create([
            'entreprise_id' => $entreprise->id, 'id_coursier' => $coursier->id, 'id_ville' => 1,
        ]);
        $coursier->forceFill(['id_utilisateur' => $compte->id])->save();
        // La fiche coursier suppose une ligne d'informations personnelles.
        Informations_personnels::query()->forceCreate(['entreprise_id' => $entreprise->id, 'id_coursier' => $coursier->id]);

        return $coursier;
    }

    /**
     * Commande « simple » sans compte client : la liste affiche alors nom_client.
     */
    private function commandePour(Entreprise $entreprise, string $nomClient): Commandes
    {
        $quartier = Quartier::factory()->create(['entreprise_id' => $entreprise->id, 'id_ville' => 1]);

        return Commandes::factory()->create([
            'entreprise_id' => $entreprise->id,
            'nom_client' => $nomClient,
            'id_client' => null,
            'id_quartier_colis' => $quartier->id,
            'id_quartier_livraison' => $quartier->id,
        ]);
    }
}
