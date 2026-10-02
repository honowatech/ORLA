<?php

namespace App\Http\Middleware;

use App\Tenancy\CurrentEntreprise;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Désactive le cloisonnement pour la requête (alias « tenancy.bypass ») :
 * console Super Admin et retours de paiement, qui ne s'exécutent pas dans le
 * contexte d'une entreprise connectée.
 */
class BypassTenancy
{
    public function __construct(private readonly CurrentEntreprise $courante) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->courante->forget();
        $this->courante->bypass(true);

        return $next($request);
    }
}
