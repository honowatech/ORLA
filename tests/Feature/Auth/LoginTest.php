<?php

namespace Tests\Feature\Auth;

use App\Models\Entreprise;
use App\Models\User;
use App\Tenancy\CurrentEntreprise;
use Database\Seeders\DemoSeeder;
use Database\Seeders\ReferentielSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_connexion_resout_l_entreprise_de_l_utilisateur(): void
    {
        $admin = $this->utilisateurDemo();

        $this->post('/login', ['email' => DemoSeeder::ADMIN_EMAIL, 'password' => DemoSeeder::MOT_DE_PASSE])
            ->assertRedirect('/home');
        $this->assertAuthenticatedAs($admin);

        $this->get('/admin')->assertOk();
        $this->assertSame((int) $admin->entreprise_id, app(CurrentEntreprise::class)->id());
    }

    public function test_la_deconnexion_oublie_l_entreprise_courante(): void
    {
        $admin = $this->utilisateurDemo();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->assertTrue(app(CurrentEntreprise::class)->has());

        $this->post('/logout');

        $this->assertGuest();
        $this->assertFalse(app(CurrentEntreprise::class)->has());
    }

    public function test_un_utilisateur_sans_entreprise_est_deconnecte(): void
    {
        $this->seed(ReferentielSeeder::class);
        $utilisateur = User::factory()->create();
        DB::table('users')->where('id', $utilisateur->id)->update(['entreprise_id' => null]);

        $this->actingAs($utilisateur->fresh())->get('/home')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_un_utilisateur_d_une_entreprise_supprimee_est_deconnecte(): void
    {
        $this->seed(ReferentielSeeder::class);
        $entreprise = Entreprise::factory()->create();
        $utilisateur = User::factory()->create(['entreprise_id' => $entreprise->id]);
        $entreprise->delete();

        $this->actingAs($utilisateur)->get('/home')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
