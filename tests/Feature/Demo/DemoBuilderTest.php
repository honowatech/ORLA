<?php

namespace Tests\Feature\Demo;

use App\Demo\Demo;
use App\Demo\Sandbox;
use App\Models\commandes\Commandes;
use App\Models\users\Users;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DemoBuilderTest extends DemoTestCase
{
    public function test_a_sandbox_is_a_separate_file_and_the_main_database_is_untouched(): void
    {
        $sandbox = $this->buildSandbox();

        $this->assertFileExists($sandbox->path());
        $this->assertNotNull($sandbox->built_at);
        $this->assertNull($sandbox->claimed_at);
        $this->assertSame(0, Users::count(), 'aucun utilisateur démo dans la base principale');
        $this->assertSame(0, Commandes::count(), 'aucune commande démo dans la base principale');

        $counts = $this->inSandbox($sandbox, fn () => [Users::count(), Commandes::count()]);
        $this->assertGreaterThan(5, $counts[0]);
        $this->assertGreaterThan(10, $counts[1]);
    }

    public function test_same_seed_same_day_gives_the_same_agency(): void
    {
        $dump = fn (Sandbox $s) => $this->inSandbox($s, fn () => Commandes::orderBy('id')
            ->get(['statut', 'id_coursier', 'id_quartier_livraison', 'montant_livraison', 'created_at', 'date_livre'])
            ->toJson());

        $a = $dump($this->buildSandbox(99));
        $b = $dump($this->buildSandbox(99));
        $c = $dump($this->buildSandbox(100));

        $this->assertSame($a, $b);
        $this->assertNotSame($a, $c);
    }

    public function test_history_is_replayed_and_stops_now(): void
    {
        $sandbox = $this->buildSandbox();

        $this->inSandbox($sandbox, function () {
            $this->assertFalse(Commandes::where('created_at', '>', now())->exists());
            $this->assertFalse(Commandes::where('date_livre', '>', now())->exists());
            // La journée est en cours : il reste des commandes ouvertes, et l'historique est fini.
            $this->assertTrue(Commandes::whereIn('statut', ['attente', 'attribue', 'encours'])->exists());
            $this->assertTrue(Commandes::where('statut', 'livre')->exists());
            $this->assertTrue(Commandes::where('date_livraison', '>', now()->addDay())->exists(), 'commandes planifiées');
            // Les dates viennent de l'horloge du rejeu, pas de la construction.
            $this->assertTrue(Commandes::where('created_at', '<', now()->subDays(2))->exists());
            // Tous les utilisateurs sont sur le domaine réservé.
            $this->assertSame(0, Users::where('email', 'not like', '%@demo.invalid')->count());
        });
    }

    public function test_the_clock_is_restored_after_a_build(): void
    {
        $before = now()->toIso8601String();
        $this->buildSandbox();
        $this->assertSame($before, now()->toIso8601String());
        $this->assertSame(config('database.default'), Demo::mainConnection());
    }

    public function test_a_failed_build_leaves_no_file_and_no_row(): void
    {
        // Un profil sans utilisateur : le contrôle de cohérence refuse le bac.
        $before = now()->toIso8601String();
        config(['demo.profiles.inexistant' => ['type' => 'Inexistant', 'title' => 'x', 'text' => 'x']]);

        try {
            $this->buildSandbox();
            $this->fail('le contrôle de cohérence aurait dû refuser le bac');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('inexistant', $e->getMessage());
        }

        $this->assertSame(0, Sandbox::count());
        $this->assertSame([], array_values(array_filter(
            File::glob($this->dir.'/*.sqlite*'),
            fn ($f) => ! str_starts_with(basename($f), 'schema-'),
        )));
        $this->assertSame($before, now()->toIso8601String(), 'horloge rétablie après l\'erreur');
    }

    public function test_build_command(): void
    {
        $this->assertSame(0, Artisan::call('demo:build', ['--seed' => 3, '--days' => 2]));
        $this->assertSame(1, Sandbox::whereNotNull('built_at')->count());
    }
}
