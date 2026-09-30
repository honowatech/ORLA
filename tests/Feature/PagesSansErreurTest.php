<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_les_pages_de_detail_des_commandes_s_affichent(): void
    {
        $commandes = DB::table('commandes')->pluck('id');
        $this->assertCount(4, $commandes);
        $coursierCommandes = DB::table('commandes')->whereNotNull('id_coursier')->pluck('id');

        $pages = [
            'admin@example.com' => $commandes->map(fn ($id) => route('commandes.show', $id)),
            'client@example.com' => $commandes->map(fn ($id) => route('Clientcommandes.show', $id)),
            'coursier@example.com' => $coursierCommandes->map(fn ($id) => route('Coursiercommandes.show', $id)),
        ];
        foreach ($pages as $email => $urls) {
            $user = User::where('email', $email)->firstOrFail();
            foreach ($urls as $url) {
                $this->actingAs($user)->get($url)->assertOk();
            }
        }
    }

    public function test_les_pages_super_admin_s_affichent(): void
    {
        $this->post(route('SuperAdmin.connect'), ['email' => 'superadmin@example.com', 'password' => 'motdepasse-dev']);

        $pages = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods())
                && in_array('superadmin', $route->gatherMiddleware())
                && ! str_contains($route->uri(), '{'))
            ->map(fn ($route) => '/'.ltrim($route->uri(), '/'));

        $this->assertNotEmpty($pages);
        foreach ($pages as $page) {
            $status = $this->get($page)->getStatusCode();
            $this->assertLessThan(500, $status, "$page renvoie $status pour le Super Admin");
        }
    }
}
