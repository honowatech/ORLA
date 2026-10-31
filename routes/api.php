<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Notification serveur à serveur de Monetbil après un paiement d'abonnement
Route::post('/monetbil/notify', function (Request $request, App\Billing\PaiementAbonnement $paiement) {
    $transaction = App\Models\SuperAdmin\Transaction::where('reference_paiement', (string) $request->input('payment_ref'))->first();
    if ($transaction === null) {
        return response('transaction inconnue', 404);
    }

    return response($paiement->confirmer($transaction, $request->post()));
})->middleware('throttle:60,1')->name('monetbil.notify');
