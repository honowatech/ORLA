<?php

namespace App\Http\Controllers\Admin;

use App\Models\Vehicule;
use App\Models\agents\Agents;
use App\Models\coursiers\Coursiers;
use App\Models\montant_livraison\Montant_livraison;
use App\Models\quartier\Quartier;
use App\Models\typeUtilisateur\TypeUtilisateur;
use App\Models\ville\Ville;
use App\Models\zone\Zone;
use App\Tenancy\EntrepriseCourante;

/**
 * Assistant de démarrage : les étapes pour qu'une nouvelle entreprise soit
 * prête à prendre ses premières commandes. Chaque étape renvoie vers l'écran
 * existant ; aucune n'est obligatoire.
 */
class DemarrageController extends Controller
{
  public function index(EntrepriseCourante $courante)
  {
        $type = strtoupper(TypeUtilisateur::findOrFail(Auth()->user()->id_type_utilisateur)->libelle);
        if($type == strtoupper('coursier')){
            return redirect()->route('home.coursier');
        }else if($type == strtoupper('client')){
            return redirect()->route('home.client');
        }
    $etapes = [
      ['titre' => "Villes d'activité", 'texte' => 'Les villes où vous livrez.', 'nombre' => Ville::count(), 'route' => 'ville.index'],
      ['titre' => 'Zones de livraison', 'texte' => 'Découpez vos villes en zones confiées à vos livreurs.', 'nombre' => Zone::count(), 'route' => 'zone.create'],
      ['titre' => 'Quartiers', 'texte' => 'Rattachez les quartiers à vos zones.', 'nombre' => Quartier::where('libelle', '!=', Quartier::LIBELLE_SIEGE)->count(), 'route' => 'quartier.create'],
      ['titre' => 'Tarifs de livraison', 'texte' => "Le prix d'une livraison d'une zone à une autre.", 'nombre' => Montant_livraison::count(), 'route' => 'montant_livraison.create'],
      ['titre' => 'Véhicules', 'texte' => 'Votre parc : motos, tricycles, voitures…', 'nombre' => Vehicule::count(), 'route' => 'vehicule.create'],
      ['titre' => 'Livreurs', 'texte' => "Créez leurs comptes puis envoyez-leur une invitation depuis leur fiche utilisateur.", 'nombre' => Coursiers::count(), 'route' => 'coursiers.create'],
      ['titre' => 'Agents (facultatif)', 'texte' => 'Les personnes qui reçoivent et dispatchent les commandes.', 'nombre' => Agents::count(), 'route' => 'agents.create'],
    ];
    $faites = collect($etapes)->where('nombre', '>', 0)->count();
    $entreprise = $courante->entreprise();
    return view('admin.pages.demarrage', compact('etapes', 'faites', 'entreprise'));
  }
}
