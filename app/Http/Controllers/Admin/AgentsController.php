<?php 

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agents\Agents;
use App\Models\Coursiers\Coursiers;
use App\Models\Ville\Ville;
use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Informations_personnels\Informations_personnels;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgentsController extends Controller 
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
    $agents = Agents::where('noms', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('telephone', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('prenoms', 'like', '%' . $request->input('recherche') . '%')
        ->orderBy('updated_at','desc')
        ->paginate(20);
        foreach($agents as $agent){
          $info_perso  = Informations_personnels::where('id_agent',[$agent->id])->count();
          if ($info_perso == 0) {
            $informations_personnel = new Informations_personnels;
            $informations_personnel->id_agent = $agent->id;
            $informations_personnel->save();
          }
        }
      session()->flash('search',$request->input('recherche'));
    return view('admin.pages.agents.index',compact('agents'));
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
    return view('admin.pages.agents.create');
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
    $noms = $request->input('noms');
    $prenoms = $request->input('prenoms');
    $add_user_account = $request->input('add_user_account');
    $email = $request->input('email');
    $password = $request->input('password');
    $telephone = str_replace(' ','',$request->input('telephone'));
    if ( $add_user_account == 1) 
      session()->flash('error_user','1');
    $validated = $request->validate([
        'noms' => 'bail|required|max:255',
        'prenoms' => 'bail|required|max:255',
        'telephone' => 'bail|required|unique:coursiers|regex:/^[6,2][0-9]{8}$/',
    ]);
    if ( $add_user_account == 1) {
      session()->flash('error_user','1');
      $validated = $request->validate([
        'email' => 'bail|required|unique:users|max:255',
        'password' => 'bail|required|min:8',
        'telephone' => 'bail|required|unique:users|regex:/^[6,2][0-9]{8}$/',
        ]);

      $agent = new Agents;
      $agent->noms = $noms;
      $agent->prenoms = $prenoms;
      $agent->telephone = $telephone;
      $agent->statut = 1;
      $agent->save();
      $utilisateur = new User;
      $utilisateur->noms = $noms.' '.$prenoms;
      $utilisateur->email = $email;
      $utilisateur->password = Hash::make($password);
      $utilisateur->telephone = $telephone;
      $utilisateur->id_type_utilisateur = 2;
      $utilisateur->id_agent = $agent->id;
      $utilisateur->save();
      $agent = Agents::findOrFail($agent->id);
      $agent->id_utilisateur = $utilisateur->id;
      $agent->save();

      $message = "Agent et utilisateur lié créés avec <b class='text-success'>Succès.</b>";
      session()->flash('message',$message);
    }else{
      $agent = new Agents;
      $agent->noms = $noms;
      $agent->prenoms = $prenoms;
      $agent->telephone = $telephone;
      $agent->statut = 1;
      $agent->save();
      $message = "Agent créé avec <b class='text-success'>Succès.</b>";
      session()->flash('message',$message);
    }
    return redirect()->route('agents.show',$agent->id);
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
    $agent = Agents::findOrFail($id);
    $agent->id_utilisateur != null ? $commandes_enregistrees = Commandes::where('id_saver',[$agent->compte_agent->id])->count() : $commandes_enregistrees = 'Aucune';
    return view('admin.pages.agents.info',compact('agent','commandes_enregistrees'));
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
    $villes = Ville::orderBy('updated_at','desc')->get();
    $agent = Agents::findOrFail($id);
    return view('admin.pages.agents.edit',compact('agent','villes'));
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
    $noms = $request->input('noms');
    $telephone = str_replace(' ','',$request->input('telephone'));
    $prenoms = $request->input('prenoms');
    $validated = $request->validate([
        'noms' => 'bail|required|max:255',
        'prenoms' => 'bail|required|max:255',
        'telephone' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
    ]);
    if ( Agents::where('telephone',$telephone)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
        'telephone' => 'unique:agents',
      ]);
    }
    $agent = Agents::findOrFail($id);
    $agent->noms = $noms;
      $agent->prenoms = $prenoms;
      $agent->telephone = $telephone;
      $agent->save();
    if($agent->id_utilisateur!=null){
        $utilisateur = User::findOrFail($agent->id_utilisateur);
          $utilisateur->noms = $noms.' '.$prenoms;
          $utilisateur->telephone = $telephone;
          $utilisateur->save();
    }
    $message = "Agent mis à jour avec <b class='text-success'>Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('agents.show',$agent->id);
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
    $agent = Agents::findOrFail($id);
      if ($agent->statut == 0) {
        $agent->statut = 1;
        if($agent->id_utilisateur!=null){
            $utilisateur = User::findOrFail($agent->id_utilisateur);
            $utilisateur->statut = 1;
            $utilisateur->save();
        }
        $message = $agent->noms.' '.$agent->prenoms." <b class='text-success'> Activé avec Succès.</b>";
      }else{
        if($agent->id_utilisateur!=null){
            $utilisateur = User::findOrFail($agent->id_utilisateur);
            $utilisateur->statut = 0;
            $utilisateur->save();
        }
        $agent->statut = 0;
        $message = $agent->noms.' '.$agent->prenoms." <b class='text-success'> Desactivé avec Succès.</b>";
      }
      $agent->save();
      session()->flash('message',$message);
      return redirect()->back();
    
  }
  
}


?>