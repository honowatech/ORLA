<?php

namespace App\Http\Middleware\Api;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restreint une route à un ou plusieurs rôles : ->middleware('api.role:coursier').
 * Vérifie aussi que le profil métier (coursier / client) est bien lié au compte.
 */
class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        /** @var User|null $user */
        $user = $request->user();
        $role = $user?->role();

        if ($role === null || ! in_array($role, $roles, true)) {
            return $this->refus('Cette action n\'est pas disponible pour votre profil.', 'role_non_autorise');
        }

        $profilManquant = match ($role) {
            User::ROLE_COURSIER => $user->coursier_utilisateur === null,
            User::ROLE_CLIENT => $user->client_utilisateur === null,
            default => false,
        };

        if ($profilManquant) {
            return $this->refus('Votre compte n\'est lié à aucun profil '.$role.'. Contactez votre administrateur.', 'profil_non_lie');
        }

        return $next($request);
    }

    private function refus(string $message, string $code): Response
    {
        return response()->json(['message' => $message, 'code' => $code], Response::HTTP_FORBIDDEN);
    }
}
