<?php

namespace App\Http\Controllers\Api\V1\Coursier;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\V1\ChangerStatutRequest;
use App\Http\Resources\V1\CommandeResource;
use App\Models\commandes\Commandes;
use App\Services\CommandeStatutService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

/**
 * Livraisons attribuées au coursier connecté.
 */
class CommandeController extends Controller
{
    private const RELATIONS = ['quartier_colis.ville', 'quartier_livraison.ville', 'boutique', 'details_commande'];

    public function __construct(private readonly CommandeStatutService $statuts)
    {
    }

    /**
     * Filtres : ?statut=attribue,encours  ?date=2026-11-14  ?q=texte  ?par_page=20
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'statut' => ['nullable', 'string'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $statuts = $request->filled('statut')
            ? array_values(array_intersect(explode(',', $request->query('statut')), CommandeStatutService::STATUTS))
            : null;

        $commandes = $this->requete($request)
            ->with(self::RELATIONS)
            ->when($statuts !== null, fn (Builder $q) => $q->whereIn('statut', $statuts))
            ->when($request->filled('date'), fn (Builder $q) => $q->whereDate('date_livraison', $request->query('date')))
            ->when($request->filled('q'), function (Builder $q) use ($request) {
                $terme = '%'.$request->query('q').'%';
                $q->where(fn (Builder $w) => $w
                    ->where('nom_client', 'like', $terme)
                    ->orWhere('telephone', 'like', $terme)
                    ->orWhere('adresse_colis', 'like', $terme)
                    ->orWhere('adresse_livraison', 'like', $terme)
                    ->orWhere('description', 'like', $terme));
            })
            ->orderByDesc('date_livraison')
            ->orderByDesc('id')
            ->paginate($this->parPage($request))
            ->withQueryString();

        $commandes->getCollection()->transform(
            fn (Commandes $c) => (new CommandeResource($c))->avecTransitions($this->statuts->transitionsPourCoursier($c))
        );

        return CommandeResource::collection($commandes);
    }

    /** Compteurs par statut, pour le tableau de bord du coursier. */
    public function resume(Request $request): JsonResponse
    {
        $parStatut = $this->requete($request)
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $aujourdhui = $this->requete($request)->whereDate('date_livraison', today());

        return response()->json(['data' => [
            'par_statut' => collect(CommandeStatutService::STATUTS)
                ->mapWithKeys(fn ($s) => [$s => (int) ($parStatut[$s] ?? 0)]),
            'aujourdhui' => [
                'total' => (clone $aujourdhui)->count(),
                'a_livrer' => (clone $aujourdhui)->whereIn('statut', ['attribue', 'encours'])->count(),
                'livrees' => (clone $aujourdhui)->where('statut', 'livre')->count(),
            ],
        ]]);
    }

    public function show(Request $request, int $commande): CommandeResource
    {
        $commande = $this->requete($request)->with(self::RELATIONS)->findOrFail($commande);

        return (new CommandeResource($commande))->avecTransitions($this->statuts->transitionsPourCoursier($commande));
    }

    public function changerStatut(ChangerStatutRequest $request, int $commande): CommandeResource
    {
        $commande = $this->requete($request)->findOrFail($commande);

        $this->statuts->changer(
            $commande,
            $request->string('statut'),
            $this->statuts->transitionsPourCoursier($commande),
            $request->user(),
            route('Coursiercommandes.show', $commande->id),
        );

        $commande->load(self::RELATIONS);

        return (new CommandeResource($commande))->avecTransitions($this->statuts->transitionsPourCoursier($commande));
    }

    /** Commandes visibles par le coursier connecté. */
    private function requete(Request $request): Builder
    {
        return Commandes::query()
            ->where('id_coursier', $request->user()->coursier_utilisateur->id)
            ->where('statut', '!=', 'supprime');
    }
}
