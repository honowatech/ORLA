<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Espace Super Admin : authentification par session (SuperAdmin_infos).
 * Sans session, mémorise la page demandée et renvoie vers la connexion.
 */
class VerifierSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (check_superadmin() != 'true') {
            session()->put('dernier_url', url()->current());

            return redirect()->route('SuperAdmin.login');
        }

        return $next($request);
    }
}
