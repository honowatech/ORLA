<?php

use App\Http\Controllers\Admin\CommandesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Facturation\AbonnementStatutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Multi\ZoneController;
use App\Http\Controllers\SuperAdmin\AbonnementController;
use App\Http\Controllers\SuperAdmin\ApiController;
use App\Http\Controllers\SuperAdmin\ClientController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Console Super Admin (opérateur de la plateforme)
|--------------------------------------------------------------------------
| Hors contexte d'entreprise : le cloisonnement des données est désactivé.
*/
Route::get('/superadmin', fn () => redirect()->route('SuperAdmin.home'));
Route::prefix('superadmin')->middleware('tenancy.bypass')->group(function () {
    Route::get('/Sa-login', [SuperAdminController::class, 'login'])->name('SuperAdmin.login');
    Route::post('/Sa-connect', [SuperAdminController::class, 'connect'])->name('SuperAdmin.connect');
    Route::post('/Sa-logout', [SuperAdminController::class, 'disconnect'])->name('SuperAdmin.disconnect');
    Route::get('/Sa-home', [SuperAdminController::class, 'home'])->name('SuperAdmin.home');

    Route::resource('Sa-parametre', 'App\Http\Controllers\SuperAdmin\ParametreController');

    Route::resource('Sa-client', 'App\Http\Controllers\SuperAdmin\ClientController');
    Route::get('/Sa-client-ajax', [ClientController::class, 'index_ajax'])->name('Sa-client.index_ajax');
    Route::post('/Sa-client.recap_create', [ClientController::class, 'recap_create'])->name('Sa-client.recap_create');
    Route::post('/Sa-client_recap_edit', [ClientController::class, 'recap_edit'])->name('Sa-client.recap_edit');
    Route::post('/Sa-client_recap_abonate', [ClientController::class, 'recap_abonate'])->name('Sa-client.recap_abonate');

    Route::resource('Sa-abonnement', 'App\Http\Controllers\SuperAdmin\AbonnementController');
    Route::get('/Sa-abonnement-ajax', [AbonnementController::class, 'index_ajax'])->name('Sa-abonnement.index_ajax');
    Route::post('/Sa-abonnement.recap_create', [AbonnementController::class, 'recap_create'])->name('Sa-abonnement.recap_create');
    Route::post('/Sa-abonnement_recap_edit', [AbonnementController::class, 'recap_edit'])->name('Sa-abonnement.recap_edit');

    Route::resource('Sa-api', 'App\Http\Controllers\SuperAdmin\ApiController');
    Route::post('/Sa-api_recap_edit', [ApiController::class, 'recap_edit'])->name('Sa-api.recap_edit');

    Route::resource('Sa-transaction', 'App\Http\Controllers\SuperAdmin\TransactionController');
    Route::get('/Sa-transaction-ajax', [TransactionController::class, 'index_ajax'])->name('Sa-transaction.index_ajax');
});

/*
|--------------------------------------------------------------------------
| Retour de paiement Monetbil
|--------------------------------------------------------------------------
| Public : l'utilisateur revient de la page de paiement sans session garantie.
*/
Route::middleware('tenancy.bypass')->group(function () {
    Route::get('/Sc-transaction/{Sc_transaction}', [TransactionController::class, 'show'])
        ->whereNumber('Sc_transaction')->name('Sc-transaction.show');
    Route::get('/Sc-transaction.checkpay/{id_transaction}', [TransactionController::class, 'checkpay'])->name('Sc-transaction.checkpay');
});

/*
|--------------------------------------------------------------------------
| Authentification (Laravel UI)
|--------------------------------------------------------------------------
| L'inscription publique est désactivée : la création d'un espace d'entreprise
| passera par /inscription (phase 5 du plan multi-entreprises).
*/
Auth::routes(['register' => false]);

Route::middleware('auth')->get('/NonAutorisé', [HomeController::class, 'error'])->name('home.error');

