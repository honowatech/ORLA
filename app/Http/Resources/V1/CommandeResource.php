<?php

namespace App\Http\Resources\V1;

use App\Services\CommandeService;
use App\Services\CommandeStatutService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeResource extends JsonResource
{
    /** Transitions de statut proposées à l'utilisateur courant (null = non communiqué). */
    private ?array $transitions = null;

    /** @param  list<string>  $transitions */
    public function avecTransitions(array $transitions): static
    {
        $this->transitions = $transitions;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $collecte = CommandeService::decoderAdresse($this->adresse_colis);
        $livraison = CommandeService::decoderAdresse($this->adresse_livraison);

        return [
            'id' => $this->id,
            'statut' => $this->statut,
            'statut_libelle' => CommandeStatutService::LIBELLES[$this->statut] ?? $this->statut,
            'type_commande' => $this->type_commande,
            'description' => $this->description,
            'date_commande' => $this->date($this->date_commande),
            'date_livraison' => $this->date($this->date_livraison),
            'date_mise_encours' => $this->date($this->date_mise_encours),
            'date_livre' => $this->date($this->date_livre),
            'montant_livraison' => $this->montant_livraison !== null ? (float) $this->montant_livraison : null,
            'montant_a_recuperer' => $this->montant_recuperer !== null ? (float) $this->montant_recuperer : null,
            'mode_de_paiement' => $this->mode_de_paiement,
            'client' => [
                'id' => $this->id_client,
                'nom' => $this->nom_client,
                'telephone' => $this->telephone,
            ],
            'collecte' => [
                'contact' => $collecte['contact'],
                'lieu' => $collecte['lieu'],
                'description' => $collecte['description'],
                'quartier' => $this->whenLoaded('quartier_colis', fn () => $this->quartier_colis
                    ? new QuartierResource($this->quartier_colis) : null),
            ],
            'livraison' => [
                'nom' => $livraison['nom'],
                'contact' => $livraison['contact'],
                'lieu' => $livraison['lieu'],
                'description' => $livraison['description'],
                'quartier' => $this->whenLoaded('quartier_livraison', fn () => $this->quartier_livraison
                    ? new QuartierResource($this->quartier_livraison) : null),
            ],
            'coursier' => $this->whenLoaded('coursier', fn () => $this->coursier ? [
                'id' => $this->coursier->id,
                'noms' => $this->coursier->noms,
                'prenoms' => $this->coursier->prenoms,
                'telephone' => $this->coursier->telephone,
            ] : null),
            'boutique' => $this->whenLoaded('boutique', fn () => $this->boutique ? [
                'id' => $this->boutique->id,
                'libelle' => $this->boutique->libelle,
            ] : null),
            'produits' => $this->whenLoaded('details_commande', fn () => $this->details_commande->map(fn ($d) => [
                'id_produit' => $d->id_produit,
                'nom' => $d->nom_produit,
                'quantite' => $d->quantite !== null ? (float) $d->quantite : null,
            ])->values()),
            'transitions_possibles' => $this->when($this->transitions !== null, fn () => $this->transitions),
            'cree_le' => $this->date($this->created_at),
            'modifie_le' => $this->date($this->updated_at),
        ];
    }

    private function date(mixed $valeur): ?string
    {
        if ($valeur === null || $valeur === '') {
            return null;
        }

        return \Illuminate\Support\Carbon::parse($valeur)->toIso8601String();
    }
}
