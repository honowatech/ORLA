<?php 

namespace App\Http\Controllers\Admin;

use App\Models\users\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\typeUtilisateur\TypeUtilisateur;
use Illuminate\Http\Request;

class PasswordController extends Controller 
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
    $utilisateur = Users::findOrFail($id);
    $this->protegerSuperAdmin($utilisateur->id_type_utilisateur);
    // Mot de passe aléatoire, affiché une seule fois à l'administrateur.
    $mot_de_passe = Str::password(12, symbols: false);
    $utilisateur->password = Hash::make($mot_de_passe);
    $utilisateur->save();
    $message = "Mot de passe réinitialisé avec <b class='text-success'> Succès.</b><br>"
      ."Nouveau mot de passe de ".e($utilisateur->noms)." : <b class='user-select-all'>".e($mot_de_passe)."</b><br>"
      ."<small>Communiquez-le à l'utilisateur : il ne sera plus affiché.</small>";
    session()->flash('message',$message);
    return redirect()->back();
    }
}

?> 