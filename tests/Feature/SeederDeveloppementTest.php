<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le seeder de développement doit donner une application utilisable :
 * chaque compte créé se connecte et atteint son espace.
 */
class SeederDeveloppementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['SEED_PASSWORD'] = $_ENV['SEED_PASSWORD'] = 'motdepasse-dev';
        $this->seed();
    }

    protected function tearDown(): void
    {
        unset($_SERVER['SEED_PASSWORD'], $_ENV['SEED_PASSWORD']);

        parent::tearDown();
    }

    public function test_chaque_compte_se_connecte_et_atteint_son_espace(): void
    {
        $espaces = [
            'admin@example.com' => '/admin/users',
            'agent@example.com' => '/admin/users',
            'coursier@example.com' => '/coursier',
            'client@example.com' => '/',
        ];

        foreach ($espaces as $email => $page) {
            $this->post('/login', ['email' => $email, 'password' => 'motdepasse-dev'])
                ->assertRedirect('/home');
            $this->get($page)->assertOk();
            $this->post('/logout');
        }
    }

    public function test_le_super_admin_se_connecte(): void
    {
        $this->post(route('SuperAdmin.connect'), [
            'email' => 'superadmin@example.com',
            'password' => 'motdepasse-dev',
        ]);

        $this->get(route('SuperAdmin.home'))->assertOk();
    }
}
