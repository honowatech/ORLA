<?php

namespace App\Http\Middleware;

use App\Enums\EtatAbonnement;
use App\Services\Abonnement\AbonnementService;
use App\Tenancy\CurrentEntreprise;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque l'espace de travail d'une entreprise suspendue ou dont l'abonnement est
 * expiré (alias « entreprise.active »). Remplace Check_Sa_Client_Error, qui
 * jugeait l'instance entière au lieu de l'entreprise de l'utilisateur.
 */
class EnsureEntrepriseActive
{
    public function __construct(
        private readonly CurrentEntreprise $courante,
        private readonly AbonnementService $abonnements,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $entreprise = $this->courante->get();

        if ($entreprise === null) {
            return $next($request);
        }

        $etat = $this->abonnements->etat($entreprise);

        View::share([
            'etatAbonnement' => $etat,
            'abonnementJoursRestants' => $this->abonnements->joursRestants($entreprise),
        ]);

        if ($etat->permetAcces()) {
            return $next($request);
        }

        if ($etat === EtatAbonnement::Suspendue) {
            return redirect()->route('abonnement.suspendue');
        }

        // Expiré : l'administrateur est invité à souscrire, les autres rôles sont informés.
        return $request->user()?->estAdministrateur()
            ? redirect()->route('Sc-transaction.create')
            : redirect()->route('abonnement.expire');
    }
}
