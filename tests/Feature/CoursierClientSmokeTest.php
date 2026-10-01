<?php

namespace Tests\Feature;

use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursierClientSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_coursier_accede_a_son_tableau_de_bord(): void
    {
        $coursier = $this->utilisateurDemo(DemoSeeder::COURSIER_EMAIL);

        $this->actingAs($coursier)->get('/home')->assertRedirect(route('home.coursier'));
        $this->actingAs($coursier)->get('/coursier')->assertOk();
    }

    public function test_le_client_accede_a_son_tableau_de_bord(): void
    {
        $client = $this->utilisateurDemo(DemoSeeder::CLIENT_EMAIL);

        $this->actingAs($client)->get('/home')->assertRedirect(route('home.client'));
        $this->actingAs($client)->get('/')->assertOk();
    }

    public function test_un_coursier_ne_peut_pas_ouvrir_l_espace_admin(): void
    {
        $coursier = $this->utilisateurDemo(DemoSeeder::COURSIER_EMAIL);

        $this->actingAs($coursier)->get('/admin')->assertRedirect(route('home.coursier'));
    }
}
