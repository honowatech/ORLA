<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Test de fumée : chaque page GET sans paramètre de chaque espace
 * s'affiche sans erreur serveur pour le rôle qui y a accès.
 */
class PagesSansErreurTest extends TestCase
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

    /** Points d'entrée AJAX qui exigent des paramètres : ce ne sont pas des pages. */
    private const AJAX = ['/admin/details_commande/create'];

    /** @return array<string, array{string, string}> */
    public static function espaces(): array
    {
        return [
            'admin' => ['admin@example.com', 'role:admin,agent'],
            'agent' => ['agent@example.com', 'role:admin,agent'],
            'multi (admin)' => ['admin@example.com', 'role:admin,agent,superviseur_ville'],
            'coursier' => ['coursier@example.com', 'role:coursier'],
            'client' => ['client@example.com', 'role:client'],
        ];
    }

    #[DataProvider('espaces')]
    public function test_les_pages_de_l_espace_s_affichent(string $email, string $middleware): void
    {
        $user = User::where('email', $email)->firstOrFail();
        $pages = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods())
                && in_array($middleware, $route->gatherMiddleware())
                && ! str_contains($route->uri(), '{'))
            ->map(fn ($route) => '/'.ltrim($route->uri(), '/'))
            ->reject(fn ($page) => in_array($page, self::AJAX));

        $this->assertNotEmpty($pages);
        foreach ($pages as $page) {
            $status = $this->actingAs($user)->get($page)->getStatusCode();
            $this->assertLessThan(500, $status, "$page renvoie $status pour $email");
        }
    }
}
