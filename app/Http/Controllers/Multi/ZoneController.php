<?php

namespace App\Http\Controllers\Multi;

use App\Models\montant_livraison\Montant_livraison;
use App\Models\quartier\Quartier;
use App\Models\ville\Ville;
use App\Models\zone\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {

        $statut = [];
        $lower_search = strtolower(trim($request->input('search')));
        if (str_contains('actif', $lower_search)) {
            array_push($statut, 1);
        }
        if (str_contains('désactivé', $lower_search)) {
            array_push($statut, 0);
        }
        $villes = Ville::where('libelle', 'like', '%'.$request->input('search').'%')->get();
        $quartiers = Quartier::where('libelle', 'like', '%'.$request->input('search').'%')->whereNotNull('id_zone')->get();
        $zones = Zone::where('libelle', 'like', '%'.$request->input('search').'%')
            ->orWhereIn('id', $quartiers->pluck('id_zone'))
            ->orWhereIn('id_ville', $villes->pluck('id'))
            ->orWhereIn('statut', $statut)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        session()->flash('search', $request->input('search'));

        return view('multi.pages.zones.index', compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $villes = Ville::orderBy('updated_at', 'desc')->get();
        $quartiers = Quartier::whereNull('id_zone')->orderBy('updated_at', 'desc')->get();

        return view('multi.pages.zones.create', compact('villes', 'quartiers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function quartierLie(Request $request)
    {
        $id_ville = $request->input('id_ville');
        $all = $request->input('all');
        $id_ville != '0' ? $ville = Ville::findOrFail($id_ville) : $ville = null;
        if ($request->input('all') == 1) {
            $quartiers = Quartier::where('id_ville', [$id_ville])
                ->get();
        } else {
            $quartiers = Quartier::where('id_ville', [$id_ville])
                ->whereNull('id_zone')
                ->get();
        }

        return view('multi.pages.zones.quartierLie', compact('quartiers', 'ville', 'all'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id_quartiers_associe = $request->input('id_quartier_associe');
        $id_ville = $request->input('id_ville');
        $libelle = $request->input('libelle');
        $request->validate([
            'libelle' => 'bail|required|min:4|unique:zone',
            'id_ville' => 'bail|required|numeric',
            'id_quartier_associe' => 'bail|required',
        ]);
        $zone = new Zone;
        $zone->libelle = $libelle;
        $zone->id_ville = $id_ville;
        $zone->statut = 1;
        $zone->save();
        $id_zone = $zone->id;
        foreach ($id_quartiers_associe as $id_quartier_associe) {
            $quartier = Quartier::findOrFail($id_quartier_associe);
            $quartier->id_zone = $zone->id;
            $quartier->save();
        }
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
        $message = "Zone créée avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('zone.show', $id_zone);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

        $zones = Zone::with(['details_zone', 'ville', 'quartiers', 'montantlivraisoncolis', 'montantlivraisonlivraison'])->findOrFail($id);
        $quartiers_libres = Quartier::where('id_ville', [$zones->ville->id])->whereNull('id_zone')->get();

        return view('multi.pages.zones.info', compact('zones', 'quartiers_libres'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $zone = Zone::findOrFail($id);

        return view('multi.pages.zones.edit', compact('zone'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $libelle = $request->input('libelle');
        $validated = $request->validate([
            'libelle' => 'bail|required|min:4',
        ]);
        if (Zone::where('libelle', $libelle)->whereNotIn('id', [$id])->count() > 0) {
            $validated = $request->validate([
                'libelle' => 'unique:zone',
            ]);
        }
        $zone = Zone::findOrFail($id);
        $zone->libelle = $libelle;
        $zone->save();
        $message = "Zone mise à jour avec <b class='text-success'> Succès.</b>";
        session()->flash('message', $message);

        return redirect()->route('zone.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->input('remove') != null) {
            if (Quartier::where('id_zone', [$id])->count() > 1) {
                $quartier = Quartier::findOrFail($request->input('id_quartier'));
                $quartier->id_zone = null;
                $quartier->save();
                $message = "Quartier Supprimé de la liste avec <b class='text-success'> succès</b>";
                session()->flash('message', $message);
            } else {
                $message = "<b class='text-danger'>Erreur fatale</b><br>Une ville doit être liée à au moins (1) Quartier";
                session()->flash('message', $message);
            }

            return redirect()->back();
        }
        if ($request->input('add') != null) {
            $id_quartiers_associe = $request->input('id_quartier_associe');
            if (empty($id_quartiers_associe)) {
                $message = "<b class='text-danger'>Vous n'avez choisis aucun Quartier</b>";
                session()->flash('message', $message);
            } else {
                foreach ($id_quartiers_associe as $id_quartier_associe) {
                    $quartier = Quartier::findOrFail($id_quartier_associe);
                    $quartier->id_zone = $id;
                    $quartier->save();
                }
                $message = "<b class='text-success'>Quartier(s) ajoutés avec succès</b>";
                session()->flash('message', $message);
            }

            return redirect()->back();
        }
        $zone = Zone::findOrFail($id);
        if ($zone->statut == 0) {
            $zone->statut = 1;
            $message = e($zone->libelle)." Activé(e) <b class='text-success'> Succès.</b>";
        } else {
            $zone->statut = 0;
            $message = e($zone->libelle)." Desactivé(e) <b class='text-success'> Succès.</b>";
        }
        $zone->save();
        session()->flash('message',$message);

        return redirect()->back();

    }
}
