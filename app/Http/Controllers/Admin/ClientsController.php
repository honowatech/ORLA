<?php 

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agents\Agents;
use App\Models\Boutiques\Boutiques;
use App\Models\Coursiers\Coursiers;
use App\Models\Clients\Clients;
use App\Models\Commandes\Commandes;
use App\Models\Quartier\Quartier;
use App\Models\Ville\Ville;
use App\Models\TypeClient\TypeClient;
use App\Models\TypeUtilisateur\TypeUtilisateur;
use App\Models\Informations_personnels\Informations_personnels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientsController extends Controller 
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
    if(Quartier::where(strtoupper('libelle'),strtoupper('Speedex'))->count() == 0){
      $quartier = new Quartier;
      $quartier->libelle = 'Speedex';
      $quartier->id_ville = 1;
      $quartier->save();
    }
    $request = request();
    $clients = Clients::where('noms', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('telephone', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('Prenoms', 'like', '%' . $request->input('recherche') . '%')
        ->orderBy('updated_at','desc')
        ->paginate(20);
        foreach($clients as $client){
          $info_perso  = Informations_personnels::where('id_client',[$client->id])->count();
          if ($info_perso == 0) {
            $informations_personnel = new Informations_personnels;
            $informations_personnel->id_client = $client->id;
            $informations_personnel->save();
          }
        }
      session()->flash('search',$request->input('recherche'));
    return view('admin.pages.clients.index',compact('clients'));
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
    if(Quartier::where(strtoupper('libelle'),strtoupper('Speedex'))->count() == 0){
      $quartier = new Quartier;
      $quartier->libelle = 'Speedex';
      $quartier->id_ville = 1;
      $quartier->save();
    }
    if (TypeClient::count() == 0) {
      $type1 = new TypeClient;
      $type1->libelle = 'Entreprise';
      $type1->save();
      $type2 = new TypeClient;
      $type2->libelle = 'Simple';
      $type2->save();
    }
    $types = TypeClient::get();
    return view('admin.pages.clients.create',compact('types'));
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
    $typeClient = $request->input('typeClient');
    $telephone = str_replace(' ','',$request->input('telephone'));
    if ( $add_user_account == 1) 
      session()->flash('error_user','1');
    $validated = $request->validate([
        'noms' => 'bail|required|max:255',
        'typeClient' => 'required|numeric',
        'prenoms' => 'bail|required|max:255',
        'telephone' => 'bail|required|unique:clients|regex:/^[6,2][0-9]{8}$/',
    ]);
    if ( $add_user_account == 1) {
      session()->flash('error_user','1');
      $validated = $request->validate([
        'email' => 'bail|required|unique:users|max:255',
        'password' => 'bail|required|min:8',
        'telephone' => 'bail|required|unique:users|regex:/^[6,2][0-9]{8}$/',
        ]);

      $client = new Clients;
      $client->noms = $noms;
      $client->Prenoms = $prenoms;
      $client->telephone = $telephone;
      $client->type_client = $typeClient;
      $client->statut = 1;
      $client->save();
      $utilisateur = new User;
      $utilisateur->noms = $noms.' '.$prenoms;
      $utilisateur->email = $email;
      $utilisateur->password = Hash::make($password);
      $utilisateur->telephone = $telephone;
      $utilisateur->id_type_utilisateur = 4;
      $utilisateur->id_client = $client->id;
      $utilisateur->save();
      $client = Clients::findOrFail($client->id);
      $client->id_utilisateur = $utilisateur->id;
      $client->save();

      $message = "Client(e) et utilisateur lié créés avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
    }else{
      $client = new Clients;
      $client->noms = $noms;
      $client->Prenoms = $prenoms;
      $client->type_client = $typeClient;
      $client->telephone = $telephone;
      $client->statut = 1;
      $client->save();
      $message = "Client(e) créé avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
    }
    if ( $typeClient == 1) {

      $boutique = new Boutiques;
      $boutique->libelle = 'Speedex';
      $boutique->id_quartier = null;
      $boutique->id_client = $client->id;
      $boutique->quartier = "Cette boutique est chez speedex";
      $boutique->statut = 1;
      $boutique->save();
    }
    return redirect()->route('clients.show',$client->id);
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
    $client = Clients::findOrFail($id);
    $quartiers = Quartier::get();
    return view('admin.pages.clients.info',compact('client','quartiers'));
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
    $client = Clients::findOrFail($id);
    return view('admin.pages.clients.edit',compact('client','villes'));
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
    if ( Coursiers::where('telephone',$telephone)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
        'telephone' => 'unique:coursiers',
      ]);
    }
    $client = Clients::findOrFail($id);
    $client->noms = $noms;
      $client->prenoms = $prenoms;
      $client->telephone = $telephone;
      $client->save();
    if($client->id_utilisateur!=null){
        $utilisateur = User::findOrFail($client->id_utilisateur);
          $utilisateur->noms = $noms.' '.$prenoms;
          $utilisateur->telephone = $telephone;
          $utilisateur->save();
    }
    $message = "Client(e) mis(e) à jour avec <b class='text-success'> Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('clients.show',$client->id);
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
    $client = Clients::findOrFail($id);
    if ($client->statut == 0) {
        $client->statut = 1;
        if($client->id_utilisateur!=null){
            $utilisateur = User::findOrFail($client->id_utilisateur);
            $utilisateur->statut = 1;
            $utilisateur->save();
        }
      $message = $client->noms.' '.$client->prenoms." Activé(e) avec <b class='text-success'> Succès.</b>";
    }else{
        $client->statut = 0;
        if($client->id_utilisateur!=null){
            $utilisateur = User::findOrFail($client->id_utilisateur);
            $utilisateur->statut = 0;
            $utilisateur->save();
        }
        $message = $client->noms.' '.$client->prenoms." Desactivé(e) avec <b class='text-success'> Succès.</b>";
    }
    $client->save();
    session()->flash('message',$message);
    return redirect()->back();
  }
  
}

?>