<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    public function test_chaque_route_pointe_vers_une_methode_existante(): void
    {
        $absentes = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getActionName())
            ->filter(fn ($action) => str_starts_with($action, 'App\\') && str_contains($action, '@'))
            ->reject(fn ($action) => method_exists(...explode('@', $action)))
            ->values();

        $this->assertSame([], $absentes->all());
    }

    public function test_l_inscription_publique_est_desactivee(): void
    {
        $this->assertFalse(Route::has('register'));
        $this->get('/register')->assertNotFound();
    }
}
