<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Charge les référentiels, la licence et les comptes de démonstration,
     * puis renvoie l'utilisateur de démonstration demandé.
     */
    protected function utilisateurDemo(string $email = DemoSeeder::ADMIN_EMAIL): User
    {
        $this->seed();

        return User::where('email', $email)->firstOrFail();
    }
}
