<?php

namespace App\Services\Commandes;

use App\Models\Montant_livraison\Montant_livraison;
use App\Models\Quartier\Quartier;

/**
 * Frais de livraison entre deux quartiers.
 *
 * Même zone (ou zone inconnue) : tarif par défaut. Zones différentes : tarif
 * de la grille montant_livraison définie pour ce couple de zones, dans un
 * sens ou dans l'autre.
 */
class TarifLivraison
{
    public const TARIF_PAR_DEFAUT = 1000;

    /**
     * Montant à facturer, ou null si aucun tarif n'est défini entre ces zones.
     */
    public function montant($id_quartier_depart, $id_quartier_arrivee): int|float|string|null
    {
        if ($id_quartier_depart == null || $id_quartier_arrivee == null || $id_quartier_depart == $id_quartier_arrivee) {
            return self::TARIF_PAR_DEFAUT;
        }
        $zones = $this->zones($id_quartier_depart, $id_quartier_arrivee);
        if ($zones === null) {
            return self::TARIF_PAR_DEFAUT;
        }

        return $this->grilleEntre(...$zones)?->montant;
    }

    /**
     * Ligne de la grille tarifaire appliquée, ou null pour le tarif par défaut.
     */
    public function grille($id_quartier_depart, $id_quartier_arrivee): ?Montant_livraison
    {
        if ($id_quartier_depart == null || $id_quartier_arrivee == null) {
            return null;
        }
        $zones = $this->zones($id_quartier_depart, $id_quartier_arrivee);

        return $zones === null ? null : $this->grilleEntre(...$zones);
    }

    /**
     * Identifiants des zones des deux quartiers, ou null si elles sont
     * identiques ou inconnues (tarif par défaut).
     *
     * @return array{int, int}|null
     */
    private function zones($id_quartier_depart, $id_quartier_arrivee): ?array
    {
        $depart = Quartier::findOrFail($id_quartier_depart)->zone;
        $arrivee = Quartier::findOrFail($id_quartier_arrivee)->zone;
        if ($depart == null || $arrivee == null || $depart->id == $arrivee->id) {
            return null;
        }

        return [$depart->id, $arrivee->id];
    }

    private function grilleEntre(int $zone_a, int $zone_b): ?Montant_livraison
    {
        return Montant_livraison::whereIn('id_zone_colis', [$zone_a, $zone_b])
            ->whereIn('id_zone_livraison', [$zone_a, $zone_b])
            ->first();
    }
}
