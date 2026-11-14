<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Client\CommandeController as ClientCommandeController;
use App\Http\Controllers\Api\V1\Coursier\CommandeController as CoursierCommandeController;
use App\Http\Controllers\Api\V1\ReferentielController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — application mobile Speedex
|--------------------------------------------------------------------------
|
| Toutes les routes sont préfixées par /api/v1. L'authentification se fait
| par jeton Bearer (Laravel Sanctum) obtenu via POST /api/v1/auth/login.
| Documentation : docs/API.md
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    Route::get('/ping', fn () => response()->json([
        'status' => 'ok',
        'version' => 'v1',
        'time' => now()->toIso8601String(),
    ]))->name('ping');

    Route::middleware('Check_Sa_Client_Error')->group(function () {

        Route::post('/auth/login', [AuthController::class, 'login'])
            ->middleware('throttle:api-login')
            ->name('auth.login');

        Route::middleware(['auth:sanctum', 'api.active', 'throttle:api'])->group(function () {

            // Compte
            Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
            Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::post('/auth/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout-all');
            Route::put('/auth/password', [AuthController::class, 'updatePassword'])->name('auth.password');

            // Référentiels
            Route::get('/villes', [ReferentielController::class, 'villes'])->name('villes');
            Route::get('/villes/{ville}/quartiers', [ReferentielController::class, 'quartiers'])
                ->whereNumber('ville')->name('villes.quartiers');
            Route::post('/tarifs/estimation', [ReferentielController::class, 'estimerTarif'])->name('tarifs.estimation');

            // Espace coursier
            Route::prefix('coursier')->name('coursier.')->middleware('api.role:coursier')->group(function () {
                Route::get('/commandes', [CoursierCommandeController::class, 'index'])->name('commandes.index');
                Route::get('/commandes/resume', [CoursierCommandeController::class, 'resume'])->name('commandes.resume');
                Route::get('/commandes/{commande}', [CoursierCommandeController::class, 'show'])
                    ->whereNumber('commande')->name('commandes.show');
                Route::patch('/commandes/{commande}/statut', [CoursierCommandeController::class, 'changerStatut'])
                    ->whereNumber('commande')->name('commandes.statut');
            });

            // Espace client
            Route::prefix('client')->name('client.')->middleware('api.role:client')->group(function () {
                Route::get('/commandes', [ClientCommandeController::class, 'index'])->name('commandes.index');
                Route::post('/commandes', [ClientCommandeController::class, 'store'])->name('commandes.store');
                Route::get('/commandes/{commande}', [ClientCommandeController::class, 'show'])
                    ->whereNumber('commande')->name('commandes.show');
                Route::post('/commandes/{commande}/annuler', [ClientCommandeController::class, 'annuler'])
                    ->whereNumber('commande')->name('commandes.annuler');
            });
        });
    });
});
