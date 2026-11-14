<?php

namespace App\Http\Middleware;

use App\Demo\Demo;
use App\Demo\Sandbox;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garde démo, dans le groupe « web » : aucune route ne peut l'oublier.
 *
 * Une session démo est servie par son bac et seulement lui. Si le bac est
 * introuvable, terminé ou expiré, la session est détruite sans jamais lire
 * l'utilisateur : son identifiant désigne une ligne du bac, et le résoudre
 * dans la base principale ouvrirait le compte réel de même identifiant.
 *
 * Placé avant Authenticate dans l'ordre de priorité (bootstrap/app.php).
 */
class DemoSandbox
{
    /** Interdit en démo : console plateforme, abonnement et paiement Monetbil, comptes. */
    private const FORBIDDEN = [
        'superadmin', 'superadmin/*', 'Sc-*', 'Sa-*',
        'register', 'password/*', 'login',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        Demo::leave();

        if (! $request->hasSession() || ! $request->session()->has(Demo::SESSION_KEY)) {
            return $next($request);
        }

        $session = $request->session();
        $sandbox = Sandbox::where('claim_token', (string) $session->get(Demo::SESSION_KEY))->first();

        if (! Demo::enabled() || $sandbox === null || ! $sandbox->isOpen() || ! File::exists($sandbox->path())) {
            if ($sandbox !== null && $sandbox->ended_at === null) {
                $sandbox->ended_at = now();
                $sandbox->save();
            }
            $session->invalidate();
            $session->regenerateToken();

            if (! Demo::enabled()) {
                return redirect('/login');
            }

            return redirect()->route('demo.index')
                ->with('demo_message', 'Votre démo est terminée et ses données ont été effacées. Vous pouvez en ouvrir une nouvelle.');
        }

        if ($request->is(...self::FORBIDDEN)) {
            return redirect()->route('home')
                ->with('message', "<b class='text-danger'>Indisponible en démo.</b>");
        }

        Demo::enter($sandbox);
        try {
            $sandbox->touchActivity();
            $response = $next($request);
        } finally {
            Demo::leave();
        }

        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
