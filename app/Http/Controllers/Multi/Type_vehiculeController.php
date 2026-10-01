<?php 

namespace App\Http\Controllers\Multi;

use Illuminate\Http\Request;
use App\Models\TypeUtilisateur\TypeUtilisateur;
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
  
}

?>