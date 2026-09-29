<?php 

namespace App\Http\Controllers\Admin;

use App\Models\zone\Zone;
use App\Models\ville\Ville;
use App\Models\typeUtilisateur\TypeUtilisateur;
use Illuminate\Http\Request;

class VilleController extends Controller 
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
    $request = request();
    $villes = Ville::where('libelle', 'like', '%' . $request->input('recherche') . '%')
        ->paginate(20);
      session()->flash('search',$request->input('recherche'));
    return view('admin.pages.villes.index',compact('villes'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
    return view('admin.pages.villes.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
    $libelle = $request->input('libelle');
    $code = $request->input('code');
    $request->validate([
      'libelle' => 'bail|required|min:4|unique:ville',
      'code' => 'bail|required|min:2|max:4|unique:ville',
    ]);
    $ville = new Ville;
    $ville->libelle = $libelle;
    $ville->code = $code;
    $ville->save(); 
    $message = "Ville enregistrée avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('ville.index');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($libelle)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
    $villes = Ville::where('libelle',$libelle)->get();
    foreach ($villes as $ville) {
    }
    return view('admin.pages.villes.edit',compact('ville'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request,$id)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
    $libelle = $request->input('libelle');
    $code = $request->input('code');
    $validated = $request->validate([
        'code' => 'bail|required|min:2|max:4',
        'libelle' => 'bail|required|min:4',
      ]);
    if (  Ville::where('libelle',$libelle)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
      'libelle' => 'unique:ville',
      ]);
    }
    if ( Ville::where('code',$code)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
        'code' => 'unique:ville',
      ]);
    }
    $ville = Ville::findOrFail($id);
    $ville->libelle = $libelle;
    $ville->code = $code;
    $ville->save();
    $message = "Ville mise à jour avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('ville.index');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(Request $request,$id)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        $statut = Auth()->user()->statut;
        if ($statut == 0) {
            return redirect()->route('home.error');
        }
        if($type == strtoupper('Super Admin') || $type == strtoupper('agent')){

        }else if($type == strtoupper('coursier')){

            return redirect()->route('home.coursier');

        }else if($type == strtoupper('client')){

            return redirect()->route('home.client');
        }
    if (Zone::where('id_ville',[$id])->count() > 0) {
    $message = "<b class='text-danger'>Echec... </b> Cette Ville est liée à ".Zone::where('id_ville',[$id])->count()." Zone";
    session()->flash('message',$message);
      return redirect()->back();
    }
    $ville = Ville::findOrFail($id);
    $ville->delete();
    $message = "Ville supprimée <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('ville.index');
  }
  
}

?>