/*
|--------------------------------------------------------------------------
| Espaces de travail des entreprises
|--------------------------------------------------------------------------
| « entreprise » résout le tenant depuis l'utilisateur connecté, « actif »
| écarte les comptes désactivés, « entreprise.active » bloque les entreprises
| suspendues ou dont l'abonnement est expiré.
*/
Route::middleware(['auth', 'entreprise', 'actif'])->group(function () {
    // Accessibles même lorsque l'espace est bloqué.
    Route::get('/abonnement/expire', [AbonnementStatutController::class, 'expire'])->name('abonnement.expire');
    Route::get('/abonnement/suspendue', [AbonnementStatutController::class, 'suspendue'])->name('abonnement.suspendue');
    Route::get('/Sc-transaction/create', [TransactionController::class, 'create'])->name('Sc-transaction.create');
    Route::post('/Sc-transaction', [TransactionController::class, 'store'])->name('Sc-transaction.store');

    Route::middleware('entreprise.active')->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        Route::prefix('multi')->group(function () {
            Route::resource('type_vehicule', 'App\Http\Controllers\Multi\Type_vehiculeController');
            Route::resource('vehicule', 'App\Http\Controllers\Multi\VehiculeController');
            Route::resource('coursiers', 'App\Http\Controllers\Multi\CoursiersController');
            Route::resource('zone', 'App\Http\Controllers\Multi\ZoneController');
            Route::post('/quartierLie', [ZoneController::class, 'quartierLie'])->name('quartierLie');
            Route::resource('details_zone', 'App\Http\Controllers\Multi\Details_zoneController');
            Route::resource('informations_personnels', 'App\Http\Controllers\Multi\Informations_personnelsController');
        });

        Route::prefix('admin')->group(function () {
            Route::post('/attribuate_agent', [CommandesController::class, 'attribuate_agent'])->name('commande_agent');
            Route::get('/', [HomeController::class, 'admin'])->name('home.admin');
            Route::resource('users', 'App\Http\Controllers\Admin\UsersController');
            Route::post('/compteLie', [UsersController::class, 'compteLie'])->name('compteLie');
            Route::resource('password', 'App\Http\Controllers\Admin\PasswordController');
            Route::resource('ville', 'App\Http\Controllers\Admin\VilleController');
            Route::resource('clients', 'App\Http\Controllers\Admin\ClientsController');
            Route::resource('agents', 'App\Http\Controllers\Admin\AgentsController');
            Route::resource('quartier', 'App\Http\Controllers\Admin\QuartierController');
            Route::resource('boutiques', 'App\Http\Controllers\Admin\BoutiquesController');
            Route::resource('point_relais', 'App\Http\Controllers\Admin\Point_relaisController');
            Route::resource('produits', 'App\Http\Controllers\Admin\ProduitsController');
            Route::resource('stock', 'App\Http\Controllers\Admin\StockController');
            Route::resource('commandes', 'App\Http\Controllers\Admin\CommandesController');
            Route::post('/boutiqueLie', [CommandesController::class, 'boutiqueLie'])->name('boutiqueLie');
            Route::post('/commandeclients', [CommandesController::class, 'commandeClient'])->name('commandeClient');
            Route::post('/lieu', [CommandesController::class, 'lieu'])->name('lieu');
            Route::post('/lieu2', [CommandesController::class, 'lieu2'])->name('lieu2');
            Route::post('/montant', [CommandesController::class, 'montant'])->name('montant');
            Route::resource('montant_livraison', 'App\Http\Controllers\Admin\Montant_livraisonController');
            Route::resource('details_commande', 'App\Http\Controllers\Admin\Details_commandeController');
            Route::resource('notification', 'App\Http\Controllers\Admin\NotificationController');
        });

        Route::prefix('coursier')->group(function () {
            Route::get('/', [HomeController::class, 'coursier'])->name('home.coursier');
            Route::resource('Coursierusers', 'App\Http\Controllers\Coursier\UsersController');
            Route::resource('Coursierclients', 'App\Http\Controllers\Coursier\ClientsController');
            Route::resource('Coursiercommandes', 'App\Http\Controllers\Coursier\CommandesController');
            Route::resource('Coursierpassword', 'App\Http\Controllers\Coursier\PasswordController');
        });

        // Ancien préfixe « / » : la racine du site est réservée au futur site vitrine.
        Route::prefix('client')->group(function () {
            Route::get('/', [HomeController::class, 'clients'])->name('home.client');
            Route::resource('Clientusers', 'App\Http\Controllers\Client\UsersController');
            Route::resource('Clientcommandes', 'App\Http\Controllers\Client\CommandesController');
            Route::post('/boutiqueLie', [App\Http\Controllers\Client\CommandesController::class, 'boutiqueLie'])->name('ClientboutiqueLie');
            Route::post('/commandeclients', [App\Http\Controllers\Client\CommandesController::class, 'commandeClient'])->name('ClientcommandeClient');
            Route::post('/lieu', [App\Http\Controllers\Client\CommandesController::class, 'lieu'])->name('Clientlieu');
            Route::post('/lieu2', [App\Http\Controllers\Client\CommandesController::class, 'lieu2'])->name('Clientlieu2');
            Route::post('/montant', [App\Http\Controllers\Client\CommandesController::class, 'montant'])->name('Clientmontant');
            Route::resource('Clientpassword', 'App\Http\Controllers\Client\PasswordController');
            Route::resource('Clientcoursiers', 'App\Http\Controllers\Client\CoursiersController');
        });
    });
});

Route::get('/', fn () => redirect()->route(Auth::check() ? 'home' : 'login'));
