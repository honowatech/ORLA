<?php

namespace App\Http\Controllers\Multi;

use App\Http\Controllers\Multi\Concerns\ParcAccess;
use App\Models\Parc\Vehicule_maintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Entretiens et réparations des véhicules (planification et historique).
 */
class Vehicule_maintenanceController extends Controller
{
  use ParcAccess;

  protected function regles()
  {
    return [
      'id_vehicule' => 'bail|required|integer',
      'type' => 'bail|required|in:'.implode(',', array_keys(Vehicule_maintenance::TYPES)),
      'libelle' => 'bail|required|min:2|max:255',
      'description' => 'bail|nullable|max:2000',
      'statut' => 'bail|required|in:'.implode(',', array_keys(Vehicule_maintenance::STATUTS)),
      'date_prevue' => 'bail|nullable|date',
      'date_realisation' => 'bail|nullable|date|required_if:statut,realise',
      'kilometrage' => 'bail|nullable|integer|min:0',
      'cout' => 'bail|nullable|numeric|min:0',
      'prestataire' => 'bail|nullable|max:255',
      'prochaine_date' => 'bail|nullable|date',
      'prochain_kilometrage' => 'bail|nullable|integer|min:0',
    ];
  }

  protected function remplir(Vehicule_maintenance $maintenance, Request $request)
  {
    $maintenance->type = $request->input('type');
    $maintenance->libelle = $request->input('libelle');
    $maintenance->description = $request->input('description');
    $maintenance->statut = $request->input('statut');
    $maintenance->date_prevue = $request->input('date_prevue');
    $maintenance->date_realisation = $request->input('date_realisation');
    $maintenance->kilometrage = $request->input('kilometrage');
    $maintenance->cout = $request->input('cout');
    $maintenance->prestataire = $request->input('prestataire');
    $maintenance->prochaine_date = $request->input('prochaine_date');
    $maintenance->prochain_kilometrage = $request->input('prochain_kilometrage');
  }

  /**
   * Après enregistrement : synchronise kilométrage et état du véhicule,
   * et planifie automatiquement le prochain entretien si demandé.
   */
  protected function synchroniser(Vehicule_maintenance $maintenance, $vehicule, $planifier_suivant = false)
  {
    if ($maintenance->statut == 'realise' && $maintenance->kilometrage) {
      $vehicule->enregistrerKilometrage($maintenance->kilometrage, 'maintenance', $maintenance->date_realisation, $maintenance->libelle, auth()->id());
    }
    if ($maintenance->statut == 'en_cours' && $vehicule->etat != 'en_maintenance') {
      $vehicule->etat = 'en_maintenance';
      $vehicule->save();
    }
    if (in_array($maintenance->statut, ['realise', 'annule']) && $vehicule->etat == 'en_maintenance'
        && $vehicule->maintenances()->where('statut', 'en_cours')->count() == 0) {
      $vehicule->etat = $vehicule->id_coursier ? 'en_service' : 'disponible';
      $vehicule->save();
    }
    if ($planifier_suivant && $maintenance->statut == 'realise' && ($maintenance->prochaine_date || $maintenance->prochain_kilometrage)) {
      $suivant = new Vehicule_maintenance;
      $suivant->id_vehicule = $vehicule->id;
      $suivant->type = $maintenance->type;
      $suivant->libelle = $maintenance->libelle;
      $suivant->description = 'Planifié automatiquement à la suite de l\'entretien du '.($maintenance->date_realisation ? Ladate($maintenance->date_realisation) : Ladate(Carbon::now()));
      $suivant->statut = 'planifie';
      $suivant->date_prevue = $maintenance->prochaine_date;
      $suivant->prochain_kilometrage = $maintenance->prochain_kilometrage;
      $suivant->prestataire = $maintenance->prestataire;
      $suivant->id_saver = auth()->id();
      $suivant->save();
    }
  }

