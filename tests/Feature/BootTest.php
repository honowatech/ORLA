<?php

namespace Tests\Feature;

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
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/home')->assertRedirect(route('login'));
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_les_anciennes_pages_d_erreur_de_licence_ont_disparu(): void
    {
        $this->get('/Sc-full_error')->assertNotFound();
        $this->get('/Sc-no_abonnement')->assertNotFound();
    }
}
