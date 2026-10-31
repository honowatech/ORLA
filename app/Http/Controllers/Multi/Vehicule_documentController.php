<?php

namespace App\Http\Controllers\Multi;

use App\Http\Controllers\Multi\Concerns\ParcAccess;
use App\Models\Parc\Vehicule_document;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Documents réglementaires des véhicules (assurance, visite technique, etc.).
 */
class Vehicule_documentController extends Controller
{
  use ParcAccess;

  protected function regles()
  {
    return [
      'id_vehicule' => 'bail|required|integer',
      'type' => 'bail|required|in:'.implode(',', array_keys(Vehicule_document::TYPES)),
      'libelle' => 'bail|nullable|max:255',
      'numero' => 'bail|nullable|max:255',
      'fournisseur' => 'bail|nullable|max:255',
      'date_debut' => 'bail|nullable|date',
      'date_expiration' => 'bail|nullable|date|after_or_equal:date_debut',
      'montant' => 'bail|nullable|numeric|min:0',
      'fichier' => 'bail|nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
      'description' => 'bail|nullable|max:2000',
    ];
  }

  protected function remplir(Vehicule_document $document, Request $request)
  {
    $document->type = $request->input('type');
    $document->libelle = $request->input('libelle') ?: Vehicule_document::TYPES[$request->input('type')];
    $document->numero = $request->input('numero');
    $document->fournisseur = $request->input('fournisseur');
    $document->date_debut = $request->input('date_debut');
    $document->date_expiration = $request->input('date_expiration');
    $document->montant = $request->input('montant');
    $document->description = $request->input('description');
    if ($request->hasFile('fichier')) {
      $fichier = $request->file('fichier');
      $nom = 'doc_'.$document->id_vehicule.'_'.time().'_'.preg_replace('/[^A-Za-z0-9_.-]/', '_', $fichier->getClientOriginalName());
      $fichier->move(public_path('uploads/parc/documents'), $nom);
      if ($document->fichier && file_exists(public_path('uploads/parc/documents/'.$document->fichier))) {
        @unlink(public_path('uploads/parc/documents/'.$document->fichier));
      }
      $document->fichier = $nom;
    }
  }

  /**
   * Échéancier de tous les documents du parc.
   */
  public function index(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $ids = $this->idsVehiculesAccessibles();
    $query = Vehicule_document::whereIn('id_vehicule', $ids)->with(['vehicule.type', 'vehicule.coursier']);
    $etat = $request->input('etat');
    $today = Carbon::now()->toDateString();
    $limite = Carbon::now()->addDays(30)->toDateString();
    if ($etat == 'expire') {
      $query->whereNotNull('date_expiration')->where('date_expiration', '<', $today);
    } elseif ($etat == 'bientot') {
      $query->whereBetween('date_expiration', [$today, $limite]);
    } elseif ($etat == 'valide') {
      $query->where(function ($q) use ($limite) {
        $q->whereNull('date_expiration')->orWhere('date_expiration', '>', $limite);
      });
    }
    if ($request->filled('type') && array_key_exists($request->input('type'), Vehicule_document::TYPES)) {
      $query->where('type', $request->input('type'));
    }
    $recherche = trim((string) $request->input('recherche'));
    if ($recherche != '') {
      $query->where(function ($q) use ($recherche) {
        $q->where('numero', 'like', '%'.$recherche.'%')
          ->orWhere('libelle', 'like', '%'.$recherche.'%')
          ->orWhere('fournisseur', 'like', '%'.$recherche.'%')
          ->orWhereHas('vehicule', function ($qv) use ($recherche) {
            $qv->where('immatriculation', 'like', '%'.$recherche.'%')
               ->orWhere('marque', 'like', '%'.$recherche.'%')
               ->orWhere('modele', 'like', '%'.$recherche.'%');
          });
      });
    }
    $documents = $query->orderByRaw('date_expiration IS NULL')->orderBy('date_expiration')->paginate(20)->appends($request->only('etat', 'type', 'recherche'));
    $compteurs = [
      'expire' => Vehicule_document::whereIn('id_vehicule', $ids)->whereNotNull('date_expiration')->where('date_expiration', '<', $today)->count(),
      'bientot' => Vehicule_document::whereIn('id_vehicule', $ids)->whereBetween('date_expiration', [$today, $limite])->count(),
      'total' => Vehicule_document::whereIn('id_vehicule', $ids)->count(),
    ];
    $vehicules = $this->vehiculesAccessibles()->where('statut', 1)->orderBy('immatriculation')->get();
    session()->flash('search', $recherche);
    return view('multi.pages.parc.documents', compact('documents', 'etat', 'compteurs', 'vehicules'));
  }

  public function store(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $request->validate($this->regles());
    $vehicule = $this->vehiculeAccessible($request->input('id_vehicule'));
    $document = new Vehicule_document;
    $document->id_vehicule = $vehicule->id;
    $document->id_saver = auth()->id();
    $this->remplir($document, $request);
    $document->save();
    session()->flash('message', "Document <b>".$document->libelle."</b> enregistré avec <b class='text-success'>Succès.</b>");
    return redirect()->route('vehicule.show', [$vehicule->id, 'onglet' => 'documents']);
  }

  public function update(Request $request, $id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $document = Vehicule_document::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($document->id_vehicule);
    $request->merge(['id_vehicule' => $vehicule->id]);
    $request->validate($this->regles());
    $this->remplir($document, $request);
    $document->save();
    session()->flash('message', "Document <b>".$document->libelle."</b> mis à jour avec <b class='text-success'>Succès.</b>");
    return redirect()->route('vehicule.show', [$vehicule->id, 'onglet' => 'documents']);
  }

  public function destroy($id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $document = Vehicule_document::findOrFail($id);
    $vehicule = $this->vehiculeAccessible($document->id_vehicule);
    if ($document->fichier && file_exists(public_path('uploads/parc/documents/'.$document->fichier))) {
      @unlink(public_path('uploads/parc/documents/'.$document->fichier));
    }
    $document->delete();
    session()->flash('message', "Document supprimé avec <b class='text-success'>Succès.</b>");
    return redirect()->route('vehicule.show', [$vehicule->id, 'onglet' => 'documents']);
  }

  /**
   * Téléchargement du fichier joint.
   */
  public function telecharger($id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $document = Vehicule_document::findOrFail($id);
    $this->vehiculeAccessible($document->id_vehicule);
    $chemin = public_path('uploads/parc/documents/'.$document->fichier);
    if (!$document->fichier || !file_exists($chemin)) {
      abort(404);
    }
    return response()->download($chemin);
  }
}
