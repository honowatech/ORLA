<?php 

namespace App\Http\Controllers\Coursier;

use App\Models\users\Users;
use App\Models\agents\Agents;
use App\Models\coursiers\Coursiers;
use App\Models\Activity;
use App\Models\clients\Clients;
use App\Models\commandes\Commandes;
use App\Models\typeUtilisateur\TypeUtilisateur;
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
        return view('errors.404');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
        return view('errors.404');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
        return view('errors.404');
   
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show(Request $request,$id)
  {
    $user = Users::findOrFail(Auth()->user()->id);
    return view('coursier.pages.user.info',compact('user'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    $user = Users::findOrFail(Auth()->user()->id);
    return view('coursier.pages.user.edit',compact('user'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request,$id)
  {
    // Seul le profil du compte connecté peut être modifié.
    $id = Auth()->user()->id;
    $noms = $request->input('noms');
    $prenoms = $request->input('prenoms');
    $email = $request->input('email');
    $telephone = str_replace(' ','',$request->input('telephone'));
    $validated = $request->validate([
        'noms' => 'bail|required|max:255',
        'prenoms' => 'bail|required|max:255',
        'email' => 'bail|required|max:255',
        'telephone' => 'bail|required|regex:/^[6,2][0-9]{8}$/',
    ]);
    if ( Users::where('email',$email)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
        'email' => 'unique:users',
      ]);
    }
    if ( Users::where('telephone',$telephone)->whereNotIn('id',[$id])->count() > 0) {
      $validated = $request->validate([
        'telephone' => 'unique:users',
      ]);
    }
    $utilisateur = Users::findOrFail($id);
    $utilisateur->noms = $noms.' '.$prenoms;
    $utilisateur->email = $email;
    $utilisateur->telephone = $telephone;
    if($utilisateur->id_coursier != null){
        $coursier = Coursiers::findOrFail($utilisateur->id_coursier);
        $coursier->noms = $noms;
        $coursier->prenoms = $prenoms;
        $coursier->telephone = $telephone;
        $coursier->save();
    }
    $utilisateur->save();
    $message = "Informations Enregistrées avec <b class='text-success'> Succès. </b>";
    session()->flash('message',$message);
    return redirect()->route('Coursierusers.show',$utilisateur->id);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    $commande = $this->commandeDuCoursier($id);

    $activity = new Activity;
    $activity->lien = route('Coursiercommandes.show',$id);
    $activity->texte_lien = 'Voir la commande';
    $activity->jour = now();
    $activity->heure = now();
    $activity->id_user = Auth::user()->id;
    // dd($commande);
    if ($commande->disponibility == 1) {
        $activity->title = 'Commande Indisponible';
        $activity->color = 'danger';
        $activity->message ='commande rendue Indisponible avec  <b class="text-success">succès</b>';
        $commande->disponibility = 0;
        $message = "Commande rendue Indisponible avec <b class='text-success'> Succès. </b>";
    }else{
        $activity->title = 'Commande Disponible';
        $activity->color = 'success';
        $activity->message ='commande rendue Disponible avec  <b class="text-success">succès</b>';
      $commande->disponibility = 1;
      $message = "Commande rendue Disponible avec <b class='text-success'> Succès.</b>";
    }
    $activity->save();
    $commande->save();
    session()->flash('message',$message);
    return redirect()->back();
  }
}

?>