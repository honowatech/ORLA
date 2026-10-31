<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seed an active super-admin client with a valid subscription so the
     * Check_Sa_Client_Error middleware lets requests through.
     */
    private function seedActiveClient(): void
    {
        $abonnementId = DB::table('super_admin_abonnement')->insertGetId([
            'titre' => 'Test',
            'type_periode' => 'mois',
            'statut' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('super_admin_client')->insert([
            'name' => 'Test client',
            'id_abonnement' => $abonnementId,
            'date_fin' => now()->addMonth(),
            'statut' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_login_page_is_reachable(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login_from_home(): void
    {
        $this->seedActiveClient();

        $this->get('/')->assertRedirect(route('login'));
    }
}