  /**
   * Planning global des entretiens.
   */
  public function index(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $ids = $this->idsVehiculesAccessibles();
    $query = Vehicule_maintenance::whereIn('id_vehicule', $ids)->with(['vehicule.type', 'vehicule.coursier']);
    $statut = $request->input('statut');
    $today = Carbon::now()->toDateString();
    if ($statut == 'retard') {
      $query->whereIn('statut', ['planifie', 'en_cours'])->whereNotNull('date_prevue')->where('date_prevue', '<', $today);
    } elseif ($statut == 'a_venir') {
      $query->whereIn('statut', ['planifie', 'en_cours']);
    } elseif ($statut && array_key_exists($statut, Vehicule_maintenance::STATUTS)) {
      $query->where('statut', $statut);
    }
    if ($request->filled('type') && array_key_exists($request->input('type'), Vehicule_maintenance::TYPES)) {
      $query->where('type', $request->input('type'));
    }
    $recherche = trim((string) $request->input('recherche'));
    if ($recherche != '') {
      $query->where(function ($q) use ($recherche) {
        $q->where('libelle', 'like', '%'.$recherche.'%')
          ->orWhere('prestataire', 'like', '%'.$recherche.'%')
          ->orWhereHas('vehicule', function ($qv) use ($recherche) {
            $qv->where('immatriculation', 'like', '%'.$recherche.'%')
               ->orWhere('marque', 'like', '%'.$recherche.'%')
               ->orWhere('modele', 'like', '%'.$recherche.'%');
          });
      });
    }
    $maintenances = $query->orderByRaw("CASE WHEN statut IN ('planifie','en_cours') THEN 0 ELSE 1 END")
      ->orderByRaw('date_prevue IS NULL')->orderBy('date_prevue')->orderBy('date_realisation', 'desc')
      ->paginate(20)->appends($request->only('statut', 'type', 'recherche'));
    $compteurs = [
      'retard' => Vehicule_maintenance::whereIn('id_vehicule', $ids)->whereIn('statut', ['planifie', 'en_cours'])->whereNotNull('date_prevue')->where('date_prevue', '<', $today)->count(),
      'a_venir' => Vehicule_maintenance::whereIn('id_vehicule', $ids)->whereIn('statut', ['planifie', 'en_cours'])->count(),
      'cout_mois' => (float) Vehicule_maintenance::whereIn('id_vehicule', $ids)->where('statut', 'realise')->whereBetween('date_realisation', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()])->sum('cout'),
    ];
    $vehicules = $this->vehiculesAccessibles()->where('statut', 1)->orderBy('immatriculation')->get();
    session()->flash('search', $recherche);
    $coursiers = \App\Models\coursiers\Coursiers::where('statut', 1)->orderBy('noms')->get();
    return view('multi.pages.parc.entretiens', compact('maintenances', 'statut', 'compteurs', 'vehicules', 'coursiers'));
  }

  public function store(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $request->validate($this->regles());
    $vehicule = $this->vehiculeAccessible($request->input('id_vehicule'));
    $maintenance = new Vehicule_maintenance;
    $maintenance->id_vehicule = $vehicule->id;
    $maintenance->id_saver = auth()->id();
    $this->remplir($maintenance, $request);
    $maintenance->save();
    $this->synchroniser($maintenance, $vehicule, $request->boolean('planifier_suivant'));
    session()->flash('message', "Entretien <b>".$maintenance->libelle."</b> enregistré avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'entretiens']));
  }

  public function update(Request $request, $id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $maintenance = Vehicule_maintenance::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($maintenance->id_vehicule);
    $request->merge(['id_vehicule' => $vehicule->id]);
    $request->validate($this->regles());
    $etait_realise = $maintenance->statut == 'realise';
    $this->remplir($maintenance, $request);
    $maintenance->save();
    $this->synchroniser($maintenance, $vehicule, !$etait_realise && $request->boolean('planifier_suivant'));
    session()->flash('message', "Entretien <b>".$maintenance->libelle."</b> mis à jour avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'entretiens']));
  }

  public function destroy(Request $request, $id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $maintenance = Vehicule_maintenance::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($maintenance->id_vehicule);
    $maintenance->delete();
    if ($vehicule->etat == 'en_maintenance' && $vehicule->maintenances()->where('statut', 'en_cours')->count() == 0) {
      $vehicule->etat = $vehicule->id_coursier ? 'en_service' : 'disponible';
      $vehicule->save();
    }
    session()->flash('message', "Entretien supprimé avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'entretiens']));
  }
}
