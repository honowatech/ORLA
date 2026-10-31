<?php

namespace App\Http\Controllers\Multi;

use App\Http\Controllers\Multi\Concerns\ParcAccess;
use App\Models\Parc\Vehicule_incident;
use Illuminate\Http\Request;

/**
 * Incidents, sinistres, pannes et amendes.
 */
class Vehicule_incidentController extends Controller
{
  use ParcAccess;

  protected function regles()
  {
    return [
      'id_vehicule' => 'bail|required|integer',
      'id_coursier' => 'bail|nullable|integer|exists:coursiers,id',
      'type' => 'bail|required|in:'.implode(',', array_keys(Vehicule_incident::TYPES)),
      'date' => 'bail|required|date',
      'lieu' => 'bail|nullable|max:255',
      'description' => 'bail|required|min:3|max:3000',
      'cout' => 'bail|nullable|numeric|min:0',
      'statut' => 'bail|required|in:'.implode(',', array_keys(Vehicule_incident::STATUTS)),
      'date_resolution' => 'bail|nullable|date|required_if:statut,resolu',
      'immobilise' => 'bail|nullable|boolean',
    ];
  }

  protected function remplir(Vehicule_incident $incident, Request $request, $vehicule)
  {
    $incident->id_coursier = $request->input('id_coursier') ?: $vehicule->id_coursier;
    $incident->type = $request->input('type');
    $incident->date = $request->input('date');
    $incident->lieu = $request->input('lieu');
    $incident->description = $request->input('description');
    $incident->cout = $request->input('cout');
    $incident->statut = $request->input('statut');
    $incident->date_resolution = $request->input('statut') == 'resolu' ? $request->input('date_resolution') : null;
    $incident->immobilise = $request->boolean('immobilise');
  }

  /**
   * Met à jour l'état du véhicule selon l'immobilisation liée aux incidents.
   */
  protected function synchroniser($vehicule)
  {
    $immobilise = $vehicule->incidents()->whereIn('statut', ['ouvert', 'en_cours'])->where('immobilise', 1)->count() > 0;
    if ($immobilise && !in_array($vehicule->etat, ['immobilise', 'hors_service'])) {
      $vehicule->etat = 'immobilise';
      $vehicule->save();
    } elseif (!$immobilise && $vehicule->etat == 'immobilise') {
      $vehicule->etat = $vehicule->id_coursier ? 'en_service' : 'disponible';
      $vehicule->save();
    }
  }

  /**
   * Registre global des incidents.
   */
  public function index(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $ids = $this->idsVehiculesAccessibles();
    $query = Vehicule_incident::whereIn('id_vehicule', $ids)->with(['vehicule.type', 'coursier']);
    $statut = $request->input('statut');
    if ($statut == 'ouverts') {
      $query->whereIn('statut', ['ouvert', 'en_cours']);
    } elseif ($statut && array_key_exists($statut, Vehicule_incident::STATUTS)) {
      $query->where('statut', $statut);
    }
    if ($request->filled('type') && array_key_exists($request->input('type'), Vehicule_incident::TYPES)) {
      $query->where('type', $request->input('type'));
    }
    $recherche = trim((string) $request->input('recherche'));
    if ($recherche != '') {
      $query->where(function ($q) use ($recherche) {
        $q->where('description', 'like', '%'.$recherche.'%')
          ->orWhere('lieu', 'like', '%'.$recherche.'%')
          ->orWhereHas('vehicule', function ($qv) use ($recherche) {
            $qv->where('immatriculation', 'like', '%'.$recherche.'%');
          })
          ->orWhereHas('coursier', function ($qc) use ($recherche) {
            $qc->where('noms', 'like', '%'.$recherche.'%')->orWhere('prenoms', 'like', '%'.$recherche.'%');
          });
      });
    }
    $incidents = $query->orderByRaw("CASE WHEN statut = 'resolu' THEN 1 ELSE 0 END")->orderBy('date', 'desc')->paginate(20)->appends($request->only('statut', 'type', 'recherche'));
    $compteurs = [
      'ouverts' => Vehicule_incident::whereIn('id_vehicule', $ids)->whereIn('statut', ['ouvert', 'en_cours'])->count(),
      'immobilises' => Vehicule_incident::whereIn('id_vehicule', $ids)->whereIn('statut', ['ouvert', 'en_cours'])->where('immobilise', 1)->count(),
      'cout_total' => (float) Vehicule_incident::whereIn('id_vehicule', $ids)->sum('cout'),
    ];
    $vehicules = $this->vehiculesAccessibles()->where('statut', 1)->with('coursier')->orderBy('immatriculation')->get();
    session()->flash('search', $recherche);
    $coursiers = \App\Models\coursiers\Coursiers::where('statut', 1)->orderBy('noms')->get();
    return view('multi.pages.parc.incidents', compact('incidents', 'statut', 'compteurs', 'vehicules', 'coursiers'));
  }

  public function store(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $request->validate($this->regles());
    $vehicule = $this->vehiculeAccessible($request->input('id_vehicule'));
    $incident = new Vehicule_incident;
    $incident->id_vehicule = $vehicule->id;
    $incident->id_saver = auth()->id();
    $this->remplir($incident, $request, $vehicule);
    $incident->save();
    $this->synchroniser($vehicule);
    session()->flash('message', "Incident <b>".$incident->type_libelle."</b> enregistré avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'incidents']));
  }

  public function update(Request $request, $id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $incident = Vehicule_incident::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($incident->id_vehicule);
    $request->merge(['id_vehicule' => $vehicule->id]);
    $request->validate($this->regles());
    $this->remplir($incident, $request, $vehicule);
    $incident->save();
    $this->synchroniser($vehicule);
    session()->flash('message', "Incident mis à jour avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'incidents']));
  }

  public function destroy(Request $request, $id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $incident = Vehicule_incident::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($incident->id_vehicule);
    $incident->delete();
    $this->synchroniser($vehicule);
    session()->flash('message', "Incident supprimé avec <b class='text-success'>Succès.</b>");
    return $this->redirectionRetour($request, route('vehicule.show', [$vehicule->id, 'onglet' => 'incidents']));
  }
}
