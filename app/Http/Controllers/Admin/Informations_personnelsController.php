<?php 

namespace App\Http\Controllers\Admin;

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
    $id = $request->input('id');
    $type = $request->input('type');
    $telephone2 = $request->input('telephone2');
    $date_naissance = $request->input('date_naissance');
    $lieu_naissance = $request->input('lieu_naissance');
    $cni = $request->input('cni');
    $date_delivrance = $request->input('date_delivrance');
    $date_expiration = $request->input('date_expiration');
    $lieu_delivrance = $request->input('lieu_delivrance');
    $id_ville = $request->input('id_ville');
    $quartier = $request->input('id_quartier_associe');
    $localisation = $request->input('localisation');
    session()->flash('infos_perso_error','1');
    if($telephone2!=null){
      $validated = $request->validate([
        'telephone2' => 'bail|numeric|regex:/^[6,2][0-9]{8}$/',
      ]);
    }
     $request->validate([
      'id' => 'bail|required',
      'date_naissance' => 'bail|required|date',
      'lieu_naissance' => 'bail|required|min:4',
      'lieu_delivrance' => 'bail|required|min:4',
      'cni' => 'bail|required|min:7|max:25',
      'date_delivrance' => 'bail|required|date|before:date_expiration',
      'date_expiration' => 'bail|required|date|after:date_delivrance',
      'id_ville' => 'bail|numeric|gt:0',
      'id_quartier_associe' => 'bail|gt:0',
    ]);
    if ( Informations_personnels::where('telephone2',$telephone2)->whereNotIn('id_'.$type,[$id])->count() > 0) {
      $validated = $request->validate([
        'telephone2' => 'bail|numeric|unique:informations_personnels',
      ]);
    }
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
    return redirect()->route($type.'s.index');
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
  public function edit($infos)
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
    $table_info = explode('-', $infos);
    session()->flash('infos_perso','1');
    $villes = Ville::orderBy('updated_at','desc')->get();
    if($table_info[1]==0){
      $agent = Agents::findOrFail($table_info[0]);
      return view('admin.pages.agents.edit',compact('agent'));
    }
    if($table_info[1]==1){
      $client = Clients::findOrFail($table_info[0]);
      return view('admin.pages.clients.edit',compact('client','villes'));
    }
    if($table_info[1]==2){
      $coursier = coursiers::findOrFail($table_info[0]);
      return view('admin.pages.coursiers.edit',compact('coursier','villes'));
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
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
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
  
}

?>