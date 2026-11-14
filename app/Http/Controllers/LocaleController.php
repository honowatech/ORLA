<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the interface language and go back to the previous page.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('app.available_locales', [])), 404);

        $request->session()->put('locale', $locale);

        return redirect()->back(fallback: url('/'))
            ->withCookie(cookie()->forever('locale', $locale));
    }
}
