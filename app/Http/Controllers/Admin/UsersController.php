<?php 

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Agents\Agents;
use App\Models\Coursiers\Coursiers;
use App\Models\Clients\Clients;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UsersController extends Controller 
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
    $users = User::with(['type_utilisateur'])
    ->where('noms', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('telephone', 'like', '%' . $request->input('recherche') . '%')
        ->orderBy('updated_at','desc')
        ->paginate(20);
      session()->flash('search',$request->input('recherche'));
    return view('admin.pages.utilisateurs.index',compact('users'));
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
    $typesUser = TypeUtilisateur::get();
    return view('admin.pages.utilisateurs.create',compact('typesUser'));
  }

  /**
   * montrer la selection qui va choisir le compte lié au user
   *
   * @return Response
   */
  public function compteLie(Request $request)
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
    $typeUser = TypeUtilisateur::findOrFail($request->input('type_user'));
    if($request->input('type_user') == 2){
      $compteLie = Agents::whereNull('id_utilisateur')->where('statut',[1])->get();
    }
    else if($request->input('type_user') == 3){
      $compteLie = coursiers::whereNull('id_utilisateur')->where('statut',[1])->get();
    }
    else if($request->input('type_user') == 4){
      $compteLie = Clients::whereNull('id_utilisateur')->where('statut',[1])->get();
    }else {
      $compteLie = null;
    }
    return view('admin.pages.utilisateurs.compteLie',compact('typeUser','compteLie'));
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
    $email = $request->input('email');
    $password = $request->input('password');
    $telephone = str_replace(' ','',$request->input('telephone'));
    $type_user = $request->input('type_user');
    $id_compte_associe = $request->input('id_compte_associe');
    $validated = $request->validate([
        'noms' => 'bail|required|max:255',
        'email' => 'bail|required|unique:users|max:255',
        'password' => 'bail|required|min:8',
        'type_user' => 'bail|required',
        'telephone' => 'bail|required|unique:users|regex:/^[6,2][0-9]{8}$/',
    ]);
    if ( $id_compte_associe == null && $type_user != 1) {
      $validated = $request->validate([
        'id_compte_associe' => 'bail|required',
      ]);
    }
    $utilisateur = new User;
    $utilisateur->noms = $noms;
    $utilisateur->email = $email;
    $utilisateur->password = Hash::make($password);
    $utilisateur->telephone = $telephone;
    $utilisateur->id_type_utilisateur = $type_user;
    if($type_user == 2){
      $utilisateur->id_agent = $id_compte_associe;
    }
    else if($type_user == 3){
      $utilisateur->id_coursier = $id_compte_associe;
    }
    else if($type_user == 4){
      $utilisateur->id_client = $id_compte_associe;
    }
    $utilisateur->save();
    if($type_user == 2){
        $compte = Agents::findOrFail($id_compte_associe);
        $compte->id_utilisateur = $utilisateur->id;
        $compte->save();
    }
    else if($type_user == 3){
        $compte = Coursiers::findOrFail($id_compte_associe);
        $compte->id_utilisateur = $utilisateur->id;
        $compte->save();
    }
    else if($type_user == 4){
        $compte = Clients::findOrFail($id_compte_associe);
        $compte->id_utilisateur = $utilisateur->id;
        $compte->save();
    }
    $message = "Utilisateur créé avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('users.index');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show(Request $request,$email)
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
    $users = User::with(['type_utilisateur','client_utilisateur','coursier_utilisateur','agent_utilisateur','commandes_enregistrees'])->where('email',$email)->get();
    foreach ($users as $user) {
    }
    return view('admin.pages.utilisateurs.info',compact('user'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($email)
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
    $users = User::where('email',$email)->get();
    foreach ($users as $user) {
    }
    return view('admin.pages.utilisateurs.edit',compact('user'));
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
    $email = $request->input('email');
    $telephone = str_replace(' ','',$request->input('telephone'));
    $validated = $request->validate([
        'noms' => 'bail|required|max:255',
        'email' => 'bail|required|max:255',
        'telephone' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
    ]);
    if ( User::where('email',$email)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
        'email' => 'unique:users',
      ]);
    }
    if ( User::where('telephone',$telephone)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
        'telephone' => 'unique:users',
      ]);
    }
    $utilisateur = User::findOrFail($id);
    $utilisateur->noms = $noms;
    $utilisateur->email = $email;
    $utilisateur->telephone = $telephone;
    if($utilisateur->id_agent != null){
      $agent = Agents::findOrFail($utilisateur->id_agent);
      $agent->telephone = $telephone;
      $agent->save();
    }
    else if($utilisateur->id_coursier != null){
      $coursier = Coursiers::findOrFail($utilisateur->id_coursier);
      $coursier->telephone = $telephone;
      $coursier->save();
    }
    else if($utilisateur->id_client != null){
      $client = Clients::findOrFail($utilisateur->id_client);
      $client->telephone = $telephone;
      $client->save();
    }
    $utilisateur->save();
    $message = "Utilisateur mis à jour avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('users.index');
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
    $user = User::findOrFail($id);
    if ($user->statut == 0) {
        $user->statut = 1;
        $message = $user->noms." Activé(e) avec <b class='text-success'> Succès.</b>";
        if($user->id_agent != null){
          $agent = Agents::findOrFail($user->id_agent);
          $agent->statut = 1;
          $agent->save();
        }
        else if($user->id_coursier != null){
          $coursier = Coursiers::findOrFail($user->id_coursier);
          $coursier->statut = 1;
          $coursier->save();
        }
        else if($user->id_client != null){
          $client = Clients::findOrFail($user->id_client);
          $client->statut = 1;
          $client->save();
        }
    }else{
      $user->statut = 0;
      $message = $user->noms." Desactivé(e) avec <b class='text-success'>Succès.</b>";
        if($user->id_agent != null){
          $agent = Agents::findOrFail($user->id_agent);
          $agent->statut = 0;
          $agent->save();
        }
        else if($user->id_coursier != null){
          $coursier = Coursiers::findOrFail($user->id_coursier);
          $coursier->statut = 0;
          $coursier->save();
        }
        else if($user->id_client != null){
          $client = Clients::findOrFail($user->id_client);
          $client->statut = 0;
          $client->save();
        }
    }
    $user->save();
    session()->flash('message',$message);
    return redirect()->back();
  }
}

?>