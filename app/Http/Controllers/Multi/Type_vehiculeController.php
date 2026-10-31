<?php 

namespace App\Http\Controllers\Multi;

use Illuminate\Http\Request;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\Vehicule;
use App\Models\Type_vehicule;

class Type_vehiculeController extends Controller 
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $user = Auth()->user();
    $filter = filter(['admin','routeur','superviseur_ville'],$user);
    if($filter != 'true'){
      return redirect()->route($filter);
    }
    $filter = filter(['routeur','superville'],$user);
    $request = request();
    $types_vehicule = Type_vehicule::where('libelle', 'like', '%' . $request->input('recherche') . '%')
                    ->orderBy('updated_at','desc')
                    ->paginate(10);
    session()->flash('search',$request->input('recherche'));
    return view('multi.pages.type_vehicule.index',compact('types_vehicule'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    $user = Auth()->user();
        $filter = filter(['admin','routeur','superviseur_ville'],$user);
        if($filter != 'true'){
            return redirect()->route($filter);
        }
        $filter = filter(['routeur','superville'],$user);

    return view('multi.pages.type_vehicule.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    $user = Auth()->user();
        $filter = filter(['admin','routeur','superviseur_ville'],$user);
        if($filter != 'true'){
            return redirect()->route($filter);
        }
        $filter = filter(['routeur','superville'],$user);

    $libelle = $request->input('libelle');
    $description = $request->input('description');
    $request->validate([
      'libelle' => 'bail|required|min:4|unique:type_vehicule',
    ]);
    if ($description != null) {
      $request->validate([
        'description' => 'bail|required|min:2',
      ]);
    }
    $type_vehicule = new Type_vehicule;
    $type_vehicule->libelle = $libelle;
    $type_vehicule->description = $description;
    $this->appliquerContraintes($request, $type_vehicule);
    $type_vehicule->save(); 
    $message = "Type de véhicule <b>".$type_vehicule->libelle."</b> enregistré avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('type_vehicule.index');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $user = Auth()->user();
        $filter = filter(['admin','routeur','superviseur_ville'],$user);
        if($filter != 'true'){
            return redirect()->route($filter);
        }
        $filter = filter(['routeur','superville'],$user);

    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    $user = Auth()->user();
        $filter = filter(['admin','routeur','superviseur_ville'],$user);
        if($filter != 'true'){
            return redirect()->route($filter);
        }
        $filter = filter(['routeur','superville'],$user);

    $type_vehicule = Type_vehicule::findOrFail($id);
    return view('multi.pages.type_vehicule.edit',compact('type_vehicule'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request,$id)
  {
    $user = Auth()->user();
    $filter = filter(['admin','routeur','superviseur_ville'],$user);
    if($filter != 'true'){
        return redirect()->route($filter);
    }
    $filter = filter(['routeur','superville'],$user);
    $libelle = $request->input('libelle');
    $description = $request->input('description');
    if ($description != null) {
      $request->validate([
        'description' => 'bail|required|min:2',
      ]);
    }
    if ( Type_vehicule::where('libelle',$libelle)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
        'code' => 'unique:type_vehicule',
      ]);
    }
    $type_vehicule = Type_vehicule::findOrFail($id);
    $type_vehicule->libelle = $libelle;
    $type_vehicule->description = $description;
    $this->appliquerContraintes($request, $type_vehicule);
    $type_vehicule->save();
    $message = "Type de véhicule <b>".$type_vehicule->libelle."</b> mis à jour avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('type_vehicule.index');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(Request $request,$id)
  {
    $user = Auth()->user();
    $filter = filter(['admin','routeur','superviseur_ville'],$user);
    if($filter != 'true'){
      return redirect()->route($filter);
    }
    $filter = filter(['routeur','superville'],$user);
    $type_vehicule = Type_vehicule::findOrFail($id);
    $type = $request->input('type');
    if ($type == 'delete') {
      if ($type_vehicule->vehicule->count() > 0) {
        $message = "<b class='text-danger'> Erreur.</b> <br> Ce Type de véhicule est connecté à un ou plusieurs véhicules...<br>Déconnecter et rééssayer";
        session()->flash('message',$message);
        return redirect()->back();
      }
      $type_vehicule->delete();
      $message = "Type de Vehicule supprimée <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
      return redirect()->back();
    }else{
      if ($type_vehicule->statut == 0) {
        $type_vehicule->statut = 1;
        $type_vehicule->save();
        $message = "Type de Vehicule activé avec <b class='text-success'> Succès.</b>";
        session()->flash('message',$message);
        return redirect()->back();
        // code...
      }else{
        $type_vehicule->statut = 0;
        $type_vehicule->save();
        $message = "Type de Vehicule desactivé avec <b class='text-success'> Succès.</b>";
        session()->flash('message',$message);
        return redirect()->back();
      }
    }
  }
  

  /** Contraintes d'exploitation (optimiseur de tournées). */
  private function appliquerContraintes(Request $request, Type_vehicule $type_vehicule): void
  {
    $request->validate([
      'vitesse_moyenne' => 'nullable|numeric|min:1|max:200',
      'cout_km' => 'nullable|numeric|min:0',
      'cout_heure' => 'nullable|numeric|min:0',
      'capacite_colis' => 'nullable|integer|min:1',
      'heure_debut_autorisee' => 'nullable|date_format:H:i|required_with:heure_fin_autorisee',
      'heure_fin_autorisee' => 'nullable|date_format:H:i|required_with:heure_debut_autorisee',
    ]);
    $type_vehicule->vitesse_moyenne = $request->filled('vitesse_moyenne') ? $request->input('vitesse_moyenne') : config('routage.vitesse_defaut', 30);
    $type_vehicule->cout_km = $request->input('cout_km') ?: 0;
    $type_vehicule->cout_heure = $request->input('cout_heure') ?: 0;
    $type_vehicule->capacite_colis = $request->filled('capacite_colis') ? $request->input('capacite_colis') : null;
    $type_vehicule->heure_debut_autorisee = $request->input('heure_debut_autorisee') ?: null;
    $type_vehicule->heure_fin_autorisee = $request->input('heure_fin_autorisee') ?: null;
  }
}

?>