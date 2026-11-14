<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the interface language chosen by the visitor (FR / EN).
 *
 * Order of precedence: ?lang= query parameter, session, cookie,
 * browser preference (Accept-Language), then the configured default.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = array_keys(config('app.available_locales', []));
        $locale = $request->query('lang');

        if (! in_array($locale, $available, true)) {
            $locale = $request->hasSession() ? $request->session()->get('locale') : null;
        }
        if (! in_array($locale, $available, true)) {
            $locale = $request->cookie('locale');
        }
        if (! in_array($locale, $available, true)) {
            $locale = $request->getPreferredLanguage($available);
        }
        if (! in_array($locale, $available, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
