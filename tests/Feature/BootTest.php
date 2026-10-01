<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ReferentielSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BootTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_de_connexion_est_accessible(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_la_page_de_connexion_super_admin_est_accessible(): void
    {
        $this->get('/superadmin/Sa-login')->assertOk();
    }

    public function test_la_route_de_reinitialisation_du_super_admin_a_disparu(): void
    {
        $this->get('/teston')->assertNotFound();
    }

    public function test_l_inscription_publique_laravel_ui_est_desactivee(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_un_visiteur_non_connecte_est_renvoye_vers_la_connexion(): void
    {
        // Le middleware « auth » a une priorité framework supérieure au contrôle
        // de licence : il s'exécute en premier.
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_sans_licence_un_utilisateur_connecte_est_renvoye_vers_la_page_d_erreur(): void
    {
        // Référentiels seuls (types d'utilisateur), sans licence ni comptes de démonstration.
        $this->seed(ReferentielSeeder::class);
        $utilisateur = User::factory()->admin()->create();

        $this->actingAs($utilisateur)->get('/')->assertRedirect(route('SuperAdmin.empty_client'));
    }
}
