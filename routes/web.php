<?php

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

///////////////////////////////////////////////////////////// Super Admin
Route::get('/Sc-no_abonnement', [App\Http\Controllers\SuperAdmin\ErrorController::class, 'no_abonnement'])->name('SuperAdmin.no_abonnement');
Route::get('/Sc-empty_abonnement', [App\Http\Controllers\SuperAdmin\ErrorController::class, 'empty_abonnement'])->name('SuperAdmin.empty_abonnement');
Route::get('/Sc-full_error', [App\Http\Controllers\SuperAdmin\ErrorController::class, 'full_error'])->name('SuperAdmin.empty_client');
Route::get('/Sc-site_inactif', [App\Http\Controllers\SuperAdmin\ErrorController::class, 'site_inactif'])->name('SuperAdmin.site_inactif');
Route::resource('Sc-transaction', 'App\Http\Controllers\SuperAdmin\TransactionController');
Route::get('/Sc-transaction.checkpay/{id_transaction}', [App\Http\Controllers\SuperAdmin\TransactionController::class, 'checkpay'])->name('Sc-transaction.checkpay');

Route::get('/superadmin',function () {return redirect()->route('SuperAdmin.home');});
Route::group(['prefix' => 'superadmin'], function(){
        Route::get('/Sa-login', [App\Http\Controllers\SuperAdmin\SuperAdminController::class, 'login'])->name('SuperAdmin.login');
        Route::post('/Sa-connect', [App\Http\Controllers\SuperAdmin\SuperAdminController::class, 'connect'])->name('SuperAdmin.connect');
        Route::post('/Sa-logout', [App\Http\Controllers\SuperAdmin\SuperAdminController::class, 'disconnect'])->name('SuperAdmin.disconnect');

    // Espace réservé au Super Admin connecté (session SuperAdmin_infos).
    Route::middleware('superadmin')->group(function(){
        Route::get('/Sa-home', [App\Http\Controllers\SuperAdmin\SuperAdminController::class, 'home'])->name('SuperAdmin.home');

        Route::resource('Sa-parametre', 'App\Http\Controllers\SuperAdmin\ParametreController')->only(['index']);

        Route::resource('Sa-client', 'App\Http\Controllers\SuperAdmin\ClientController');
        Route::get('/Sa-client-ajax', [App\Http\Controllers\SuperAdmin\ClientController::class, 'index_ajax'])->name('Sa-client.index_ajax');
        Route::post('/Sa-client.recap_create', [App\Http\Controllers\SuperAdmin\ClientController::class, 'recap_create'])->name('Sa-client.recap_create');
        Route::post('/Sa-client_recap_edit', [App\Http\Controllers\SuperAdmin\ClientController::class, 'recap_edit'])->name('Sa-client.recap_edit');
        Route::post('/Sa-client_recap_abonate', [App\Http\Controllers\SuperAdmin\ClientController::class, 'recap_abonate'])->name('Sa-client.recap_abonate');

        Route::resource('Sa-abonnement', 'App\Http\Controllers\SuperAdmin\AbonnementController');
        Route::get('/Sa-abonnement-ajax', [App\Http\Controllers\SuperAdmin\AbonnementController::class, 'index_ajax'])->name('Sa-abonnement.index_ajax');
        Route::post('/Sa-abonnement.recap_create', [App\Http\Controllers\SuperAdmin\AbonnementController::class, 'recap_create'])->name('Sa-abonnement.recap_create');
        Route::post('/Sa-abonnement_recap_edit', [App\Http\Controllers\SuperAdmin\AbonnementController::class, 'recap_edit'])->name('Sa-abonnement.recap_edit');

        Route::resource('Sa-api', 'App\Http\Controllers\SuperAdmin\ApiController')->only(['edit', 'update', 'destroy']);
        Route::get('/Sa-api-ajax', [App\Http\Controllers\SuperAdmin\ApiController::class, 'index_ajax'])->name('Sa-api.index_ajax');
        Route::post('/Sa-api.recap_create', [App\Http\Controllers\SuperAdmin\ApiController::class, 'recap_create'])->name('Sa-api.recap_create');
        Route::post('/Sa-api_recap_edit', [App\Http\Controllers\SuperAdmin\ApiController::class, 'recap_edit'])->name('Sa-api.recap_edit');

        Route::get('/Sa-transaction-ajax', [App\Http\Controllers\SuperAdmin\TransactionController::class, 'index_ajax'])->name('Sa-transaction.index_ajax');
    });

        // Paiement d'abonnement : create/store/show restent accessibles au client
        // qui paie ; le contrôleur vérifie lui-même les autres actions.
        Route::resource('Sa-transaction', 'App\Http\Controllers\SuperAdmin\TransactionController');
}); 

