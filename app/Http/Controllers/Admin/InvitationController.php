<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Tenancy\InscriptionEntreprise;

/**
 * Envoie à un utilisateur de l'entreprise (livreur, agent, client) un lien
 * pour choisir lui-même son mot de passe.
 */
class InvitationController extends Controller
{
  public function envoyer($id, InscriptionEntreprise $inscription)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        if($type == strtoupper('coursier')){
            return redirect()->route('home.coursier');
        }else if($type == strtoupper('client')){
            return redirect()->route('home.client');
        }
    $user = User::findOrFail($id);
    try {
      $inscription->inviter($user);
      $message = "Invitation envoyée à <b>".$user->email."</b> avec <b class='text-success'>Succès</b>";
    } catch (\Throwable $e) {
      report($e);
      $message = "<b class='text-danger'>Erreur.</b><br> L'email d'invitation n'a pas pu être envoyé.";
    }
    session()->flash('message',$message);
    return redirect()->back();
  }
}
