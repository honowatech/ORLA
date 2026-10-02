<?php

namespace App\Services\Abonnement;

use App\Enums\EtatAbonnement;
use App\Models\Entreprise;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Règles d'accès liées à l'abonnement d'une entreprise.
 *
 * - suspendue : décision du Super Admin (entreprises.statut = 0) ;
 * - essai     : aucun paiement encore et essai_fin non atteinte ;
 * - active    : date_fin non atteinte ;
 * - grâce     : date_fin dépassée mais date_fin + période de grâce du forfait non atteinte ;
 * - expirée   : sinon.
 */
class AbonnementService
{
    public function etat(Entreprise $entreprise, ?CarbonInterface $maintenant = null): EtatAbonnement
    {
        $maintenant ??= CarbonImmutable::now();

        if ($entreprise->estSuspendue()) {
            return EtatAbonnement::Suspendue;
        }

        if ($entreprise->date_fin === null) {
            return $entreprise->essai_fin !== null && $entreprise->essai_fin->gte($maintenant)
                ? EtatAbonnement::Essai
                : EtatAbonnement::Expiree;
        }

        if ($entreprise->date_fin->gte($maintenant)) {
            return EtatAbonnement::Active;
        }

        $finEffective = $this->dateFinEffective($entreprise);

        return $finEffective !== null && $finEffective->gte($maintenant)
            ? EtatAbonnement::Grace
            : EtatAbonnement::Expiree;
    }

    /**
     * Fin d'accès réelle : date de fin d'abonnement augmentée de la période de grâce du forfait.
     */
    public function dateFinEffective(Entreprise $entreprise): ?CarbonImmutable
    {
        if ($entreprise->date_fin === null) {
            return null;
        }

        $grace = (int) ($entreprise->abonnement?->periode_grace ?? 0);

        return CarbonImmutable::instance($entreprise->date_fin)->addDays($grace);
    }

    /**
     * Jours restants avant la fin de l'essai, de l'abonnement ou de la grâce (null si expiré ou suspendu).
     */
    public function joursRestants(Entreprise $entreprise, ?CarbonInterface $maintenant = null): ?int
    {
        $maintenant ??= CarbonImmutable::now();

        $echeance = match ($this->etat($entreprise, $maintenant)) {
            EtatAbonnement::Essai => CarbonImmutable::instance($entreprise->essai_fin),
            EtatAbonnement::Active => CarbonImmutable::instance($entreprise->date_fin),
            EtatAbonnement::Grace => $this->dateFinEffective($entreprise),
            default => null,
        };

        return $echeance === null ? null : max(0, (int) $maintenant->diffInDays($echeance, false));
    }
}
