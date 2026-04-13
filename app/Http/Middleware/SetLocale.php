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
        $locale = $request->route('locale', config('locales.default', 'de'));
        $supported = config('locales.supported', ['de', 'en']);

        if (!in_array($locale, $supported)) {
            $locale = config('locales.default', 'de');
        }

        App::setLocale($locale);
        session()->put('locale', $locale);

        // Make locale available globally in views
        view()->share('locale', $locale);
        view()->share('altLocale', $locale === 'de' ? 'en' : 'de');

        // Set URL defaults so route() includes the locale automatically
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
