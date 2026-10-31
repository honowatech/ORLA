<?php

namespace App\Http\Controllers\Multi;

use App\Http\Controllers\Multi\Concerns\ParcAccess;
use App\Models\Parc\Vehicule_carburant;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Journal des pleins de carburant.
 */
class Vehicule_carburantController extends Controller
{
  use ParcAccess;

  protected function regles()
  {
    return [
      'id_vehicule' => 'bail|required|integer',
      'id_coursier' => 'bail|nullable|integer|exists:coursiers,id',
      'date' => 'bail|required|date|before_or_equal:today',
      'litres' => 'bail|required|numeric|min:0.1',
      'montant' => 'bail|required|numeric|min:0',
      'kilometrage' => 'bail|nullable|integer|min:0',
      'plein_complet' => 'bail|nullable|boolean',
      'station' => 'bail|nullable|max:255',
      'description' => 'bail|nullable|max:2000',
    ];
  }

  protected function remplir(Vehicule_carburant $plein, Request $request, $vehicule)
  {
    $plein->id_coursier = $request->input('id_coursier') ?: $vehicule->id_coursier;
    $plein->date = $request->input('date');
    $plein->litres = $request->input('litres');
    $plein->montant = $request->input('montant');
    $plein->kilometrage = $request->input('kilometrage');
    $plein->plein_complet = $request->boolean('plein_complet');
    $plein->station = $request->input('station');
    $plein->description = $request->input('description');
  }

  /**
   * Journal global du carburant.
   */
  public function index(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $ids = $this->idsVehiculesAccessibles();
    $debut = $request->input('debut') ?: Carbon::now()->startOfMonth()->toDateString();
    $fin = $request->input('fin') ?: Carbon::now()->endOfMonth()->toDateString();
    $query = Vehicule_carburant::whereIn('id_vehicule', $ids)->whereBetween('date', [$debut, $fin])->with(['vehicule.type', 'coursier']);
    if ($request->filled('id_vehicule')) {
      $query->where('id_vehicule', $request->input('id_vehicule'));
    }
    $pleins = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(25)->appends($request->only('debut', 'fin', 'id_vehicule'));
    $base = Vehicule_carburant::whereIn('id_vehicule', $ids)->whereBetween('date', [$debut, $fin]);
    if ($request->filled('id_vehicule')) {
      $base->where('id_vehicule', $request->input('id_vehicule'));
    }
    $totaux = [
      'montant' => (float) (clone $base)->sum('montant'),
      'litres' => (float) (clone $base)->sum('litres'),
      'pleins' => (clone $base)->count(),
    ];
    $totaux['prix_litre'] = $totaux['litres'] > 0 ? round($totaux['montant'] / $totaux['litres'], 2) : null;
    $vehicules = $this->vehiculesAccessibles()->where('statut', 1)->with('coursier')->orderBy('immatriculation')->get();
    $coursiers = \App\Models\coursiers\Coursiers::where('statut', 1)->orderBy('noms')->get();
    return view('multi.pages.parc.carburant', compact('pleins', 'totaux', 'debut', 'fin', 'vehicules', 'coursiers'));
  }

  public function store(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $request->validate($this->regles());
    $vehicule = $this->vehiculeAccessible($request->input('id_vehicule'));
    $plein = new Vehicule_carburant;
    $plein->id_vehicule = $vehicule->id;
    $plein->id_saver = auth()->id();
    $this->remplir($plein, $request, $vehicule);
    $plein->save();
    if ($plein->kilometrage) {
      $vehicule->enregistrerKilometrage($plein->kilometrage, 'carburant', $plein->date, 'Plein de '.$plein->litres.' L', auth()->id());
    }
    session()->flash('message', "Plein de <b>".$plein->litres." L</b> enregistré avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'carburant']));
  }

  public function update(Request $request, $id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $plein = Vehicule_carburant::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($plein->id_vehicule);
    $request->merge(['id_vehicule' => $vehicule->id]);
    $request->validate($this->regles());
    $this->remplir($plein, $request, $vehicule);
    $plein->save();
    if ($plein->kilometrage) {
      $vehicule->enregistrerKilometrage($plein->kilometrage, 'carburant', $plein->date, 'Plein de '.$plein->litres.' L (correction)', auth()->id());
    }
    session()->flash('message', "Plein mis à jour avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'carburant']));
  }

  public function destroy(Request $request, $id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $plein = Vehicule_carburant::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($plein->id_vehicule);
    $plein->delete();
    session()->flash('message', "Plein supprimé avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'carburant']));
  }
}
