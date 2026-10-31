<?php

namespace App\Http\Middleware;

use App\Models\Entreprise;
use App\Tenancy\EntrepriseCourante;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Détermine l'entreprise de la requête à partir de l'utilisateur connecté.
 * À placer juste après « auth ».
 */
class IdentifierEntreprise
{
    public function __construct(protected EntrepriseCourante $courante)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $entreprise = $user?->entreprise_id ? Entreprise::find($user->entreprise_id) : null;

        if ($entreprise === null) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors([
                'email' => "Votre compte n'est rattaché à aucune entreprise active.",
            ]);
        }

        $this->courante->definir($entreprise);
        view()->share('entreprise_courante', $entreprise);

        try {
            return $next($request);
        } finally {
            $this->courante->oublier();
        }
    }
}
