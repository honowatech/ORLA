<?php 

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\users\Users;
use App\Models\agents\Agents;
use App\Models\coursiers\Coursiers;
use App\Models\clients\Clients;
use App\Models\commandes\Commandes;
use App\Models\boutiques\Boutiques;
use App\Models\quartier\Quartier;
use App\Models\ville\Ville;
use App\Models\typeClient\TypeClient;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\informations_personnels\Informations_personnels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class BoutiquesController extends Controller 
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
    $libelle = $request->input('libelle');
    $id_quartier = $request->input('id_quartier');
    $id_client = $request->input('id_client');
    $localisation = $request->input('localisation');
      session()->flash('erreur','erreur');
    $validated = $request->validate([
        'libelle' => 'bail|required|unique:boutiques|min:4|max:25',
        'id_quartier' => 'bail|required|numeric|min:1',
        'id_client' => 'bail|required|numeric|min:1',
        'localisation' => 'bail|required|max:255|min:10',
        ]);
    session()->forget('erreur');
      $boutique = new Boutiques;
      $boutique->libelle = $libelle;
      $boutique->id_quartier = $id_quartier;
      $boutique->id_client = $id_client;
      $boutique->quartier = $localisation;
      $boutique->statut = 1;
      $boutique->save();
    $message = "Boutique crée avec <b class='text-success'>Succès.</b>";
    session()->flash('message',$message);
    return redirect()->back();
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
  public function edit($id)
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
    $boutique = Boutiques::findOrFail($id);
    if ($boutique->statut == 0) {
      $boutique->statut = 1;
      $message = 'Boutique "'.$boutique->libelle.'" Activée avec <b class="text-success"> Succès.</b>';
    }else{
      $boutique->statut = 0;
      $message = 'Boutique "'.$boutique->libelle.'" Desactivée avec <b class="text-success"> Succès.</b>';
    }
    $boutique->save();
    session()->flash('message',$message);
    return redirect()->back();
  }
  
}

?>