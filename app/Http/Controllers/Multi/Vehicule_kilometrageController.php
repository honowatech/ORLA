<?php

namespace App\Http\Controllers\Multi;

use App\Http\Controllers\Multi\Concerns\ParcAccess;
use App\Models\Parc\Vehicule_kilometrage;
use Illuminate\Http\Request;

/**
 * Relevés manuels de kilométrage.
 */
class Vehicule_kilometrageController extends Controller
{
  use ParcAccess;

  public function store(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $request->validate([
      'id_vehicule' => 'bail|required|integer',
      'date' => 'bail|required|date|before_or_equal:today',
      'kilometrage' => 'bail|required|integer|min:0',
      'description' => 'bail|nullable|max:1000',
    ]);
    $vehicule = $this->vehiculeAccessible($request->input('id_vehicule'));
    $vehicule->enregistrerKilometrage($request->input('kilometrage'), 'manuel', $request->input('date'), $request->input('description'), auth()->id());
    session()->flash('message', "Relevé de <b>".formatMontant($request->input('kilometrage'))." km</b> enregistré avec <b class='text-success'>Succès.</b>");
    return redirect()->route('vehicule.show', [$vehicule->id, 'onglet' => 'kilometrage']);
  }

  public function destroy($id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $releve = Vehicule_kilometrage::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($releve->id_vehicule);
    $releve->delete();
    // Recalcule le compteur à partir des relevés restants.
    $max = (int) $vehicule->releves_kilometrage()->max('kilometrage');
    if ($max < $vehicule->kilometrage) {
      $vehicule->kilometrage = $max;
      $vehicule->save();
    }
    session()->flash('message', "Relevé supprimé avec <b class='text-success'>Succès.</b>");
    return redirect()->route('vehicule.show', [$vehicule->id, 'onglet' => 'kilometrage']);
  }
}
