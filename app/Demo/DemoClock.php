<?php

namespace App\Demo;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Horloge du rejeu : chaque événement est joué à son heure, si bien que les
 * dates que posent les actions (created_at, date_livre…) sont les bonnes sans
 * que le générateur sache quelles colonnes en portent une.
 */
class DemoClock
{
    private static bool $holding = false;

    private static ?CarbonInterface $saved = null;

    private static ?CarbonInterface $savedImmutable = null;

    public static function at(CarbonInterface $moment): void
    {
        if (! self::$holding) {
            self::$saved = Carbon::getTestNow();
            self::$savedImmutable = CarbonImmutable::getTestNow();
            self::$holding = true;
        }
        Carbon::setTestNow($moment);
        CarbonImmutable::setTestNow($moment);
    }

    /** Rétablit l'horloge d'avant le premier at(), même après une erreur. */
    public static function restore(): void
    {
        if (! self::$holding) {
            return;
        }
        Carbon::setTestNow(self::$saved);
        CarbonImmutable::setTestNow(self::$savedImmutable);
        self::$saved = self::$savedImmutable = null;
        self::$holding = false;
    }
}
