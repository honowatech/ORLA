<?php

namespace Tests\Feature;

use App\Models\Parc\Vehicule_attribution;
use App\Models\Parc\Vehicule_maintenance;
use App\Models\Type_vehicule;
use App\Models\User;
use App\Models\Vehicule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParcAutomobileTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Vehicule $vehicule;
    protected int $id_coursier;

    protected function setUp(): void
    {
        parent::setUp();

        // Espace client (tenant) actif avec abonnement valide pour le middleware Check_Sa_Client_Error.
        $id_abonnement = DB::table('super_admin_abonnement')->insertGetId([
            'titre' => 'Mensuel', 'accumulateur' => 1, 'type_periode' => 'mois', 'montant' => 10000,
            'periode_grace' => 5, 'statut' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('super_admin_client')->insert([
            'name' => 'Speedex Test', 'id_abonnement' => $id_abonnement, 'statut' => 1,
            'date_fin' => Carbon::now()->addMonth(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        $id_type_admin = DB::table('type_utilisateur')->insertGetId(['libelle' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('type_utilisateur')->insert(['libelle' => 'Coursier', 'created_at' => now(), 'updated_at' => now()]);
        $id_ville = DB::table('ville')->insertGetId(['libelle' => 'Douala', 'code' => 'Dla', 'created_at' => now(), 'updated_at' => now()]);

        $this->admin = User::create([
            'email' => 'admin@test.com', 'password' => Hash::make('secret123'),
            'id_type_utilisateur' => $id_type_admin, 'statut' => 1, 'noms' => 'Admin',
        ]);

        $this->id_coursier = DB::table('coursiers')->insertGetId([
            'noms' => 'Mbarga', 'prenoms' => 'Jean', 'telephone' => '690000000', 'statut' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $type = Type_vehicule::create(['libelle' => 'Moto', 'statut' => 1]);
        $this->vehicule = Vehicule::create([
            'immatriculation' => 'LT-123-AB', 'marque' => 'Haojue', 'modele' => 'HJ125',
            'id_type' => $type->id, 'id_ville' => $id_ville, 'statut' => 1, 'etat' => 'disponible', 'kilometrage' => 1000,
        ]);
    }

    public function test_le_tableau_de_bord_du_parc_est_accessible(): void
    {
        $this->actingAs($this->admin)->get(route('parc.index'))
            ->assertOk()
            ->assertSee('Tableau de bord du parc')
            ->assertSee('Véhicules actifs');
    }

    public function test_la_fiche_vehicule_affiche_les_onglets(): void
    {
        $this->actingAs($this->admin)->get(route('vehicule.show', $this->vehicule->id))
            ->assertOk()
            ->assertSee('Fiche véhicule')
            ->assertSee('onglet_documents', false)
            ->assertSee('onglet_entretiens', false)
            ->assertSee('onglet_carburant', false);
    }

    public function test_un_document_expire_genere_une_alerte(): void
    {
        $this->actingAs($this->admin)->post(route('vehicule_document.store'), [
            'id_vehicule' => $this->vehicule->id,
            'type' => 'assurance',
            'numero' => 'POL-001',
            'date_debut' => Carbon::now()->subYear()->toDateString(),
            'date_expiration' => Carbon::now()->subDays(3)->toDateString(),
            'montant' => 45000,
        ])->assertRedirect();

        $this->assertDatabaseHas('vehicule_document', ['id_vehicule' => $this->vehicule->id, 'numero' => 'POL-001']);

        $this->actingAs($this->admin)->get(route('parc.alertes'))
            ->assertOk()
            ->assertSee('Assurance')
            ->assertSee('Expiré depuis 3 jour(s)');
    }

    public function test_un_entretien_realise_met_a_jour_le_kilometrage_et_planifie_le_suivant(): void
    {
        $this->actingAs($this->admin)->post(route('vehicule_maintenance.store'), [
            'id_vehicule' => $this->vehicule->id,
            'type' => 'vidange',
            'libelle' => 'Vidange moteur',
            'statut' => 'realise',
            'date_realisation' => Carbon::now()->toDateString(),
            'kilometrage' => 1500,
            'cout' => 8000,
            'prochaine_date' => Carbon::now()->addMonths(3)->toDateString(),
            'prochain_kilometrage' => 4500,
            'planifier_suivant' => 1,
        ])->assertRedirect();

        $this->assertEquals(1500, $this->vehicule->fresh()->kilometrage);
        $this->assertEquals(2, Vehicule_maintenance::where('id_vehicule', $this->vehicule->id)->count());
        $this->assertDatabaseHas('vehicule_maintenance', ['id_vehicule' => $this->vehicule->id, 'statut' => 'planifie', 'prochain_kilometrage' => 4500]);
        $this->assertDatabaseHas('vehicule_kilometrage', ['id_vehicule' => $this->vehicule->id, 'kilometrage' => 1500, 'source' => 'maintenance']);
    }

    public function test_un_entretien_en_cours_passe_le_vehicule_en_maintenance(): void
    {
        $this->actingAs($this->admin)->post(route('vehicule_maintenance.store'), [
            'id_vehicule' => $this->vehicule->id,
            'type' => 'reparation',
            'libelle' => 'Changement embrayage',
            'statut' => 'en_cours',
        ])->assertRedirect();
        $this->assertEquals('en_maintenance', $this->vehicule->fresh()->etat);
    }

    public function test_les_pleins_calculent_la_consommation_et_les_couts(): void
    {
        foreach ([[1000, 5, 4000], [1200, 6, 4800], [1400, 6, 4800]] as [$km, $litres, $montant]) {
            $this->actingAs($this->admin)->post(route('vehicule_carburant.store'), [
                'id_vehicule' => $this->vehicule->id,
                'date' => Carbon::now()->toDateString(),
                'litres' => $litres,
                'montant' => $montant,
                'kilometrage' => $km,
                'plein_complet' => 1,
            ])->assertRedirect();
        }
        $vehicule = $this->vehicule->fresh();
        $this->assertEquals(1400, $vehicule->kilometrage);
        $this->assertEquals(3.0, $vehicule->consommationMoyenne()); // 12 L sur 400 km
        $this->assertEquals(13600.0, $vehicule->coutCarburant());

        $this->actingAs($this->admin)->get(route('parc.couts'))->assertOk()->assertSee('13 600');
    }

    public function test_un_incident_immobilisant_change_l_etat_du_vehicule(): void
    {
        $this->actingAs($this->admin)->post(route('vehicule_incident.store'), [
            'id_vehicule' => $this->vehicule->id,
            'type' => 'accident',
            'date' => Carbon::now()->format('Y-m-d\TH:i'),
            'description' => 'Collision légère au carrefour',
            'statut' => 'ouvert',
            'immobilise' => 1,
            'cout' => 25000,
        ])->assertRedirect();
        $this->assertEquals('immobilise', $this->vehicule->fresh()->etat);

        $incident = $this->vehicule->incidents()->first();
        $this->actingAs($this->admin)->put(route('vehicule_incident.update', $incident->id), [
            'type' => 'accident',
            'date' => Carbon::now()->format('Y-m-d\TH:i'),
            'description' => 'Collision légère au carrefour',
            'statut' => 'resolu',
            'date_resolution' => Carbon::now()->toDateString(),
            'immobilise' => 1,
            'cout' => 25000,
        ])->assertRedirect();
        $this->assertEquals('disponible', $this->vehicule->fresh()->etat);
    }

    public function test_l_attribution_est_historisee_et_la_restitution_la_cloture(): void
    {
        $this->actingAs($this->admin)->post(route('vehicule.attribuer', $this->vehicule->id), [
            'id_coursier' => $this->id_coursier,
            'kilometrage' => 1000,
        ])->assertRedirect(route('vehicule.show', $this->vehicule->id));

        $vehicule = $this->vehicule->fresh();
        $this->assertEquals($this->id_coursier, $vehicule->id_coursier);
        $this->assertEquals('en_service', $vehicule->etat);
        $this->assertEquals(1, Vehicule_attribution::whereNull('date_fin')->count());

        $this->actingAs($this->admin)->delete(route('vehicule.destroy', $this->vehicule->id), [
            'attribuate' => 0,
            'kilometrage' => 1250,
        ])->assertRedirect();

        $vehicule = $this->vehicule->fresh();
        $this->assertNull($vehicule->id_coursier);
        $this->assertEquals('disponible', $vehicule->etat);
        $attribution = Vehicule_attribution::first();
        $this->assertNotNull($attribution->date_fin);
        $this->assertEquals(250, $attribution->distance);
    }

    public function test_l_export_csv_contient_les_vehicules(): void
    {
        $response = $this->actingAs($this->admin)->get(route('parc.export'));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('LT-123-AB', $response->streamedContent());
    }

    public function test_un_vehicule_avec_historique_ne_peut_pas_etre_supprime(): void
    {
        $this->vehicule->carburants()->create(['date' => now()->toDateString(), 'litres' => 5, 'montant' => 4000]);
        $this->actingAs($this->admin)->delete(route('vehicule.destroy', $this->vehicule->id), ['type' => 'delete'])->assertRedirect();
        $this->assertDatabaseHas('vehicule', ['id' => $this->vehicule->id]);
    }

    public function test_les_pages_globales_du_parc_repondent(): void
    {
        foreach (['vehicule_document.index', 'vehicule_maintenance.index', 'vehicule_carburant.index', 'vehicule_incident.index', 'parc.alertes', 'parc.couts'] as $route) {
            $this->actingAs($this->admin)->get(route($route))->assertOk();
        }
    }
}
