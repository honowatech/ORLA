<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\V1\StoreCommandeRequest;
use App\Http\Resources\V1\CommandeResource;
use App\Models\commandes\Commandes;
use App\Services\CommandeService;
use App\Services\CommandeStatutService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Commandes du client connecté.
 */
class CommandeController extends Controller
{
    private const RELATIONS = ['quartier_colis.ville', 'quartier_livraison.ville', 'coursier', 'boutique', 'details_commande'];

    public function __construct(
        private readonly CommandeService $commandes,
        private readonly CommandeStatutService $statuts,
    ) {
    }

    /** Filtres : ?statut=attente,encours  ?par_page=20 */
    public function index(Request $request): AnonymousResourceCollection
    {
        $statuts = $request->filled('statut')
            ? array_values(array_intersect(explode(',', (string) $request->query('statut')), CommandeStatutService::STATUTS))
            : null;

        $commandes = $this->requete($request)
            ->with(self::RELATIONS)
            ->when($statuts !== null, fn (Builder $q) => $q->whereIn('statut', $statuts))
            ->orderByDesc('date_commande')
            ->orderByDesc('id')
            ->paginate($this->parPage($request))
            ->withQueryString();

        $commandes->getCollection()->transform(
            fn (Commandes $c) => (new CommandeResource($c))->avecTransitions($this->statuts->transitionsPourClient($c))
        );

        return CommandeResource::collection($commandes);
    }

    public function store(StoreCommandeRequest $request): JsonResponse
    {
        $commande = $this->commandes->creerPourClient(
            $request->user(),
            $request->user()->client_utilisateur,
            $request->validated(),
        );

        $commande->load(self::RELATIONS);

        return (new CommandeResource($commande))
            ->avecTransitions($this->statuts->transitionsPourClient($commande))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, int $commande): CommandeResource
    {
        $commande = $this->requete($request)->with(self::RELATIONS)->findOrFail($commande);

        return (new CommandeResource($commande))->avecTransitions($this->statuts->transitionsPourClient($commande));
    }

    /** Annulation d'une commande encore en attente. */
    public function annuler(Request $request, int $commande): CommandeResource
    {
        $commande = $this->requete($request)->findOrFail($commande);

        $this->statuts->changer(
            $commande,
            'annulee',
            $this->statuts->transitionsPourClient($commande),
            $request->user(),
            route('Clientcommandes.show', $commande->id),
        );

        $commande->load(self::RELATIONS);

        return (new CommandeResource($commande))->avecTransitions($this->statuts->transitionsPourClient($commande));
    }

    private function requete(Request $request): Builder
    {
        return Commandes::query()
            ->where('id_client', $request->user()->client_utilisateur->id)
            ->where('statut', '!=', 'supprime');
    }
}
