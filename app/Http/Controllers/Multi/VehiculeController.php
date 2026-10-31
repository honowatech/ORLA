<?php 

namespace App\Http\Controllers\Multi;

use App\Http\Controllers\Multi\Concerns\ParcAccess;
use App\Models\coursiers\Coursiers;
use App\Models\Parc\Vehicule_carburant;
use App\Models\Parc\Vehicule_document;
use App\Models\Parc\Vehicule_incident;
use App\Models\Parc\Vehicule_kilometrage;
use App\Models\Parc\Vehicule_maintenance;
use App\Models\Type_vehicule;
use App\Models\Vehicule;
use App\Models\ville\Ville;
use App\Services\ParcService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehiculeController extends Controller 
{
  use ParcAccess;

  /**
   * Règles de validation communes aux champs "parc" du véhicule.
   */
  protected function reglesParc()
  {
    return [
      'etat' => 'bail|nullable|in:'.implode(',', array_keys(Vehicule::ETATS)),
      'kilometrage' => 'bail|nullable|integer|min:0',
      'numero_chassis' => 'bail|nullable|max:255',
      'annee' => 'bail|nullable|integer|min:1950|max:'.(date('Y') + 1),
      'carburant' => 'bail|nullable|in:'.implode(',', array_keys(Vehicule::CARBURANTS)),
      'capacite_reservoir' => 'bail|nullable|numeric|min:0',
      'date_acquisition' => 'bail|nullable|date',
      'prix_acquisition' => 'bail|nullable|numeric|min:0',
    ];
  }

  /**
   * Recopie les champs "parc" depuis la requête vers le véhicule.
   */
  protected function remplirParc(Vehicule $vehicule, Request $request)
  {
    $vehicule->numero_chassis = $request->input('numero_chassis');
    $vehicule->annee = $request->input('annee');
    $vehicule->carburant = $request->input('carburant');
    $vehicule->capacite_reservoir = $request->input('capacite_reservoir');
    $vehicule->date_acquisition = $request->input('date_acquisition');
    $vehicule->prix_acquisition = $request->input('prix_acquisition');
    if ($request->filled('etat')) {
      $vehicule->etat = $request->input('etat');
    }
  }

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $statut = [];
    $etats = [];
    $recherche = trim((string) $request->input('recherche'));
    $lower_search = strtolower($recherche);
    if ($lower_search != '' && str_contains('actif', $lower_search)) {
      array_push($statut, 1);
    }
    if ($lower_search != '' && str_contains('désactivé', $lower_search)) {
      array_push($statut, 0);
    }
    foreach (Vehicule::ETATS as $cle => $libelle) {
      if ($lower_search != '' && str_contains(strtolower($libelle), $lower_search)) {
        $etats[] = $cle;
      }
    }
    $types = Type_vehicule::where('libelle', 'like', '%' . $recherche . '%')->get();
    $villes = Ville::where('libelle', 'like', '%' . $recherche . '%')->get();
    $coursiers = Coursiers::where('noms', 'like', '%' . $recherche . '%')
                            ->orWhere('prenoms', 'like', '%' . $recherche . '%')
                            ->get();
    $vehicules = $this->vehiculesAccessibles()
        ->where(function ($q) use ($recherche, $coursiers, $types, $villes, $statut, $etats) {
          $q->where('immatriculation', 'like', '%' . $recherche . '%')
            ->orWhereIn('id_coursier', $coursiers->pluck('id'))
            ->orWhereIn('id_type', $types->pluck('id'))
            ->orWhereIn('id_ville', $villes->pluck('id'))
            ->orWhereIn('statut', $statut)
            ->orWhereIn('etat', $etats)
            ->orWhere('couleur', 'like', '%' . $recherche . '%')
            ->orWhere('marque', 'like', '%' . $recherche . '%')
            ->orWhere('modele', 'like', '%' . $recherche . '%')
            ->orWhere('numero_chassis', 'like', '%' . $recherche . '%')
            ->orWhere('description', 'like', '%' . $recherche . '%');
        });
    if ($request->filled('etat') && array_key_exists($request->input('etat'), Vehicule::ETATS)) {
      $vehicules->where('etat', $request->input('etat'));
    }
    $vehicules = $vehicules->with(['type', 'ville', 'coursier'])
        ->orderBy('updated_at','desc')
        ->paginate(20)
        ->appends($request->only('recherche', 'etat'));
    session()->flash('search', $recherche);
    return view('multi.pages.vehicule.index',compact('vehicules'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $villes = Ville::get();
    $types_vehicule = Type_vehicule::where('statut',[1])->get();
    return view('multi.pages.vehicule.create',compact('villes','types_vehicule'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $immatriculation = $request->input('immatriculation');
    $couleur = $request->input('couleur');
    $id_type = $request->input('id_type');
    $marque = $request->input('marque');
    $modele = $request->input('modele');
    $description = $request->input('description');
    $id_ville = $request->input('id_ville');
    $request->validate(array_merge([
      'immatriculation' => 'bail|required|min:4|max:255|unique:vehicule',
      'id_type' => 'bail|required|numeric|min:1',
      'marque' => 'bail|required|min:2|max:255',
      'modele' => 'bail|required|min:2|max:255',
      'id_ville' => 'bail|required|numeric|min:1|',
    ], $this->reglesParc()));
    if ($description != null) {
      $request->validate([
        'description' => 'bail|required|min:2',
      ]);
    }
    if ($couleur != null) {
      $request->validate([
        'couleur' => 'bail|required|min:2|max:255|',
      ]);
    }
    $vehicule = new Vehicule;
    $vehicule->immatriculation = $immatriculation;
    $vehicule->couleur = $couleur;
    $vehicule->id_type = $id_type;
    $vehicule->marque = $marque;
    $vehicule->modele = $modele;
    $vehicule->description = $description;
    $vehicule->id_ville = $id_ville;
    $this->remplirParc($vehicule, $request);
    $vehicule->kilometrage = (int) $request->input('kilometrage', 0);
    $vehicule->save();
    if ($request->filled('kilometrage')) {
      $vehicule->enregistrerKilometrage($request->input('kilometrage'), 'manuel', null, 'Kilométrage initial', auth()->id());
    }
    $message = "Véhicule enregistré avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('vehicule.show', $vehicule->id);
  }

  /**
   * Fiche complète du véhicule : résumé, documents, entretiens, carburant,
   * incidents, kilométrage, historique d'attribution.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id, ParcService $parc)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $vehicule = $this->vehiculeAccessible($id);
    $vehicule->load(['type', 'ville', 'coursier']);

    $documents = $vehicule->documents()->orderBy('date_expiration')->get();
    $maintenances = $vehicule->maintenances()->orderByRaw("CASE WHEN statut IN ('planifie','en_cours') THEN 0 ELSE 1 END")->orderBy('date_prevue', 'desc')->orderBy('date_realisation', 'desc')->get();
    $carburants = $vehicule->carburants()->with('coursier')->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
    $incidents = $vehicule->incidents()->with('coursier')->orderBy('date', 'desc')->get();
    $releves = $vehicule->releves_kilometrage()->orderBy('date', 'desc')->orderBy('kilometrage', 'desc')->get();
    $attributions = $vehicule->attributions()->with('coursier')->orderBy('date_debut', 'desc')->get();

    $coursiers = Coursiers::where('statut', 1)
        ->whereHas('user', function ($q) use ($vehicule) {
          $q->where('id_ville', $vehicule->id_ville);
        })
        ->orderBy('noms')->get();
    if ($coursiers->isEmpty()) {
      $coursiers = Coursiers::where('statut', 1)->orderBy('noms')->get();
    }

    $debut_mois = Carbon::now()->startOfMonth()->toDateString();
    $fin_mois = Carbon::now()->endOfMonth()->toDateString();
    $indicateurs = [
      'cout_total' => $vehicule->coutTotal(),
      'cout_mois' => $vehicule->coutTotal($debut_mois, $fin_mois),
      'cout_carburant' => $vehicule->coutCarburant(),
      'cout_maintenance' => $vehicule->coutMaintenance(),
      'cout_incidents' => $vehicule->coutIncidents(),
      'cout_documents' => $vehicule->coutDocuments(),
      'litres_total' => (float) $carburants->sum('litres'),
      'consommation' => $vehicule->consommationMoyenne(),
      'cout_km' => $vehicule->coutParKm(),
      'documents_echeance' => $documents->filter(function ($d) { return $d->etat != 'valide'; })->count(),
      'maintenances_dues' => $vehicule->maintenancesDues()->count(),
      'incidents_ouverts' => $incidents->whereIn('statut', ['ouvert', 'en_cours'])->count(),
      'commandes_livrees' => $vehicule->id_coursier ? $vehicule->coursier->commandes()->where('statut', 'livre')->count() : 0,
    ];
    $alertes = $parc->alertes(collect([$vehicule]));

    return view('multi.pages.vehicule.show', compact(
      'vehicule', 'documents', 'maintenances', 'carburants', 'incidents', 'releves',
      'attributions', 'coursiers', 'indicateurs', 'alertes'
    ));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $vehicule = $this->vehiculeAccessible($id);
    $villes = Ville::get();
    $types_vehicule = Type_vehicule::where('statut',[1])->get();
    return view('multi.pages.vehicule.edit',compact('vehicule','villes','types_vehicule'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request,$id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $user = auth()->user();
    $immatriculation = $request->input('immatriculation');
    $couleur = $request->input('couleur');
    $id_type = $request->input('id_type');
    $marque = $request->input('marque');
    $modele = $request->input('modele');
    $description = $request->input('description');
    $id_ville = $request->input('id_ville');
    $request->validate(array_merge([
      'immatriculation' => 'bail|required|min:4|max:255',
      'id_type' => 'bail|required|numeric|min:1',
      'marque' => 'bail|required|min:2|max:255',
      'modele' => 'bail|required|min:2|max:255',
      'id_ville' => 'bail|required|numeric|min:1|',
    ], $this->reglesParc()));
    if ( Vehicule::where('immatriculation',$immatriculation)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
      'immatriculation' => 'unique:vehicule',
      ]);
    }
    if ($description != null) {
      $request->validate([
        'description' => 'bail|required|min:2',
      ]);
    }
    if ($couleur != null) {
      $request->validate([
        'couleur' => 'bail|required|min:2|max:255|',
      ]);
    }
    $vehicule = Vehicule::findOrFail($id);
    if (filter(['routeur','superviseur_ville'],$user) == 'true' && $vehicule->id_ville != $user->id_ville) {
      $message = "<b class='text-danger'> Erreur.</b> <br> Ce véhicule n'appartient pas à votre ville";
      session()->flash('message',$message);
      return redirect()->route('vehicule.index');
    }
    $vehicule->immatriculation = $immatriculation;
    $vehicule->couleur = $couleur;
    $vehicule->id_type = $id_type;
    $vehicule->marque = $marque;
    $vehicule->modele = $modele;
    $vehicule->description = $description;
    $vehicule->id_ville = $id_ville;
    $this->remplirParc($vehicule, $request);
    $vehicule->save();
    if ($request->filled('kilometrage') && (int) $request->input('kilometrage') != (int) $vehicule->kilometrage) {
      $vehicule->enregistrerKilometrage($request->input('kilometrage'), 'manuel', null, 'Mise à jour de la fiche', auth()->id());
    }
    $message = "Véhicule mis à jour avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('vehicule.show', $vehicule->id);
  }

  /**
   * Attribue le véhicule à un coursier (depuis la fiche véhicule).
   */
  public function attribuer(Request $request, $id, ParcService $parc)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $vehicule = $this->vehiculeAccessible($id);
    $request->validate([
      'id_coursier' => 'bail|required|integer|exists:coursiers,id',
      'kilometrage' => 'bail|nullable|integer|min:0',
    ]);
    if ($vehicule->statut == 0 || in_array($vehicule->etat, ['hors_service'])) {
      session()->flash('message', "<b class='text-danger'>Erreur.</b><br>Un véhicule désactivé ou hors service ne peut pas être attribué.");
      return redirect()->back();
    }
    $attribution = $parc->attribuer($vehicule, $request->input('id_coursier'), $request->input('kilometrage'), auth()->id());
    $coursier = $attribution->coursier;
    session()->flash('message', "Véhicule ".$vehicule->nom_complet." attribué à <b>".$coursier->noms.' '.$coursier->prenoms."</b> avec <b class='text-success'>Succès.</b>");
    return redirect()->route('vehicule.show', $vehicule->id);
  }

  /**
   * Change l'état opérationnel du véhicule.
   */
  public function etat(Request $request, $id)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $vehicule = $this->vehiculeAccessible($id);
    $request->validate([
      'etat' => 'bail|required|in:'.implode(',', array_keys(Vehicule::ETATS)),
    ]);
    $vehicule->etat = $request->input('etat');
    $vehicule->save();
    session()->flash('message', "État du véhicule ".$vehicule->nom_complet." passé à <b>".$vehicule->etat_libelle."</b> avec <b class='text-success'>Succès.</b>");
    return redirect()->back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(Request $request,$id, ParcService $parc)
  {
    if ($redirect = $this->accesParc()) {
      return $redirect;
    }
    $vehicule = $this->vehiculeAccessible($id);
    $nom_vehicule = $vehicule->modele.' '.$vehicule->marque.', ';
    if ($request->has('attribuate')) {
        $parc->desattribuer($vehicule, $request->input('kilometrage'), auth()->id());
        $message = "Véhicule  ".$nom_vehicule." desattribué  avec <b class='text-success'> Succès.</b>";
        session()->flash('message',$message);
        return redirect()->back();
    }
    $type = $request->input('type');
    if ($type == 'delete') {
      if ($vehicule->documents()->count() + $vehicule->maintenances()->count() + $vehicule->carburants()->count() + $vehicule->incidents()->count() > 0) {
        $message = "<b class='text-danger'> Erreur.</b> <br> Ce véhicule possède un historique (documents, entretiens, carburant ou incidents).<br>Désactivez-le ou passez-le hors service plutôt que de le supprimer.";
        session()->flash('message',$message);
        return redirect()->back();
      }
      $vehicule->attributions()->delete();
      $vehicule->releves_kilometrage()->delete();
      $vehicule->delete();
      $message = "Véhicule  ".$nom_vehicule." supprimée <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
      return redirect()->route('vehicule.index');
    }else{
      if ($vehicule->statut == 0) {
        $vehicule->statut = 1;
        $vehicule->save();
        $message = "vehicule ".$nom_vehicule." Activé avec Succès.</b>";
        session()->flash('message',$message);
        return redirect()->back();
      }else{
        $vehicule->statut = 0;
        $vehicule->save();
        $message = "Véhicule  ".$nom_vehicule." desactivé avec <b class='text-success'> Succès.</b>";
        session()->flash('message',$message);
        return redirect()->back();
      }
    }
  }
  
}
