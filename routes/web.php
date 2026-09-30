<?php

use App\Http\Controllers\Admin\CommandesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Multi\ZoneController;
use App\Http\Controllers\SuperAdmin\AbonnementController;
use App\Http\Controllers\SuperAdmin\ApiController;
use App\Http\Controllers\SuperAdmin\ClientController;
use App\Http\Controllers\SuperAdmin\ErrorController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// /////////////////////////////////////////////////////////// Super Admin
Route::get('/Sc-no_abonnement', [ErrorController::class, 'no_abonnement'])->name('SuperAdmin.no_abonnement');
Route::get('/Sc-empty_abonnement', [ErrorController::class, 'empty_abonnement'])->name('SuperAdmin.empty_abonnement');
Route::get('/Sc-full_error', [ErrorController::class, 'full_error'])->name('SuperAdmin.empty_client');
Route::get('/Sc-site_inactif', [ErrorController::class, 'site_inactif'])->name('SuperAdmin.site_inactif');
Route::resource('Sc-transaction', 'App\Http\Controllers\SuperAdmin\TransactionController')->except(['edit', 'update']);
Route::get('/Sc-transaction.checkpay/{id_transaction}', [TransactionController::class, 'checkpay'])->name('Sc-transaction.checkpay');

Route::get('/superadmin', function () {
    return redirect()->route('SuperAdmin.home');
});
Route::group(['prefix' => 'superadmin'], function () {
    Route::get('/Sa-login', [SuperAdminController::class, 'login'])->name('SuperAdmin.login');
    Route::post('/Sa-connect', [SuperAdminController::class, 'connect'])->name('SuperAdmin.connect');
    Route::post('/Sa-logout', [SuperAdminController::class, 'disconnect'])->name('SuperAdmin.disconnect');

    // Espace réservé au Super Admin connecté (session SuperAdmin_infos).
    Route::middleware('superadmin')->group(function () {
        Route::get('/Sa-home', [SuperAdminController::class, 'home'])->name('SuperAdmin.home');

        Route::resource('Sa-parametre', 'App\Http\Controllers\SuperAdmin\ParametreController')->only(['index']);

        Route::resource('Sa-client', 'App\Http\Controllers\SuperAdmin\ClientController');
        Route::get('/Sa-client-ajax', [ClientController::class, 'index_ajax'])->name('Sa-client.index_ajax');
        Route::post('/Sa-client.recap_create', [ClientController::class, 'recap_create'])->name('Sa-client.recap_create');
        Route::post('/Sa-client_recap_edit', [ClientController::class, 'recap_edit'])->name('Sa-client.recap_edit');
        Route::post('/Sa-client_recap_abonate', [ClientController::class, 'recap_abonate'])->name('Sa-client.recap_abonate');

        Route::resource('Sa-abonnement', 'App\Http\Controllers\SuperAdmin\AbonnementController');
        Route::get('/Sa-abonnement-ajax', [AbonnementController::class, 'index_ajax'])->name('Sa-abonnement.index_ajax');
        Route::post('/Sa-abonnement.recap_create', [AbonnementController::class, 'recap_create'])->name('Sa-abonnement.recap_create');
        Route::post('/Sa-abonnement_recap_edit', [AbonnementController::class, 'recap_edit'])->name('Sa-abonnement.recap_edit');

        Route::resource('Sa-api', 'App\Http\Controllers\SuperAdmin\ApiController')->only(['edit', 'update', 'destroy']);
        Route::post('/Sa-api_recap_edit', [ApiController::class, 'recap_edit'])->name('Sa-api.recap_edit');

        Route::get('/Sa-transaction-ajax', [TransactionController::class, 'index_ajax'])->name('Sa-transaction.index_ajax');
    });

    // Paiement d'abonnement : create/store/show restent accessibles au client
    // qui paie ; le contrôleur vérifie lui-même les autres actions.
    Route::resource('Sa-transaction', 'App\Http\Controllers\SuperAdmin\TransactionController')->except(['edit', 'update']);
});

