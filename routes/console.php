<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Démo sans mot de passe (docs/demo.md)
|--------------------------------------------------------------------------
*/

Artisan::command('demo:build {--seed= : graine (aléatoire par défaut)} {--days= : jours d\'historique} {--ahead= : jours planifiés à venir}', function () {
    $options = array_filter([
        'seed' => $this->option('seed') !== null ? (int) $this->option('seed') : null,
        'history_days' => $this->option('days') !== null ? (int) $this->option('days') : null,
        'ahead_days' => $this->option('ahead') !== null ? (int) $this->option('ahead') : null,
    ], fn ($v) => $v !== null);

    $started = microtime(true);
    $sandbox = app(App\Demo\SandboxBuilder::class)->build($options);
    $this->info(sprintf('Bac n° %d prêt (graine %d) en %.1f s.', $sandbox->id, $sandbox->seed, microtime(true) - $started));
})->purpose('Construit un bac démo et le met dans la réserve');

Artisan::command('demo:refill', function () {
    $pool = app(App\Demo\SandboxPool::class);
    try {
        $built = $pool->refill(fn ($s) => $this->line("Bac n° {$s->id} prêt."));
    } catch (Throwable $e) {
        Illuminate\Support\Facades\Log::error('demo.refill_failed', ['error' => $e->getMessage()]);
        $this->error('Échec de la construction : '.$e->getMessage());

        return 1;
    }
    $this->info("$built bac(s) construit(s), {$pool->readyCount()} prêt(s).");
})->purpose('Maintient la réserve de bacs démo prêts');

Artisan::command('demo:prune', function () {
    $count = app(App\Demo\SandboxPool::class)->prune();
    $this->info("$count bac(s) supprimé(s).");
})->purpose('Supprime définitivement les bacs démo expirés ou périmés');

Illuminate\Support\Facades\Schedule::command('demo:refill')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/demo.log'));

Illuminate\Support\Facades\Schedule::command('demo:prune')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer();
