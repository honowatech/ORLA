<?php

namespace Tests\Feature\Demo;

use App\Demo\Demo;
use App\Demo\Sandbox;
use App\Demo\SandboxBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class DemoTestCase extends TestCase
{
    use RefreshDatabase;

    protected string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(now('Africa/Douala')->setTime(15, 30)->utc());

        $this->dir = sys_get_temp_dir().'/orla-demo-tests';
        File::ensureDirectoryExists($this->dir);
        foreach (File::glob($this->dir.'/*.sqlite*') as $file) {
            if (! str_starts_with(basename($file), 'schema-')) {
                File::delete($file);
            }
        }

        config([
            'demo.enabled' => true,
            'demo.path' => $this->dir,
            'demo.history_days' => 3,
            'demo.ahead_days' => 2,
            'demo.build_on_demand' => false,
        ]);
    }

    protected function tearDown(): void
    {
        Demo::leave();
        parent::tearDown();
    }

    protected function buildSandbox(int $seed = 7): Sandbox
    {
        return app(SandboxBuilder::class)->build(['seed' => $seed]);
    }

    /**
     * Rejoue la session de la réponse dans la requête suivante, comme un
     * navigateur, et oublie l'utilisateur gardé en mémoire par le guard.
     */
    protected function carrySession(TestResponse $response): static
    {
        $name = config('session.cookie');
        $cookie = $response->getCookie($name, false);
        if ($cookie !== null) {
            // Valeur déjà chiffrée par la réponse : la renvoyer telle quelle.
            $this->withUnencryptedCookie($name, $cookie->getValue());
        }
        $this->app['auth']->forgetGuards();

        return $this;
    }

    /** Lit une valeur dans le fichier d'un bac, sans toucher à la connexion par défaut. */
    protected function inSandbox(Sandbox $sandbox, callable $read): mixed
    {
        Demo::useFile($sandbox->path());
        try {
            return $read();
        } finally {
            Demo::leave();
        }
    }
}
