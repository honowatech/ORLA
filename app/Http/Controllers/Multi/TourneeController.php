<?php

namespace App\Http\Controllers\Multi;

use App\Models\commandes\Commandes;
use App\Models\coursiers\Coursiers;
use App\Models\quartier\Quartier;
use App\Models\tournee\Tournee;
use App\Models\Vehicule;
use App\Models\ville\Ville;
use App\Services\Routage\TourneeService;
use Illuminate\Http\Request;

/**
 * Planification de tournées optimisées pour les responsables logistiques.
 */
class TourneeController extends Controller
{
    private const PROFILS = ['admin', 'routeur', 'superviseur_ville'];

    public function __construct(private TourneeService $service)
    {
    }

    public function index(Request $request)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $recherche = trim((string) $request->input('recherche'));
        $coursiers = Coursiers::where('noms', 'like', '%'.$recherche.'%')->orWhere('prenoms', 'like', '%'.$recherche.'%')->pluck('id');
        $statuts = array_keys(array_filter(Tournee::STATUTS, fn ($lib) => $recherche !== '' && str_contains(strtolower($lib), strtolower($recherche))));

        $tournees = Tournee::with(['coursier', 'vehicule', 'quartier_depart', 'ville'])
            ->withCount('etapes')
            ->where(function ($q) use ($coursiers, $statuts, $recherche) {
                $q->whereIn('id_coursier', $coursiers)->orWhereIn('statut', $statuts);
                if ($recherche !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $recherche)) {
                    $q->orWhereDate('date_depart', $recherche);
                }
            });
        if (filter(['routeur', 'superviseur_ville'], $user) == 'true') {
            $tournees->where('id_ville', $user->id_ville);
        }
        $tournees = $tournees->orderBy('date_depart', 'desc')->paginate(20);
        session()->flash('search', $recherche);
        return view('multi.pages.tournee.index', compact('tournees'));
    }

    public function create(Request $request)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $restreint = filter(['routeur', 'superviseur_ville'], $user) == 'true';
        $villes = $restreint ? Ville::where('id', $user->id_ville)->get() : Ville::get();
        $id_ville = (int) ($restreint ? $user->id_ville : ($request->input('id_ville') ?: ($villes->first()->id ?? 0)));

        $donnees = $this->donneesVille($id_ville);
        return view('multi.pages.tournee.create', array_merge($donnees, compact('villes', 'id_ville')));
    }

    /** Simulation AJAX : renvoie le comparatif optimisé / référence sans rien enregistrer. */
    public function simuler(Request $request)
    {
        $user = Auth()->user();
        if (filter(self::PROFILS, $user) != 'true') {
            abort(403);
        }
        $entree = $this->validerPlanification($request);
        $commandes = $this->commandesPlanifiables($entree['id_ville'])->whereIn('id', $entree['commandes'])->get();
        if ($commandes->isEmpty()) {
            return response("<div class='alert alert-warning p-1'>Aucune commande planifiable sélectionnée.</div>");
        }
        $vehicule = $entree['id_vehicule'] ? Vehicule::with('type')->find($entree['id_vehicule']) : null;
        $simulation = $this->service->simuler($entree['id_ville'], $commandes, $entree['id_quartier_depart'], $entree['heure_depart'], $vehicule, $entree['avec_enlevement']);
        $quartiers = Quartier::where('id_ville', $entree['id_ville'])->get()->keyBy('id');
        return view('multi.pages.tournee.result', [
            'optimise' => $simulation['optimise'],
            'reference' => $simulation['reference'],
            'contraintes' => $simulation['vehicule'],
            'quartiers' => $quartiers,
            'commandes' => $commandes->keyBy('id'),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $entree = $this->validerPlanification($request, true);
        $commandes = $this->commandesPlanifiables($entree['id_ville'])->whereIn('id', $entree['commandes'])->get();
        if ($commandes->isEmpty()) {
            return back()->withInput()->withErrors(['commandes' => 'Aucune commande planifiable sélectionnée.']);
        }
        $vehicule = $entree['id_vehicule'] ? Vehicule::with('type')->find($entree['id_vehicule']) : null;
        $simulation = $this->service->simuler($entree['id_ville'], $commandes, $entree['id_quartier_depart'], $entree['heure_depart'], $vehicule, $entree['avec_enlevement']);
        $tournee = $this->service->enregistrer($simulation, $entree['id_ville'], $entree['id_coursier'], $entree['id_vehicule'], $entree['id_quartier_depart'], $user->id);

        session()->flash('message', 'Tournée n°'.$tournee->id." planifiée avec <b class='text-success'> Succès.</b> Les ".$commandes->count().' commandes ont été attribuées au livreur.');
        return redirect()->route('tournee.show', $tournee->id);
    }

    public function show($id)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $tournee = Tournee::with(['etapes.commande', 'etapes.quartier', 'coursier', 'vehicule.type', 'quartier_depart', 'ville', 'enregistreur'])->findOrFail($id);
        if (filter(['routeur', 'superviseur_ville'], $user) == 'true' && $tournee->id_ville != $user->id_ville) {
            return redirect()->route('tournee.index');
        }
        $quartiers = Quartier::where('id_ville', $tournee->id_ville)->get()->keyBy('id');
        return view('multi.pages.tournee.show', compact('tournee', 'quartiers'));
    }

    public function edit($id)
    {
        return redirect()->route('tournee.show', $id);
    }

    /** Changement de statut (encours / terminee). */
    public function update(Request $request, $id)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $request->validate(['statut' => 'required|in:planifiee,encours,terminee']);
        $tournee = Tournee::findOrFail($id);
        $tournee->statut = $request->input('statut');
        $tournee->save();
        if ($tournee->statut === 'encours') {
            Commandes::whereIn('id', $tournee->etapes()->pluck('id_commande'))->where('statut', 'attribue')
                ->update(['statut' => 'encours', 'date_mise_encours' => now()]);
        }
        session()->flash('message', 'Tournée n°'.$tournee->id.' passée au statut <b>'.Tournee::STATUTS[$tournee->statut].'</b>.');
        return redirect()->back();
    }

    /** Annulation : libère les commandes (retour en attente) ; suppression si demandé. */
    public function destroy(Request $request, $id)
    {
        $user = Auth()->user();
        $filter = filter(self::PROFILS, $user);
        if ($filter != 'true') {
            return redirect()->route($filter);
        }
        $tournee = Tournee::findOrFail($id);
        $idsCommandes = $tournee->etapes()->pluck('id_commande');
        if ($tournee->statut !== 'terminee') {
            Commandes::whereIn('id', $idsCommandes)->whereIn('statut', ['attribue', 'encours'])
                ->update(['id_coursier' => null, 'statut' => 'attente']);
        }
        if ($request->input('type') == 'delete') {
            $tournee->etapes()->delete();
            $tournee->delete();
            session()->flash('message', 'Tournée n°'.$id." supprimée avec <b class='text-success'> Succès.</b>");
            return redirect()->route('tournee.index');
        }
        $tournee->statut = 'annulee';
        $tournee->save();
        session()->flash('message', 'Tournée n°'.$id." annulée : les commandes sont de nouveau en attente d'attribution.");
        return redirect()->back();
    }

    // ------------------------------------------------------------------

    private function validerPlanification(Request $request, bool $pourEnregistrement = false): array
    {
        $regles = [
            'id_ville' => 'bail|required|numeric|min:1',
            'id_quartier_depart' => 'bail|required|numeric|min:1',
            'date_depart' => 'bail|required|date',
            'id_vehicule' => 'nullable|numeric',
            'commandes' => 'required|array|min:1',
            'commandes.*' => 'numeric',
        ];
        if ($pourEnregistrement) {
            $regles['id_coursier'] = 'bail|required|numeric|min:1';
        }
        $request->validate($regles);

        $user = Auth()->user();
        $id_ville = (int) $request->input('id_ville');
        if (filter(['routeur', 'superviseur_ville'], $user) == 'true') {
            $id_ville = (int) $user->id_ville;
        }
        return [
            'id_ville' => $id_ville,
            'id_quartier_depart' => (int) $request->input('id_quartier_depart'),
            'heure_depart' => strtotime($request->input('date_depart')),
            'id_vehicule' => $request->filled('id_vehicule') ? (int) $request->input('id_vehicule') : null,
            'id_coursier' => $request->filled('id_coursier') ? (int) $request->input('id_coursier') : null,
            'commandes' => array_map('intval', $request->input('commandes', [])),
            'avec_enlevement' => $request->boolean('avec_enlevement', true),
        ];
    }

    /** Commandes d'une ville encore planifiables (en attente ou attribuées sans tournée active). */
    private function commandesPlanifiables(int $idVille)
    {
        $quartiers = Quartier::where('id_ville', $idVille)->pluck('id');
        $dejaPlanifiees = Tournee::whereIn('statut', ['planifiee', 'encours'])
            ->join('tournee_etape', 'tournee_etape.id_tournee', '=', 'tournee.id')
            ->pluck('tournee_etape.id_commande');
        return Commandes::with(['client', 'quartier_colis', 'quartier_livraison'])
            ->whereIn('statut', ['attente', 'attribue'])
            ->where('disponibility', 1)
            ->whereIn('id_quartier_livraison', $quartiers)
            ->whereNotIn('id', $dejaPlanifiees)
            ->orderBy('date_livraison');
    }

    private function donneesVille(int $idVille): array
    {
        $quartiers = Quartier::where('id_ville', $idVille)->orderBy('libelle')->get();
        $coursiers = Coursiers::where('statut', 1)
            ->whereHas('user', fn ($q) => $q->where('id_ville', $idVille))
            ->orderBy('noms')->get();
        if ($coursiers->isEmpty()) {
            $coursiers = Coursiers::where('statut', 1)->orderBy('noms')->get();
        }
        $vehicules = Vehicule::with(['type', 'coursier'])->where('id_ville', $idVille)->where('statut', 1)->get();
        $commandes = $this->commandesPlanifiables($idVille)->get();
        return compact('quartiers', 'coursiers', 'vehicules', 'commandes');
    }
}
