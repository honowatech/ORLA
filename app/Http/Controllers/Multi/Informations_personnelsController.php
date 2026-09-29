<?php 

namespace App\Http\Controllers\Multi;

use Illuminate\Http\Request;
use App\Models\ville\Ville;
use App\Models\clients\Clients;
use App\Models\agents\Agents;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\coursiers\Coursiers;
use App\Models\informations_personnels\Informations_personnels;

class Informations_personnelsController extends Controller 
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    $id = $request->input('id');
    $type = $request->input('type');
    $telephone2 = $request->input('telephone2');
    $date_naissance = $request->input('date_naissance');
    $lieu_naissance = $request->input('lieu_naissance');
    $cni = $request->input('cni');
    $date_delivrance = $request->input('date_delivrance');
    $date_expiration = $request->input('date_expiration');
    $lieu_delivrance = $request->input('lieu_delivrance');
    $quartier = $request->input('id_quartier_associe');
    $localisation = $request->input('localisation');
    session()->flash('infos_perso_error','1');
    if($telephone2 != null){
      $validated = $request->validate([
        'telephone2' => 'bail|numeric|regex:/^[6,2][0-9]{8}$/',
      ]);
      if ( Informations_personnels::where('telephone2',$telephone2)->whereNotIn('id_'.$type,[$id])->count() > 0) {
        $validated = $request->validate([
          'telephone2' => 'bail|numeric|unique:informations_personnels',
        ]);
      }
    }
     $request->validate([
      'id' => 'bail|required',
      'date_naissance' => 'bail|required|date',
      'lieu_naissance' => 'bail|required|min:4',
      'lieu_delivrance' => 'bail|required|min:4',
      'cni' => 'bail|required|min:7|max:25',
      'date_delivrance' => 'bail|required|date|before:date_expiration',
      'date_expiration' => 'bail|required|date|after:date_delivrance',
      'id_quartier_associe' => 'bail|numeric|gt:0',
    ]);
    if ( Informations_personnels::where('cni',$cni)->whereNotIn('id_'.$type,[$id])->count() > 0) {
      $validated = $request->validate([
      'cni' => 'unique:informations_personnels',
      ]);
    }
     session()->forget('infos_perso_error','1');
      $info_perso  = Informations_personnels::where('id_'.$type,[$id])
                    ->get('id')->value('id');
      $informations_personnel = Informations_personnels::findOrFail($info_perso);
      $informations_personnel->telephone2 = $telephone2;
      $informations_personnel->date_naissance = $date_naissance;
      $informations_personnel->lieu_naissance = $lieu_naissance;
      $informations_personnel->cni = $cni;
      $informations_personnel->date_delivrance = $date_delivrance;
      $informations_personnel->lieu_delivrance = $lieu_delivrance;
      $informations_personnel->date_expiration = $date_expiration;
      $informations_personnel->id_quartier = $quartier;
      $informations_personnel->localisation = $localisation;
      $informations_personnel->save();
    $message = "Informations personnels mis à jour avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route($type.'s.show',$id);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($infos)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    $table_info = explode('-', $infos);
    session()->flash('infos_perso','1');
    $villes = Ville::orderBy('updated_at','desc')->get();
    if($table_info[1]==0){
      session()->flash('message', "L'authentification des agents n'est pas encore disponible.");
      return redirect()->back();
    }
    if($table_info[1]==1){
      session()->flash('message', "L'authentification des clients n'est pas encore disponible.");
      return redirect()->back();
    }
    if($table_info[1]==2){
      $coursier = coursiers::findOrFail($table_info[0]);
      return view('multi.pages.coursiers.edit',compact('coursier','villes'));
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
        $filter = filter(['routeur','admin','superviseur_ville'],Auth()->user());
        if($filter != 'true'){
            return redirect()->route($filter);
        }
    
  }
  
}

?>