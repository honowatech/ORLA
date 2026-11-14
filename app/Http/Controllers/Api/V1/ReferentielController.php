<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\EstimationTarifRequest;
use App\Http\Resources\V1\QuartierResource;
use App\Http\Resources\V1\VilleResource;
use App\Models\quartier\Quartier;
use App\Models\ville\Ville;
use App\Services\TarifService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Données de référence utilisées par les formulaires de l'application.
 */
class ReferentielController extends Controller
{
    public function villes(): AnonymousResourceCollection
    {
        return VilleResource::collection(Ville::query()->orderBy('libelle')->get());
    }

    /** Quartiers d'une ville, avec recherche optionnelle : ?q=akwa */
    public function quartiers(Request $request, int $ville): AnonymousResourceCollection
    {
        Ville::findOrFail($ville);

        $quartiers = Quartier::query()
            ->where('id_ville', $ville)
            ->when($request->filled('q'), fn ($q) => $q->where('libelle', 'like', '%'.$request->query('q').'%'))
            ->orderBy('libelle')
            ->get();

        return QuartierResource::collection($quartiers);
    }

    public function estimerTarif(EstimationTarifRequest $request, TarifService $tarifs): JsonResponse
    {
        $estimation = $tarifs->estimer(
            Quartier::find($request->integer('id_quartier_collecte')),
            Quartier::find($request->integer('id_quartier_livraison')),
        );

        return response()->json(['data' => [
            'montant' => $estimation['montant'],
            'devise' => 'XAF',
            'tarif_par_defaut' => $estimation['tarif_par_defaut'],
        ]]);
    }
}
