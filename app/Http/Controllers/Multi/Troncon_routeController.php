<?php

namespace App\Http\Controllers\Multi;

use App\Models\quartier\Quartier;
use App\Models\Troncon_route;
use App\Models\Type_vehicule;
use App\Models\ville\Ville;
use Illuminate\Http\Request;

/**
 * Gestion des tronçons de route (distance, état, restrictions) utilisés par
 * l'optimiseur de tournées.
 */
class Troncon_routeController extends Controller
{
    private const PROFILS = ['admin', 'routeur', 'superviseur_ville'];

    public function index(Request $request)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $recherche = trim((string) $request->input('recherche'));
        $quartiers = Quartier::where('libelle', 'like', '%'.$recherche.'%')->pluck('id');
        $etats = array_keys(array_filter(Troncon_route::ETATS, fn ($lib, $cle) => $recherche !== '' && (str_contains(strtolower($lib), strtolower($recherche)) || str_contains($cle, strtolower($recherche))), ARRAY_FILTER_USE_BOTH));

        $troncons = Troncon_route::with(['quartier_depart', 'quartier_arrivee', 'ville'])
            ->where(function ($q) use ($quartiers, $etats, $recherche) {
                $q->whereIn('id_quartier_depart', $quartiers)
                    ->orWhereIn('id_quartier_arrivee', $quartiers)
                    ->orWhereIn('etat_route', $etats)
                    ->orWhere('description', 'like', '%'.$recherche.'%');
            });
        if (filter(['routeur', 'superviseur_ville'], $user) == 'true') {
            $troncons->where('id_ville', $user->id_ville);
        }
        $troncons = $troncons->orderBy('updated_at', 'desc')->paginate(20);
        session()->flash('search', $recherche);
        return view('multi.pages.troncon.index', compact('troncons'));
    }

    public function create()
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        [$villes, $quartiers] = $this->listesFormulaire($user);
        $types_vehicule = Type_vehicule::where('statut', 1)->get();
        $etats = Troncon_route::ETATS;
        return view('multi.pages.troncon.create', compact('villes', 'quartiers', 'types_vehicule', 'etats'));
    }

    public function store(Request $request)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $data = $this->valider($request);
        if (Troncon_route::where('id_quartier_depart', $data['id_quartier_depart'])->where('id_quartier_arrivee', $data['id_quartier_arrivee'])->exists()) {
            return back()->withInput()->withErrors(['id_quartier_arrivee' => 'Un tronçon existe déjà entre ces deux quartiers.']);
        }
        Troncon_route::create($data);
        session()->flash('message', "Tronçon enregistré avec <b class='text-success'> Succès.</b>");
        return redirect()->route('troncon.index');
    }

    public function show($id)
    {
        return redirect()->route('troncon.edit', $id);
    }

    public function edit($id)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $troncon = Troncon_route::findOrFail($id);
        [$villes, $quartiers] = $this->listesFormulaire($user);
        $types_vehicule = Type_vehicule::where('statut', 1)->get();
        $etats = Troncon_route::ETATS;
        return view('multi.pages.troncon.edit', compact('troncon', 'villes', 'quartiers', 'types_vehicule', 'etats'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $troncon = Troncon_route::findOrFail($id);
        if (filter(['routeur', 'superviseur_ville'], $user) == 'true' && $troncon->id_ville != $user->id_ville) {
            session()->flash('message', "<b class='text-danger'> Erreur.</b> <br> Ce tronçon n'appartient pas à votre ville");
            return redirect()->route('troncon.index');
        }
        $data = $this->valider($request);
        $troncon->fill($data)->save();
        session()->flash('message', "Tronçon mis à jour avec <b class='text-success'> Succès.</b>");
        return redirect()->route('troncon.index');
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $troncon = Troncon_route::findOrFail($id);
        $nom = $troncon->quartier_depart->libelle.' → '.$troncon->quartier_arrivee->libelle;
        if ($request->input('type') == 'delete') {
            $troncon->delete();
            session()->flash('message', 'Tronçon '.$nom." supprimé avec <b class='text-success'> Succès.</b>");
        } else {
            $troncon->statut = !$troncon->statut;
            $troncon->save();
            session()->flash('message', 'Tronçon '.$nom.' '.($troncon->statut ? 'activé' : 'désactivé')." avec <b class='text-success'> Succès.</b>");
        }
        return redirect()->back();
    }

    private function valider(Request $request): array
    {
        $request->validate([
            'id_ville' => 'bail|required|numeric|min:1',
            'id_quartier_depart' => 'bail|required|numeric|min:1|different:id_quartier_arrivee',
            'id_quartier_arrivee' => 'bail|required|numeric|min:1',
            'distance_km' => 'bail|required|numeric|min:0.01',
            'duree_minutes' => 'nullable|numeric|min:0',
            'etat_route' => 'bail|required|in:'.implode(',', array_keys(Troncon_route::ETATS)),
            'heure_debut_fermeture' => 'nullable|date_format:H:i|required_with:heure_fin_fermeture',
            'heure_fin_fermeture' => 'nullable|date_format:H:i|required_with:heure_debut_fermeture',
            'types_vehicule_interdits' => 'nullable|array',
            'types_vehicule_interdits.*' => 'numeric',
            'description' => 'nullable|string',
        ]);
        return [
            'id_ville' => $request->input('id_ville'),
            'id_quartier_depart' => $request->input('id_quartier_depart'),
            'id_quartier_arrivee' => $request->input('id_quartier_arrivee'),
            'distance_km' => $request->input('distance_km'),
            'duree_minutes' => $request->filled('duree_minutes') ? $request->input('duree_minutes') : null,
            'etat_route' => $request->input('etat_route'),
            'bidirectionnel' => $request->boolean('bidirectionnel', true),
            'heure_debut_fermeture' => $request->input('heure_debut_fermeture') ?: null,
            'heure_fin_fermeture' => $request->input('heure_fin_fermeture') ?: null,
            'types_vehicule_interdits' => array_values(array_map('intval', $request->input('types_vehicule_interdits', []))),
            'description' => $request->input('description'),
        ];
    }

    private function listesFormulaire($user): array
    {
        if (filter(['routeur', 'superviseur_ville'], $user) == 'true') {
            $villes = Ville::where('id', $user->id_ville)->get();
            $quartiers = Quartier::where('id_ville', $user->id_ville)->orderBy('libelle')->get();
        } else {
            $villes = Ville::get();
            $quartiers = Quartier::orderBy('libelle')->get();
        }
        return [$villes, $quartiers];
    }
}
