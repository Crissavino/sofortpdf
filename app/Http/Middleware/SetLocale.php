<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $default = config('locales.default', 'en');
        $supported = config('locales.supported', ['en']);
        $locale = $request->route('locale', $default);

        if (!in_array($locale, $supported, true)) {
            $locale = $default;
        }

        App::setLocale($locale);
        session()->put('locale', $locale);

        // Make locale available globally in views.
        view()->share('locale', $locale);
        // `altLocale` historically pointed at "the other" locale (binary
        // DE/EN setup). With four locales we expose the full list of
        // alternatives instead; legacy callers can fall back to the
        // first non-current entry.
        $alts = array_values(array_diff($supported, [$locale]));
        view()->share('altLocale', $alts[0] ?? $default);
        view()->share('altLocales', $alts);

        // Set URL defaults so route() includes the locale automatically
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
