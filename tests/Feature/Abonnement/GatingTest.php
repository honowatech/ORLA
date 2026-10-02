<?php

namespace Tests\Feature\Abonnement;

use App\Enums\EtatAbonnement;
use App\Models\Entreprise;
use App\Models\User;
use App\Services\Abonnement\AbonnementService;
use Database\Seeders\ReferentielSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le blocage de l'espace de travail est évalué par entreprise, à chaque requête.
 */
class GatingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferentielSeeder::class);
        $this->seed(SuperAdminSeeder::class);
    }

    public function test_le_service_calcule_l_etat_d_abonnement(): void
    {
        $service = app(AbonnementService::class);

        $this->assertSame(EtatAbonnement::Essai, $service->etat(Entreprise::factory()->create()));
        $this->assertSame(EtatAbonnement::Active, $service->etat(Entreprise::factory()->abonnee()->create()));
        $this->assertSame(EtatAbonnement::Grace, $service->etat(Entreprise::factory()->enGrace()->create()));
        $this->assertSame(EtatAbonnement::Expiree, $service->etat(Entreprise::factory()->expiree()->create()));
        $this->assertSame(EtatAbonnement::Expiree, $service->etat(Entreprise::factory()->essaiExpire()->create()));
        $this->assertSame(EtatAbonnement::Suspendue, $service->etat(Entreprise::factory()->abonnee()->suspendue()->create()));
    }

    public function test_une_entreprise_en_essai_active_ou_en_grace_accede_a_son_espace(): void
    {
        foreach ([Entreprise::factory(), Entreprise::factory()->abonnee(), Entreprise::factory()->enGrace()] as $factory) {
            $admin = $this->admin($factory->create());
            $this->actingAs($admin)->get('/admin')->assertOk();
        }
    }

    public function test_un_essai_expire_renvoie_l_administrateur_vers_la_souscription(): void
    {
        $admin = $this->admin(Entreprise::factory()->essaiExpire()->create());

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('Sc-transaction.create'));
        $this->actingAs($admin)->get('/Sc-transaction/create')->assertOk();
    }

    public function test_un_abonnement_expire_bloque_l_espace_selon_le_role(): void
    {
        $entreprise = Entreprise::factory()->expiree()->create(['name' => 'Gamma Colis']);
        $admin = $this->admin($entreprise);
        $coursier = $this->coursier($entreprise);

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('Sc-transaction.create'));
        $this->actingAs($coursier)->get('/home')->assertRedirect(route('abonnement.expire'));

        $this->actingAs($coursier)->get('/abonnement/expire')
            ->assertOk()
            ->assertSee('Gamma Colis')
            ->assertDontSee("S'abonner", false);

        $this->actingAs($admin)->get('/abonnement/expire')->assertOk()->assertSee("S'abonner", false);
    }

    public function test_une_entreprise_suspendue_est_bloquee_pour_tous_les_roles(): void
    {
        $entreprise = Entreprise::factory()->abonnee()->suspendue()->create(['name' => 'Delta Express']);

        $this->actingAs($this->admin($entreprise))->get('/admin')->assertRedirect(route('abonnement.suspendue'));
        $this->actingAs($this->coursier($entreprise))->get('/home')->assertRedirect(route('abonnement.suspendue'));
        $this->actingAs($this->admin($entreprise))->get('/abonnement/suspendue')->assertOk()->assertSee('Delta Express');
        $this->actingAs($this->admin($entreprise))->get('/abonnement/expire')->assertRedirect(route('abonnement.suspendue'));
    }

    public function test_les_pages_de_blocage_renvoient_vers_l_espace_quand_l_acces_est_permis(): void
    {
        $admin = $this->admin(Entreprise::factory()->abonnee()->create());

        $this->actingAs($admin)->get('/abonnement/expire')->assertRedirect(route('home'));
        $this->actingAs($admin)->get('/abonnement/suspendue')->assertRedirect(route('home'));
    }

    public function test_un_compte_desactive_est_ecarte_avant_le_controle_d_abonnement(): void
    {
        $admin = $this->admin(Entreprise::factory()->expiree()->create());
        $admin->forceFill(['statut' => 0])->save();

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('home.error'));
    }

    private function admin(Entreprise $entreprise): User
    {
        return User::factory()->admin()->create(['entreprise_id' => $entreprise->id]);
    }

    private function coursier(Entreprise $entreprise): User
    {
        return User::factory()->coursier()->create(['entreprise_id' => $entreprise->id]);
    }
}