// /////////////////////////////////////////////////////////// End Super Admin
// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Pas d'inscription publique : les comptes sont créés par l'administration.
Auth::routes(['register' => false]);
// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::middleware('Check_Sa_Client_Error')->group(function () {// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    Route::get('/NonAutorisé', [HomeController::class, 'error'])->name('home.error');
    // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    Route::middleware('auth')->group(function () {
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::group(['prefix' => '/multi', 'middleware' => 'role:admin,agent,superviseur_ville'], function () {
            Route::resource('type_vehicule', 'App\Http\Controllers\Multi\Type_vehiculeController');
            Route::resource('vehicule', 'App\Http\Controllers\Multi\VehiculeController');
            Route::resource('coursiers', 'App\Http\Controllers\Multi\CoursiersController');
            Route::resource('zone', 'App\Http\Controllers\Multi\ZoneController');
            Route::post('/quartierLie', [ZoneController::class, 'quartierLie'])->name('quartierLie');
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('details_zone', 'App\Http\Controllers\Multi\Details_zoneController')->names('multi.details_zone')->only(['store', 'update']);
            Route::resource('informations_personnels', 'App\Http\Controllers\Multi\Informations_personnelsController')->only(['store', 'edit']);
        });
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        Route::group(['prefix' => '/admin', 'middleware' => 'role:admin,agent'], function () {
            // --- Route liées à un admin ---//

            Route::post('/attribuate_agent', [CommandesController::class, 'attribuate_agent'])->name('commande_agent');
            Route::get('/', [HomeController::class, 'admin'])->name('home.admin');
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('users', 'App\Http\Controllers\Admin\UsersController');
            Route::post('/compteLie', [UsersController::class, 'compteLie'])->name('compteLie');
            Route::resource('password', 'App\Http\Controllers\Admin\PasswordController')->names(['update' => 'admin.password.update'])->only(['destroy']);
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('ville', 'App\Http\Controllers\Admin\VilleController')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('clients', 'App\Http\Controllers\Admin\ClientsController');
            Route::resource('agents', 'App\Http\Controllers\Admin\AgentsController');
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('details_zone', 'App\Http\Controllers\Admin\Details_zoneController')->only(['store', 'update']);
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('quartier', 'App\Http\Controllers\Admin\QuartierController')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
            Route::resource('boutiques', 'App\Http\Controllers\Admin\BoutiquesController')->only(['store', 'show', 'edit', 'destroy']);
            Route::resource('produits', 'App\Http\Controllers\Admin\ProduitsController');
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('commandes', 'App\Http\Controllers\Admin\CommandesController');
            Route::post('/boutiqueLie', [CommandesController::class, 'boutiqueLie'])->name('boutiqueLie');
            Route::post('/commandeclients', [CommandesController::class, 'commandeClient'])->name('commandeClient');
            Route::post('/lieu', [CommandesController::class, 'lieu'])->name('lieu');
            Route::post('/lieu2', [CommandesController::class, 'lieu2'])->name('lieu2');
            Route::post('/montant', [CommandesController::class, 'montant'])->name('montant');
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('montant_livraison', 'App\Http\Controllers\Admin\Montant_livraisonController')->only(['index', 'store']);
            Route::resource('details_commande', 'App\Http\Controllers\Admin\Details_commandeController')->only(['index', 'create', 'store', 'update']);
            Route::resource('notification', 'App\Http\Controllers\Admin\NotificationController')->only(['index']);
            // --- fin -- Route liées à un admin ---//
        });

        // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        Route::group(['prefix' => '/coursier', 'middleware' => 'role:coursier'], function () {
            // --- Route liées à un coursier ---//
            Route::get('/', [HomeController::class, 'coursier'])->name('home.coursier');
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('Coursierusers', 'App\Http\Controllers\Coursier\UsersController')->only(['show', 'edit', 'update', 'destroy']);
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Le coursier consulte seulement les clients de ses commandes : les autres actions sont réservées à l'admin.
            Route::resource('Coursierclients', 'App\Http\Controllers\Coursier\ClientsController')->only(['show']);
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('Coursiercommandes', 'App\Http\Controllers\Coursier\CommandesController')->only(['store', 'show', 'edit', 'update', 'destroy']);
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('Coursierpassword', 'App\Http\Controllers\Coursier\PasswordController')->only(['index', 'update']);
            // --- fin -- Route liées à un Coursier ---//
        });

        // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        Route::group(['prefix' => '', 'middleware' => 'role:client'], function () {
            // --- Route liées à un client ---//
            Route::get('/', [HomeController::class, 'clients'])->name('home.client');
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('Clientusers', 'App\Http\Controllers\Client\UsersController')->only(['show', 'edit', 'update', 'destroy']);
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('Clientcommandes', 'App\Http\Controllers\Client\CommandesController');
            Route::post('/boutiqueLie', [App\Http\Controllers\Client\CommandesController::class, 'boutiqueLie'])->name('ClientboutiqueLie');
            Route::post('/commandeclients', [App\Http\Controllers\Client\CommandesController::class, 'commandeClient'])->name('ClientcommandeClient');
            Route::post('/lieu', [App\Http\Controllers\Client\CommandesController::class, 'lieu'])->name('Clientlieu');
            Route::post('/lieu2', [App\Http\Controllers\Client\CommandesController::class, 'lieu2'])->name('Clientlieu2');
            Route::post('/montant', [App\Http\Controllers\Client\CommandesController::class, 'montant'])->name('Clientmontant');
            // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            Route::resource('Clientpassword', 'App\Http\Controllers\Client\PasswordController')->only(['index', 'update']);
            Route::resource('Clientcoursiers', 'App\Http\Controllers\Client\CoursiersController')->only(['show']);
            // --- fin -- Route liées à un client ---//
        });
        // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    });
});

// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