///////////////////////////////////////////////////////////// End Super Admin
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Auth::routes();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::middleware('Check_Sa_Client_Error')->group(function() {////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        Route::get('/NonAutorisé', [App\Http\Controllers\HomeController::class, 'error'])->name('home.error');
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        Route::middleware('auth')->group(function() {
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
                Route::group(['prefix' => '/multi', 'middleware' => 'role:admin,agent,superviseur_ville'], function(){
                        Route::resource('type_vehicule', 'App\Http\Controllers\Multi\Type_vehiculeController');
                        Route::resource('vehicule', 'App\Http\Controllers\Multi\VehiculeController');
                        Route::resource('coursiers', 'App\Http\Controllers\Multi\CoursiersController');      
                        Route::resource('zone', 'App\Http\Controllers\Multi\ZoneController');
                        Route::post('/quartierLie', [App\Http\Controllers\Multi\ZoneController::class, 'quartierLie'])->name('quartierLie');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('details_zone', 'App\Http\Controllers\Multi\Details_zoneController')->names('multi.details_zone')->only(['store', 'update']);
                        Route::resource('informations_personnels', 'App\Http\Controllers\Multi\Informations_personnelsController')->only(['store', 'edit']);
                });
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '/admin', 'middleware' => 'role:admin,agent'], function(){
                    //--- Route liées à un admin ---//
                        
                        Route::post('/attribuate_agent', [App\Http\Controllers\Admin\CommandesController::class, 'attribuate_agent'])->name('commande_agent');
                        Route::get('/', [App\Http\Controllers\HomeController::class, 'admin'])->name('home.admin');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('users', 'App\Http\Controllers\Admin\UsersController');
                        Route::post('/compteLie', [App\Http\Controllers\Admin\UsersController::class, 'compteLie'])->name('compteLie');
                        Route::resource('password', 'App\Http\Controllers\Admin\PasswordController')->names(['update' => 'admin.password.update'])->only(['destroy']);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('ville', 'App\Http\Controllers\Admin\VilleController')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('clients', 'App\Http\Controllers\Admin\ClientsController');
                        Route::resource('agents', 'App\Http\Controllers\Admin\AgentsController');
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('details_zone', 'App\Http\Controllers\Admin\Details_zoneController')->only(['store', 'update']);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('quartier', 'App\Http\Controllers\Admin\QuartierController')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
                        Route::resource('boutiques', 'App\Http\Controllers\Admin\BoutiquesController')->only(['store', 'show', 'edit', 'destroy']);
                        Route::resource('produits', 'App\Http\Controllers\Admin\ProduitsController');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('commandes', 'App\Http\Controllers\Admin\CommandesController');
                        Route::post('/boutiqueLie', [App\Http\Controllers\Admin\CommandesController::class, 'boutiqueLie'])->name('boutiqueLie');
                        Route::post('/commandeclients', [App\Http\Controllers\Admin\CommandesController::class, 'commandeClient'])->name('commandeClient');
                        Route::post('/lieu', [App\Http\Controllers\Admin\CommandesController::class, 'lieu'])->name('lieu');
                        Route::post('/lieu2', [App\Http\Controllers\Admin\CommandesController::class, 'lieu2'])->name('lieu2');
                        Route::post('/montant', [App\Http\Controllers\Admin\CommandesController::class, 'montant'])->name('montant');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('montant_livraison', 'App\Http\Controllers\Admin\Montant_livraisonController')->only(['index', 'store']);
                        Route::resource('details_commande', 'App\Http\Controllers\Admin\Details_commandeController')->only(['index', 'create', 'store', 'update']);
                        Route::resource('notification', 'App\Http\Controllers\Admin\NotificationController')->only(['index']);
                    //--- fin -- Route liées à un admin ---//
                });

                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                Route::group(['prefix' => '/coursier', 'middleware' => 'role:coursier'], function(){
                    //--- Route liées à un coursier ---//
                        Route::get('/', [App\Http\Controllers\HomeController::class, 'coursier'])->name('home.coursier');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('Coursierusers', 'App\Http\Controllers\Coursier\UsersController');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        // Le coursier consulte seulement les clients de ses commandes : les autres actions sont réservées à l'admin.
                        Route::resource('Coursierclients', 'App\Http\Controllers\Coursier\ClientsController')->only(['show']);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('Coursiercommandes', 'App\Http\Controllers\Coursier\CommandesController')->only(['store', 'show', 'edit', 'update', 'destroy']);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('Coursierpassword', 'App\Http\Controllers\Coursier\PasswordController');
                    //--- fin -- Route liées à un Coursier ---//
                });

                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                Route::group(['prefix' => '', 'middleware' => 'role:client'], function(){
                    //--- Route liées à un client ---//
                        Route::get('/', [App\Http\Controllers\HomeController::class, 'clients'])->name('home.client');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('Clientusers', 'App\Http\Controllers\Client\UsersController');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('Clientcommandes', 'App\Http\Controllers\Client\CommandesController');
                        Route::post('/boutiqueLie', [App\Http\Controllers\Client\CommandesController::class, 'boutiqueLie'])->name('ClientboutiqueLie');
                        Route::post('/commandeclients', [App\Http\Controllers\Client\CommandesController::class, 'commandeClient'])->name('ClientcommandeClient');
                        Route::post('/lieu', [App\Http\Controllers\Client\CommandesController::class, 'lieu'])->name('Clientlieu');
                        Route::post('/lieu2', [App\Http\Controllers\Client\CommandesController::class, 'lieu2'])->name('Clientlieu2');
                        Route::post('/montant', [App\Http\Controllers\Client\CommandesController::class, 'montant'])->name('Clientmontant');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        Route::resource('Clientpassword', 'App\Http\Controllers\Client\PasswordController');
                        Route::resource('Clientcoursiers', 'App\Http\Controllers\Client\CoursiersController')->only(['show']);
                    //--- fin -- Route liées à un client ---//
                });
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        });
});

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////