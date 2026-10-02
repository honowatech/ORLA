<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Un compte désactivé (users.statut = 0) est renvoyé vers la page d'information
 * (alias « actif »). Remplace les blocs `if ($statut == 0)` des contrôleurs.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $utilisateur = $request->user();

        if ($utilisateur !== null && (int) $utilisateur->statut === 0) {
            return redirect()->route('home.error');
        }

        return $next($request);
    }
}
