<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\commandes\Commandes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Cycle de vie d'une commande :
 *   attente → attribue → encours → livre / annulee / echoue
 *
 * Les transitions reprennent celles du back-office web.
 */
class CommandeStatutService
{
    public const STATUTS = ['attente', 'attribue', 'encours', 'livre', 'annulee', 'echoue'];

    public const STATUTS_FINAUX = ['livre', 'annulee', 'echoue'];

    public const LIBELLES = [
        'attente' => 'En attente',
        'attribue' => 'Attribuée',
        'encours' => 'En cours',
        'livre' => 'Livrée',
        'annulee' => 'Annulée',
        'echoue' => 'Échouée',
        'supprime' => 'Supprimée',
    ];

    private const TRANSITIONS = [
        'attente' => ['attribue', 'annulee', 'echoue'],
        'attribue' => ['encours', 'livre', 'annulee', 'echoue'],
        'encours' => ['livre', 'annulee', 'echoue'],
    ];

    /** Statuts qu'un coursier peut appliquer lui-même. */
    private const STATUTS_COURSIER = ['encours', 'livre', 'annulee', 'echoue'];

    /** Statuts qu'un client peut appliquer lui-même. */
    private const STATUTS_CLIENT = ['annulee'];

    /** @return list<string> */
    public function transitionsPossibles(Commandes $commande): array
    {
        return self::TRANSITIONS[$commande->statut] ?? [];
    }

    /** @return list<string> */
    public function transitionsPourCoursier(Commandes $commande): array
    {
        return array_values(array_intersect($this->transitionsPossibles($commande), self::STATUTS_COURSIER));
    }

    /** @return list<string> */
    public function transitionsPourClient(Commandes $commande): array
    {
        // Un client ne peut annuler que tant que la commande n'est pas prise en charge.
        if ($commande->statut !== 'attente') {
            return [];
        }

        return array_values(array_intersect($this->transitionsPossibles($commande), self::STATUTS_CLIENT));
    }

    /**
     * Applique un nouveau statut si la transition est autorisée.
     *
     * @param  list<string>  $autorises  transitions autorisées pour l'acteur
     *
     * @throws ValidationException
     */
    public function changer(Commandes $commande, string $statut, array $autorises, User $auteur, ?string $lien = null): Commandes
    {
        if (! in_array($statut, $autorises, true)) {
            $this->journaliser($auteur, "Echec de l'action", 'Le changement de statut a échoué', 'danger', $lien);

            throw ValidationException::withMessages([
                'statut' => [sprintf(
                    'Impossible de passer la commande de « %s » à « %s ».',
                    self::LIBELLES[$commande->statut] ?? $commande->statut,
                    self::LIBELLES[$statut] ?? $statut,
                )],
            ]);
        }

        return DB::transaction(function () use ($commande, $statut, $auteur, $lien) {
            $commande->statut = $statut;

            if ($statut === 'encours') {
                $commande->date_mise_encours = now();
            }

            if (in_array($statut, self::STATUTS_FINAUX, true)) {
                if ($commande->date_mise_encours === null) {
                    $commande->date_mise_encours = now();
                }
                $commande->date_livre = now();
            }

            $commande->save();

            [$titre, $couleur] = match ($statut) {
                'encours' => ['Livraison En cours', 'dark'],
                'livre' => ['Livraison terminée', 'success'],
                'annulee' => ['Livraison annulée', 'danger'],
                'echoue' => ['Livraison échouée', 'danger'],
                default => ['Statut modifié', 'info'],
            };

            $this->journaliser($auteur, $titre, $titre.' avec <b class="text-success">succès</b>', $couleur, $lien);

            return $commande;
        });
    }

    private function journaliser(User $auteur, string $titre, string $message, string $couleur, ?string $lien): void
    {
        $activity = new Activity;
        $activity->title = $titre;
        $activity->message = $message;
        $activity->color = $couleur;
        $activity->lien = $lien;
        $activity->texte_lien = $lien ? 'Voir la commande' : null;
        $activity->jour = now()->toDateString();
        $activity->heure = now()->toTimeString();
        $activity->id_user = $auteur->id;
        $activity->save();
    }
}
