<?php

namespace App\Billing;

use App\Models\Entreprise;
use App\Tenancy\EntrepriseCourante;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Limites de l'offre souscrite (nombre de livreurs, véhicules, agents,
 * commandes par mois). Aucune limite pendant l'essai gratuit ; une limite
 * vide signifie « illimité ».
 */
class Limites
{
    /** table => [colonne de l'offre, libellé, compté sur le mois en cours ?] */
    public const RESSOURCES = [
        'coursiers' => ['max_coursiers', 'livreurs', false],
        'vehicule' => ['max_vehicules', 'véhicules', false],
        'agents' => ['max_agents', 'agents', false],
        'commandes' => ['max_commandes_mois', 'commandes par mois', true],
    ];

    public function __construct(protected EntrepriseCourante $courante)
    {
    }

    /**
     * Appelée à la création de toute donnée métier : refuse la création si
     * l'offre de l'entreprise est atteinte.
     */
    public function verifierCreation(Model $model): void
    {
        $table = $model->getTable();
        if (! isset(self::RESSOURCES[$table]) || $this->courante->filtreDesactive()) {
            return;
        }

        $entreprise = $this->courante->entreprise();
        $limite = $this->limite($entreprise, $table);
        if ($limite === null) {
            return;
        }

        if ($this->utilise($entreprise, $table) >= $limite) {
            [, $libelle] = self::RESSOURCES[$table];
            throw ValidationException::withMessages([
                'limite' => "Votre offre « {$entreprise->abonnement->titre} » est limitée à $limite $libelle. Passez à une offre supérieure depuis « Mon abonnement ».",
            ]);
        }
    }

    public function limite(?Entreprise $entreprise, string $table): ?int
    {
        if ($entreprise === null || $entreprise->enEssai() || $entreprise->abonnement === null) {
            return null;
        }

        $valeur = $entreprise->abonnement->{self::RESSOURCES[$table][0]};

        return $valeur === null ? null : (int) $valeur;
    }

    public function utilise(Entreprise $entreprise, string $table): int
    {
        $requete = DB::table($table)->where('entreprise_id', $entreprise->id);
        if (self::RESSOURCES[$table][2]) {
            $requete->where('created_at', '>=', now()->startOfMonth());
        }

        return $requete->count();
    }

    /**
     * Consommation de l'offre, pour la page « Mon abonnement ».
     *
     * @return array<int, array{libelle: string, utilise: int, limite: int|null}>
     */
    public function utilisation(Entreprise $entreprise): array
    {
        $lignes = [];
        foreach (self::RESSOURCES as $table => [, $libelle]) {
            $lignes[] = ['libelle' => ucfirst($libelle), 'utilise' => $this->utilise($entreprise, $table), 'limite' => $this->limite($entreprise, $table)];
        }

        return $lignes;
    }
}
