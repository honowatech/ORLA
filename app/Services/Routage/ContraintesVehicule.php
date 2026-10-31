<?php

namespace App\Services\Routage;

/**
 * Contraintes d'exploitation d'un véhicule (ou type de véhicule) pour une tournée.
 */
class ContraintesVehicule
{
    public function __construct(
        public ?int $idType = null,
        public float $vitesseKmh = 30,
        public float $coutKm = 0,
        public float $coutHeure = 0,
        /** @var array{0:string,1:string}|null plage ["HH:MM","HH:MM"] pendant laquelle le véhicule peut circuler */
        public ?array $plageHoraire = null,
        public ?int $capaciteColis = null,
    ) {
        if ($this->vitesseKmh <= 0) {
            $this->vitesseKmh = 30;
        }
    }

    /** Le véhicule peut-il circuler à cet instant (timestamp) ? */
    public function peutCirculer(int $timestamp): bool
    {
        if ($this->plageHoraire === null) {
            return true;
        }
        return Horaire::dansPlage($timestamp, $this->plageHoraire[0], $this->plageHoraire[1]);
    }

    /** Prochain instant (>= $timestamp) où le véhicule a le droit de circuler. */
    public function prochaineOuverture(int $timestamp): int
    {
        if ($this->peutCirculer($timestamp)) {
            return $timestamp;
        }
        return Horaire::prochainDebut($timestamp, $this->plageHoraire[0]);
    }
}
