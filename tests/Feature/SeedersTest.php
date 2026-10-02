<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\SuperAdmin\User as SuperAdminUser;
use App\Models\TypeClient\TypeClient;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use App\Models\User;
use App\Models\Ville\Ville;
use App\Tenancy\CurrentEntreprise;
use Database\Seeders\DemoSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedersTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_seeders_creent_les_referentiels_et_les_comptes(): void
    {
        $this->seed();

        $this->assertSame(4, TypeUtilisateur::count());
        $this->assertSame('Super Admin', TypeUtilisateur::find(1)->libelle);
        $this->assertSame(2, TypeClient::count());
        $this->assertSame(2, Ville::count());
        $this->assertTrue(SuperAdminUser::where('email', SuperAdminSeeder::EMAIL)->exists());

        $entreprise = Entreprise::where('slug', DemoSeeder::ENTREPRISE_SLUG)->firstOrFail();
        $this->assertNotNull($entreprise->id_abonnement);
        $this->assertTrue($entreprise->date_fin->isFuture());
        $this->assertNotNull($entreprise->id_quartier_siege);

        $admin = User::where('email', DemoSeeder::ADMIN_EMAIL)->firstOrFail();
        $this->assertTrue($admin->estAdministrateur());
        $this->assertSame($entreprise->id, (int) $admin->entreprise_id);

        $coursier = User::where('email', DemoSeeder::COURSIER_EMAIL)->firstOrFail();
        $this->assertSame($entreprise->id, (int) $coursier->entreprise_id);

        // Les profils métier sont cloisonnés : leur lecture exige le contexte de l'entreprise.
        app(CurrentEntreprise::class)->runAs($entreprise, function () use ($entreprise, $coursier) {
            $this->assertSame($entreprise->id, (int) $coursier->coursier_utilisateur->entreprise_id);
            $this->assertSame($coursier->id, (int) $coursier->coursier_utilisateur->id_utilisateur);

            $client = User::where('email', DemoSeeder::CLIENT_EMAIL)->firstOrFail();
            $this->assertNotNull($client->client_utilisateur);
        });
    }

    public function test_les_seeders_sont_idempotents(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(4, TypeUtilisateur::count());
        $this->assertSame(1, User::where('email', DemoSeeder::ADMIN_EMAIL)->count());
        $this->assertSame(1, Entreprise::count());
    }
}
