<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restreint un groupe de routes à certains types d'utilisateur.
 *
 * Usage : ->middleware('role:admin,agent'). Les rôles correspondent aux
 * libellés de la table type_utilisateur (« admin » désigne « Super Admin »).
 * Un compte désactivé est envoyé vers home.error ; un rôle non autorisé
 * est renvoyé vers son propre espace, comme le faisaient les contrôleurs.
 */
class VerifierRole
{
    /** Libellé en base (en majuscules) => nom de rôle utilisé dans les routes. */
    private const ROLES = [
        'SUPER ADMIN' => 'admin',
        'AGENT' => 'agent',
        'COURSIER' => 'coursier',
        'CLIENT' => 'client',
        'SUPERVISEUR_VILLE' => 'superviseur_ville',
    ];

    /** Espace d'accueil de chaque rôle. */
    private const ACCUEILS = [
        'admin' => 'home.admin',
        'agent' => 'home.admin',
        'coursier' => 'home.coursier',
        'client' => 'home.client',
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user->statut == 0) {
            return redirect()->route('home.error');
        }

        $libelle = strtoupper((string) $user->type_utilisateur?->libelle);
        $role = self::ROLES[$libelle] ?? null;

        if (in_array($role, $roles, true)) {
            return $next($request);
        }

        abort_unless(isset(self::ACCUEILS[$role]), 403);

        return redirect()->route(self::ACCUEILS[$role]);
    }
}
