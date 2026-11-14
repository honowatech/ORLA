<?php

namespace App\Demo;

use Illuminate\Support\Facades\DB;

/**
 * Point d'entrée de la démo : réglages, connexion principale et bac actif.
 *
 * Isolation : un bac est un fichier SQLite à part. Pendant une requête démo,
 * la connexion par défaut pointe sur ce fichier ; la base principale n'est
 * plus jointe que par le registre (Sandbox), qui la nomme explicitement.
 */
class Demo
{
    public const CONNECTION = 'demo_sandbox';

    public const SESSION_KEY = 'demo.sandbox';

    private static ?string $mainConnection = null;

    private static ?Sandbox $current = null;

    public static function enabled(): bool
    {
        return (bool) config('demo.enabled');
    }

    /** Nom de la connexion principale, retenu avant toute bascule. */
    public static function mainConnection(): string
    {
        return self::$mainConnection ??= config('database.default');
    }

    /** Bac de la requête en cours, s'il y en a un. */
    public static function current(): ?Sandbox
    {
        return self::$current;
    }

    public static function active(): bool
    {
        return self::$current !== null;
    }

    public static function directory(): string
    {
        return rtrim(config('demo.path'), '/\\');
    }

    public static function sandboxPath(string $name): string
    {
        return self::directory().DIRECTORY_SEPARATOR.$name.'.sqlite';
    }

    /**
     * Fait pointer la connexion par défaut sur le fichier du bac.
     * Les connexions déjà ouvertes (session, cache) gardent la base principale.
     */
    public static function enter(Sandbox $sandbox): void
    {
        self::mainConnection();
        self::useFile($sandbox->path());
        self::$current = $sandbox;
    }

    /** Variante sans registre, pour le générateur et les tests. */
    public static function useFile(string $file): void
    {
        self::mainConnection();
        foreach (['cache.stores.database.connection', 'queue.connections.database.connection', 'session.connection'] as $key) {
            if (config($key) === null) {
                config([$key => self::mainConnection()]);
            }
        }

        DB::purge(self::CONNECTION);
        config(['database.connections.'.self::CONNECTION => [
            'driver' => 'sqlite',
            'database' => $file,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);
        DB::setDefaultConnection(self::CONNECTION);
    }

    /** Rétablit la base principale. */
    public static function leave(): void
    {
        self::$current = null;
        if (self::$mainConnection !== null) {
            DB::setDefaultConnection(self::$mainConnection);
        }
        DB::purge(self::CONNECTION);
    }

    /** Adresse du domaine réservé (RFC 2606) : jamais livrable. */
    public static function isReservedAddress(?string $email): bool
    {
        if ($email === null) {
            return false;
        }
        $domain = strtolower(substr(strrchr($email, '@') ?: '', 1));

        return $domain === config('demo.email_domain') || str_ends_with($domain, '.invalid') || $domain === 'invalid';
    }
}
