<?php

namespace App\Http\Middleware;

use App\Models\Entreprise;
use App\Tenancy\CurrentEntreprise;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Résout l'entreprise courante à partir de l'utilisateur connecté (alias « entreprise »).
 * Un utilisateur sans entreprise valide est déconnecté.
 */
class SetCurrentEntreprise
{
    public function __construct(private readonly CurrentEntreprise $courante) {}

    public function handle(Request $request, Closure $next): Response
    {
        $utilisateur = $request->user();

        if ($utilisateur === null) {
            $this->courante->forget();

            return $next($request);
        }

        $entreprise = $utilisateur->entreprise_id
            ? Entreprise::find($utilisateur->entreprise_id)
            : null;

        if ($entreprise === null) {
            $this->courante->forget();
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('message', "Votre compte n'est rattaché à aucune entreprise active. Contactez votre administrateur.");
        }

        $this->courante->set($entreprise);

        return $next($request);
    }
}
