<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreeDesDonnees;
use Tests\TestCase;

/**
 * Middlewares role et superadmin : chaque espace n'est accessible qu'aux
 * rôles prévus, les autres sont renvoyés vers leur propre espace.
 */
class AccesParRoleTest extends TestCase
{
    use CreeDesDonnees;
    use RefreshDatabase;

    public function test_un_visiteur_est_renvoye_vers_la_connexion(): void
    {
        $this->get('/admin/users')->assertRedirect(route('login'));
        $this->get('/coursier')->assertRedirect(route('login'));
    }

    public function test_chaque_role_est_renvoye_vers_son_espace(): void
    {
        $client = $this->client('client@example.com');
        $coursier = $this->coursier('coursier@example.com');
        $agent = $this->staff('agent@example.com', 2);

        $this->actingAs($client)->get('/admin/users')->assertRedirect(route('home.client'));
        $this->actingAs($client)->get('/multi/zone')->assertRedirect(route('home.client'));
        $this->actingAs($client)->get('/coursier')->assertRedirect(route('home.client'));

        $this->actingAs($coursier)->get('/admin/users')->assertRedirect(route('home.coursier'));
        $this->actingAs($coursier)->get(route('Clientcommandes.index'))->assertRedirect(route('home.coursier'));

        $this->actingAs($agent)->get('/coursier')->assertRedirect(route('home.admin'));
        $this->actingAs($agent)->get(route('Clientcommandes.index'))->assertRedirect(route('home.admin'));
    }

    public function test_chaque_role_accede_a_son_espace(): void
    {
        $this->actingAs($this->staff('agent@example.com', 2))->get('/admin/users')->assertOk();
        $this->actingAs($this->staff('admin@example.com', 1))->get('/multi/type_vehicule')->assertOk();
    }

    public function test_admin_et_agent_affichent_la_liste_des_commandes(): void
    {
        $this->actingAs($this->staff('admin@example.com', 1))->get(route('commandes.index'))->assertOk();

        $idAgent = DB::table('agents')->insertGetId([
            'noms' => 'Agent', 'prenoms' => 'Test', 'telephone' => '690000009', 'statut' => 1,
        ]);
        $agent = $this->staff('agent@example.com', 2);
        $agent->forceFill(['id_agent' => $idAgent])->save();

        $this->actingAs($agent)->get(route('commandes.index'))->assertOk();
    }

    public function test_un_compte_desactive_est_renvoye_vers_la_page_d_erreur(): void
    {
        $agent = $this->staff('agent@example.com', 2);
        $agent->statut = 0;
        $agent->save();

        $this->actingAs($agent)->get('/admin/users')->assertRedirect(route('home.error'));
    }

    public function test_attribuer_un_agent_n_est_plus_ouvert_aux_clients(): void
    {
        $client = $this->client('client@example.com');

        $this->actingAs($client)->post(route('commande_agent'))->assertRedirect(route('home.client'));
    }

    public function test_l_espace_super_admin_exige_la_session_super_admin(): void
    {
        $this->get(route('SuperAdmin.home'))->assertRedirect(route('SuperAdmin.login'));
        $this->get(route('Sa-client.index'))->assertRedirect(route('SuperAdmin.login'));
        $this->get(route('SuperAdmin.login'))->assertOk();
    }
}
