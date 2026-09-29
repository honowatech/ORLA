<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_la_page_de_connexion_s_affiche(): void
    {
        $this->get('/login')->assertOk();
    }
}
