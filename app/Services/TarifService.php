<?php

namespace App\Services;

use App\Models\montant_livraison\Montant_livraison;
use App\Models\quartier\Quartier;

/**
 * Calcul du montant de livraison entre deux quartiers.
 *
 * Reprend la règle du back-office : le tarif est défini par couple de zones
 * (zone de collecte / zone de livraison). Si les deux quartiers sont dans la
 * même zone, si l'un d'eux n'a pas de zone, ou si aucun tarif n'est
 * configuré, le tarif par défaut s'applique.
 */
class TarifService
{
    public const TARIF_PAR_DEFAUT = 1000;

    /**
     * @return array{montant: float, id_montant_livraison: int|null, tarif_par_defaut: bool}
     */
    public function estimer(?Quartier $depart, ?Quartier $arrivee): array
    {
        $parDefaut = [
            'montant' => (float) self::TARIF_PAR_DEFAUT,
            'id_montant_livraison' => null,
            'tarif_par_defaut' => true,
        ];

        if ($depart === null || $arrivee === null || $depart->id === $arrivee->id) {
            return $parDefaut;
        }

        $zoneDepart = $depart->id_zone;
        $zoneArrivee = $arrivee->id_zone;

        if ($zoneDepart === null || $zoneArrivee === null || $zoneDepart == $zoneArrivee) {
            return $parDefaut;
        }

        $tarif = Montant_livraison::query()
            ->whereIn('id_zone_colis', [$zoneDepart, $zoneArrivee])
            ->whereIn('id_zone_livraison', [$zoneDepart, $zoneArrivee])
            ->first();

        if ($tarif === null) {
            return $parDefaut;
        }

        return [
            'montant' => (float) $tarif->montant,
            'id_montant_livraison' => $tarif->id,
            'tarif_par_defaut' => false,
        ];
    }
}
