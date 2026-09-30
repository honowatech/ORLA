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
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
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