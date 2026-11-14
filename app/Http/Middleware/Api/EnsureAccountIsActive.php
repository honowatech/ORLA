<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Refuse l'accès (et révoque le jeton courant) si le compte a été désactivé.
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && ! $user->estActif()) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'message' => 'Votre compte est désactivé. Contactez votre administrateur.',
                'code' => 'compte_inactif',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
