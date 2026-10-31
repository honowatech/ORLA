<?php

namespace App\Services\Routage;

/**
 * Graphe routier d'une ville : les nœuds sont les quartiers, les arcs les tronçons.
 * Indépendant d'Eloquent pour rester testable unitairement.
 */
class ReseauRoutier
{
    /** @var array<int, array{id:int, libelle:string, id_zone:?int, lat:?float, lng:?float}> */
    private array $noeuds = [];

    /** @var array<int, array<int, array>> adjacence : depart => [arrivee => tronçon] */
    private array $arcs = [];

    public function ajouterQuartier(int $id, string $libelle, ?int $idZone = null, ?float $lat = null, ?float $lng = null): void
    {
        $this->noeuds[$id] = ['id' => $id, 'libelle' => $libelle, 'id_zone' => $idZone, 'lat' => $lat, 'lng' => $lng];
    }

    /**
     * @param array{0:string,1:string}|null $fermeture plage de fermeture ["HH:MM","HH:MM"]
     * @param int[] $typesInterdits identifiants de types de véhicule interdits
     */
    public function ajouterTroncon(
        int $depart,
        int $arrivee,
        float $distanceKm,
        ?float $dureeMinutes = null,
        string $etat = 'bon',
        bool $bidirectionnel = true,
        ?array $fermeture = null,
        array $typesInterdits = [],
    ): void {
        $troncon = [
            'distance' => $distanceKm,
            'duree' => $dureeMinutes,
            'etat' => $etat,
            'fermeture' => $fermeture,
            'interdits' => array_map('intval', $typesInterdits),
        ];
        $this->arcs[$depart][$arrivee] = $troncon;
        if ($bidirectionnel) {
            $this->arcs[$arrivee][$depart] = $troncon;
        }
    }

    public function quartier(int $id): ?array
    {
        return $this->noeuds[$id] ?? null;
    }

    public function libelle(int $id): string
    {
        return $this->noeuds[$id]['libelle'] ?? ('Quartier #'.$id);
    }

    public function aQuartier(int $id): bool
    {
        return isset($this->noeuds[$id]);
    }

    /** @return array<int, array> arcs sortants d'un quartier */
    public function voisins(int $id): array
    {
        return $this->arcs[$id] ?? [];
    }

    public function nombreTroncons(): int
    {
        return array_sum(array_map('count', $this->arcs));
    }

    /** Distance à vol d'oiseau (km), ou null si un des quartiers n'a pas de coordonnées. */
    public function distanceHaversine(int $a, int $b): ?float
    {
        $qa = $this->noeuds[$a] ?? null;
        $qb = $this->noeuds[$b] ?? null;
        if (!$qa || !$qb || $qa['lat'] === null || $qa['lng'] === null || $qb['lat'] === null || $qb['lng'] === null) {
            return null;
        }
        $r = 6371.0;
        $dLat = deg2rad($qb['lat'] - $qa['lat']);
        $dLng = deg2rad($qb['lng'] - $qa['lng']);
        $h = sin($dLat / 2) ** 2 + cos(deg2rad($qa['lat'])) * cos(deg2rad($qb['lat'])) * sin($dLng / 2) ** 2;
        return 2 * $r * asin(min(1.0, sqrt($h)));
    }

    public function memeZone(int $a, int $b): bool
    {
        $za = $this->noeuds[$a]['id_zone'] ?? null;
        $zb = $this->noeuds[$b]['id_zone'] ?? null;
        return $za !== null && $za === $zb;
    }
}
