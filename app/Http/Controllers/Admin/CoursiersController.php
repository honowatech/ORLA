<?php 

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\users\Users;
use App\Models\agents\Agents;
use App\Models\informations_personnels\Informations_personnels;
use App\Models\coursiers\Coursiers;
use App\Models\ville\Ville;
use App\Models\details_zone\Details_zone;
use App\Models\zone\Zone;
use App\Models\clients\Clients;
use App\Models\commandes\Commandes;
use App\Models\typeUtilisateur\TypeUtilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoursiersController extends Controller 
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
    $coursiers = Coursiers::with(['coursier_utilisateur','zone_coursier','point_relais'])
    ->where('noms', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('telephone', 'like', '%' . $request->input('recherche') . '%')
        ->orWhere('prenoms', 'like', '%' . $request->input('recherche') . '%')
        ->orderBy('updated_at','desc')
        ->paginate(20);
         foreach($coursiers as $coursier){
          $info_perso  = Informations_personnels::where('id_coursier',[$coursier->id])->count();
          if ($info_perso == 0) {
            $informations_personnel = new Informations_personnels;
            $informations_personnel->id_coursier = $coursier->id;
            $informations_personnel->save();
          }
        }
      session()->flash('search',$request->input('recherche'));
    return view('admin.pages.coursiers.index',compact('coursiers'));
    
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
    return view('admin.pages.coursiers.create');
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

      $coursier = new Coursiers;
      $coursier->noms = $noms;
      $coursier->prenoms = $prenoms;
      $coursier->telephone = $telephone;
      $coursier->statut = 1;
      $coursier->save();
      $utilisateur = new Users;
      $utilisateur->noms = $noms.' '.$prenoms;
      $utilisateur->email = $email;
      $utilisateur->password = Hash::make($password);
      $utilisateur->telephone = $telephone;
      $utilisateur->id_type_utilisateur = 3;
      $utilisateur->id_coursier = $coursier->id;
      $utilisateur->save();
      $coursier = Coursiers::findOrFail($coursier->id);
      $coursier->id_utilisateur = $utilisateur->id;
      $coursier->save();

      $message = "Coursier(e) et utilisateur lié créés avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
    }else{
      $coursier = new Coursiers;
      $coursier->noms = $noms;
      $coursier->prenoms = $prenoms;
      $coursier->telephone = $telephone;
      $coursier->statut = 1;
      $coursier->save();
      $message = "Coursier(e) créé(e) avec <b class='text-success'> Succès.</b>";
      session()->flash('message',$message);
    }
    return redirect()->route('coursiers.show',$coursier->id);
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
    $zones = [];
    $toutes_zones = Zone::where('statut',[1])->get();
    foreach($toutes_zones as $zone){
      if (Details_zone::where('id_zone',$zone->id)->where('id_coursier',$id)->count() ==0) {
        array_push($zones, $zone);
      }
    }
    $coursier = Coursiers::findOrFail($id);
    return view('admin.pages.coursiers.info',compact('coursier','zones'));
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
    $coursier = coursiers::findOrFail($id);
    $villes = Ville::orderBy('updated_at','desc')->get();
    return view('admin.pages.coursiers.edit',compact('coursier','villes'));
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
    $coursier = Coursiers::findOrFail($id);
    $coursier->noms = $noms;
      $coursier->prenoms = $prenoms;
      $coursier->telephone = $telephone;
      $coursier->save();
    if($coursier->id_utilisateur!=null){
        $utilisateur = Users::findOrFail($coursier->id_utilisateur);
          $utilisateur->noms = $noms.' '.$prenoms;
          $utilisateur->telephone = $telephone;
          $utilisateur->save();
    }
    $message = "Coursier(e) mis(e) à jour avec Succès.</b>";
    session()->flash('message',$message);
    return redirect()->route('coursiers.show',$coursier->id);
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
     $coursier = Coursiers::findOrFail($id);
    if ($coursier->statut == 0) {
      $coursier->statut = 1;
        if($coursier->id_utilisateur!=null){
            $utilisateur = Users::findOrFail($coursier->id_utilisateur);
            $utilisateur->statut = 1;
            $utilisateur->save();
        }
      $message = e($coursier->noms).' '.e($coursier->prenoms)." Activé(e) avec <b class='text-success'> Succès.</b>";
    }else{
      $coursier->statut = 0;
        if($coursier->id_utilisateur!=null){
            $utilisateur = Users::findOrFail($coursier->id_utilisateur);
            $utilisateur->statut = 0;
            $utilisateur->save();
        }
      $message = e($coursier->noms).' '.e($coursier->prenoms)." Desactivé(e) avec <b class='text-success'> Succès.</b>";
    }
    $coursier->save();
    session()->flash('message',$message);
    return redirect()->back();
  }
  
}

?>