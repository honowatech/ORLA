<?php

namespace App\Services\Routage;

/**
 * Utilitaires de plages horaires journalières ("HH:MM").
 * Une plage peut chevaucher minuit (ex : 22:00 → 06:00).
 */
class Horaire
{
    public static function minutesDepuisMinuit(int $timestamp): int
    {
        return (int) date('G', $timestamp) * 60 + (int) date('i', $timestamp);
    }

    public static function versMinutes(string $hhmm): int
    {
        [$h, $m] = array_pad(explode(':', $hhmm), 2, 0);
        return (int) $h * 60 + (int) $m;
    }

    /** L'instant est-il compris dans [debut, fin] (fin incluse) ? */
    public static function dansPlage(int $timestamp, string $debut, string $fin): bool
    {
        $t = self::minutesDepuisMinuit($timestamp);
        $d = self::versMinutes($debut);
        $f = self::versMinutes($fin);
        if ($d <= $f) {
            return $t >= $d && $t <= $f;
        }
        // plage à cheval sur minuit
        return $t >= $d || $t <= $f;
    }

    /** Prochain timestamp (>= $timestamp) correspondant à l'heure "HH:MM". */
    public static function prochainDebut(int $timestamp, string $debut): int
    {
        $t = self::minutesDepuisMinuit($timestamp);
        $d = self::versMinutes($debut);
        $minuit = $timestamp - $t * 60 - (int) date('s', $timestamp);
        $candidat = $minuit + $d * 60;
        if ($candidat < $timestamp) {
            $candidat += 86400;
        }
        return $candidat;
    }

    /**
     * Coefficient de trafic applicable à un instant, d'après une table
     * ["HH:MM-HH:MM" => coefficient]. 1.0 hors des plages.
     */
    public static function coefficientTrafic(int $timestamp, array $table): float
    {
        foreach ($table as $plage => $coef) {
            [$debut, $fin] = explode('-', $plage);
            if (self::dansPlage($timestamp, trim($debut), trim($fin))) {
                return (float) $coef;
            }
        }
        return 1.0;
    }

    public static function formatDuree(float $minutes): string
    {
        $minutes = (int) round($minutes);
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return $h > 0 ? sprintf('%dh%02d', $h, $m) : sprintf('%d min', $m);
    }
}
