<?php

namespace App\Http\Controllers\Admin;

use App\Models\Montant_livraison\Montant_livraison;
use App\Models\Zone\Zone;
use Illuminate\Http\Request;

class Montant_livraisonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $deux_cote = false;
        $table = [];
        $zones_depart = Zone::get();
        $zones_arrivee = Zone::get();
        // savoir la zone de départ
        foreach ($zones_depart as $zone_depart) {
            array_push($table, $zone_depart);
            // savoir la zone d'arrivée
            foreach ($zones_arrivee as $zone_arrivee) {
                // on vérifie si la ligne existe sinon on la crèe
                if (Montant_livraison::where('id_zone_colis', $zone_depart->id)->where('id_zone_livraison', $zone_arrivee->id)->count() == 0 && $zone_depart != $zone_arrivee) {
                    // ici on erengistre les deux cotés
                    if ($deux_cote) {
                        $montant_livraison = new Montant_livraison;
                        $montant_livraison->id_zone_colis = $zone_depart->id;
                        $montant_livraison->id_zone_livraison = $zone_arrivee->id;
                        $montant_livraison->montant = 1000;
                        $montant_livraison->save();
                    } else {
                        // On enregistre un coté
                        if (! in_array($zone_arrivee, $table)) {
                            $montant_livraison = new Montant_livraison;
                            $montant_livraison->id_zone_colis = $zone_depart->id;
                            $montant_livraison->id_zone_livraison = $zone_arrivee->id;
                            $montant_livraison->montant = 1000;
                            $montant_livraison->save();
                        }
                    }
                    // ici on fait l'anregistrement
                }
            }
        }
        $verificate_table = [];
        $zones = Zone::where('libelle', 'like', '%'.$request->input('recherche').'%')
            ->orderBy('statut')
            ->get();
        foreach ($zones as $zone) {
            array_push($verificate_table, $zone->id);
        }
        $montant_livraisons = Montant_livraison::whereIn('id_zone_colis', $verificate_table)
            ->orWhereIn('id_zone_livraison', $verificate_table)
            ->orWhere('montant', 'like', '%'.$request->input('recherche').'%')
            ->paginate(20);
        session()->flash('recherche', $request->input('recherche'));

        return view('admin.pages.montant_livraison.index', compact('montant_livraisons'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id = $request->input('id_montant');
        $montant = $request->input('montant');
        $montant_livraison = Montant_livraison::findOrFail($id);
        $montant_livraison->montant = $montant;
        $montant_livraison->save();

        return 'Montant modifié avec Succès';
    }
}
