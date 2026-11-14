<?php

namespace App\Http\Controllers;

use App\Demo\Demo;
use App\Demo\SandboxPool;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Parcours du visiteur : page des profils, entrée, changement de profil, fin.
 */
class DemoController extends Controller
{
    public function __construct(private SandboxPool $pool) {}

    public function index()
    {
        abort_unless(Demo::enabled(), 404);

        return response()
            ->view('demo.index', [
                'profiles' => config('demo.profiles'),
                'ready' => $this->pool->readyCount() > 0 || config('demo.build_on_demand'),
                'inDemo' => session()->has(Demo::SESSION_KEY),
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /** POST /demo (guest, throttle:demo) : le formulaire n'envoie qu'un slug de profil. */
    public function enter(Request $request)
    {
        abort_unless(Demo::enabled(), 404);

        $slug = $request->validate([
            'profile' => ['required', 'string', Rule::in(array_keys(config('demo.profiles')))],
        ])['profile'];

        // Visiteur déconnecté uniquement, y compris de la console plateforme.
        if ($request->session()->has('SuperAdmin_infos')) {
            return redirect()->route('demo.index')
                ->with('demo_message', 'Déconnectez-vous de la console Super Admin avant d\'ouvrir une démo.');
        }

        try {
            $sandbox = $this->pool->claim();
        } catch (Throwable $e) {
            report($e);
            $sandbox = null;
        }
        if ($sandbox === null) {
            return redirect()->route('demo.index')
                ->with('demo_message', 'Toutes les démos sont occupées pour le moment. Réessayez dans quelques minutes.');
        }

        Demo::enter($sandbox);
        try {
            $user = $this->profileUser($slug);
            if ($user === null) {
                $sandbox->ended_at = now();
                $sandbox->save();

                return redirect()->route('demo.index')
                    ->with('demo_message', 'Cette démo n\'a pas pu être ouverte. Réessayez.');
            }

            $request->session()->invalidate();
            Auth::login($user);
            $request->session()->put(Demo::SESSION_KEY, $sandbox->claim_token);
        } finally {
            Demo::leave();
        }

        Log::info('demo.enter', ['sandbox' => $sandbox->id, 'profile' => $slug]);

        return redirect()->route('home');
    }

    /** POST /demo/profile : un autre utilisateur du même bac. */
    public function switchProfile(Request $request)
    {
        abort_unless(Demo::enabled(), 404);
        $sandbox = Demo::current();
        abort_unless($sandbox !== null && $sandbox->isOpen()
            && hash_equals($sandbox->claim_token, (string) $request->session()->get(Demo::SESSION_KEY)), 403);

        $slug = $request->validate([
            'profile' => ['required', 'string', Rule::in(array_keys(config('demo.profiles')))],
        ])['profile'];

        $user = $this->profileUser($slug);
        abort_if($user === null, 403);

        Auth::login($user);
        $request->session()->put(Demo::SESSION_KEY, $sandbox->claim_token);
        Log::info('demo.profile', ['sandbox' => $sandbox->id, 'profile' => $slug]);

        return redirect()->route('home');
    }

    /** POST /demo/end : déconnecte et marque le bac pour la purge. */
    public function end(Request $request)
    {
        abort_unless(Demo::enabled(), 404);
        $sandbox = Demo::current();
        abort_unless($sandbox !== null
            && hash_equals($sandbox->claim_token, (string) $request->session()->get(Demo::SESSION_KEY)), 403);

        $sandbox->ended_at = now();
        $sandbox->save();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $signup = config('demo.signup_url');
        if ($request->boolean('signup') && $signup) {
            Log::info('demo.signup', ['sandbox' => $sandbox->id]);

            return redirect()->away($signup);
        }

        return redirect()->route('demo.index')
            ->with('demo_message', 'Démo terminée : ses données ont été effacées.');
    }

    private function profileUser(string $slug): ?User
    {
        return User::where('email', $slug.'@'.config('demo.email_domain'))
            ->where('statut', 1)
            ->first();
    }
}
