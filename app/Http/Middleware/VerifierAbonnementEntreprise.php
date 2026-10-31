<?php

namespace App\Http\Middleware;

use App\Tenancy\EntrepriseCourante;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie l'abonnement de l'entreprise de l'utilisateur connecté (et
 * uniquement celle-là). À placer après IdentifierEntreprise.
 *
 * - entreprise désactivée par le Super Admin : accès bloqué ;
 * - essai en cours ou abonnement à jour (période de grâce comprise) : accès complet ;
 * - sinon : lecture seule, toute modification renvoie vers la page de paiement.
 */
class VerifierAbonnementEntreprise
{
    public function __construct(protected EntrepriseCourante $courante)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $entreprise = $this->courante->entreprise();

        if (! $entreprise->estActive()) {
            return redirect()->route('SuperAdmin.site_inactif');
        }

        if ($entreprise->accesComplet()) {
            return $next($request);
        }

        view()->share('abonnement_expire', true);

        if ($request->isMethodSafe()) {
            return $next($request);
        }

        return redirect()->route(
            $entreprise->id_abonnement === null ? 'SuperAdmin.empty_abonnement' : 'SuperAdmin.no_abonnement'
        );
    }
}
