<?php

namespace App\Http\Controllers\Admin;

use App\Billing\Limites;
use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Transaction;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Tenancy\EntrepriseCourante;

/**
 * « Mon abonnement » : offre en cours, consommation, offres disponibles et
 * historique des paiements de l'entreprise connectée.
 */
class MonAbonnementController extends Controller
{
  public function index(EntrepriseCourante $courante, Limites $limites)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        if($type == strtoupper('coursier')){
            return redirect()->route('home.coursier');
        }else if($type == strtoupper('client')){
            return redirect()->route('home.client');
        }
    $entreprise = $courante->entreprise();
    $utilisation = $limites->utilisation($entreprise);
    $offres = Abonnement::where('statut',[1])->orderBy('montant')->get();
    $transactions = Transaction::where('id_client', $entreprise->id)->with('abonnement')->orderBy('created_at','desc')->get();
    $statuts = ['success' => ['Réussi','success'], 'cancelled' => ['Annulé','danger'], 'failed' => ['Échoué','danger'], 'waiting' => ['En attente','secondary']];
    return view('admin.pages.abonnement', compact('entreprise', 'utilisation', 'offres', 'transactions', 'statuts'));
  }
}
