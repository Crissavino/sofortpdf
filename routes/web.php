<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root redirect → locale based on IP geolocation
|
| Maps the visitor's country to a supported locale; anything outside that
| mapping falls back to the default ('en'). 302 (not 301) so we can flip
| the mapping later without users being stuck via permanent-redirect
| browser caching. GetIpInformation caches per-IP for 30 days, so the
| geolocation API is only hit on first visit per IP.
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $default = config('locales.default', 'en');
    $supported = config('locales.supported', ['en']);

    // Country code (ISO 3166-1 alpha-2, uppercase) → site locale.
    // Anything not listed falls through to $default.
    $countryToLocale = [
        'DE' => 'de', 'AT' => 'de', 'CH' => 'de', 'LI' => 'de',
        'HU' => 'hu',
        'CZ' => 'cs',
        'PL' => 'pl',
    ];

    $detected = $default;
    try {
        $cc = \App\Classes\GetIpInformation::get(request()->ip(), 'countryCode');
        if ($cc) {
            $cc = strtoupper((string) $cc);
            if (isset($countryToLocale[$cc]) && in_array($countryToLocale[$cc], $supported, true)) {
                $detected = $countryToLocale[$cc];
            }
        }
    } catch (\Throwable $e) {
        // Geolocation failed — silently fall through to default.
    }

    return redirect("/{$detected}", 302);
});

/*
|--------------------------------------------------------------------------
| Sitemap
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', function () {
    $baseUrl = 'https://sofortpdf.com';
    $tools = config('tools');
    $aliases = $tools['aliases'] ?? [];
    unset($tools['aliases']);

    $urls = [];
    $supportedLocales = config('locales.supported', ['en']);

    // Per-locale homepage priorities. HU/CS/PL are the active ad markets
    // and the primary SEO focus; DE/EN no longer get paid traffic so they
    // sit a tier lower (still indexed for organic).
    $homepagePriority = [
        'hu' => '1.0',
        'cs' => '1.0',
        'pl' => '1.0',
        'de' => '0.7',
        'en' => '0.7',
    ];
    foreach ($supportedLocales as $locale) {
        $urls[] = [
            'loc'        => "{$baseUrl}/{$locale}",
            'priority'   => $homepagePriority[$locale] ?? '0.5',
            'changefreq' => 'daily',
        ];
    }

    // Tool pages (only enabled) — all supported locales
    foreach ($supportedLocales as $locale) {
        $slugs = config("locales.tool_slugs.{$locale}", []);
        // HU/CS/PL are the active ad + SEO markets, so their tool pages
        // get the highest priority; DE/EN are secondary (no paid traffic).
        $priority = in_array($locale, ['hu', 'cs', 'pl'], true) ? '0.9' : '0.6';
        foreach ($tools as $key => $tool) {
            if (empty($tool['enabled'])) continue;
            if (!isset($slugs[$key])) continue;
            $urls[] = [
                'loc'        => "{$baseUrl}/{$locale}/{$slugs[$key]}",
                'priority'   => $priority,
                'changefreq' => 'weekly',
            ];
        }
    }

    // Alias pages — all supported locales (DE has the bulk of these)
    foreach ($supportedLocales as $locale) {
        $localeAliases = config("locales.aliases.{$locale}", []);
        foreach ($localeAliases as $aliasSlug => $toolKey) {
            if (isset($tools[$toolKey]) && !empty($tools[$toolKey]['enabled'])) {
                $urls[] = [
                    'loc'        => "{$baseUrl}/{$locale}/{$aliasSlug}",
                    'priority'   => '0.6',
                    'changefreq' => 'weekly',
                ];
            }
        }
    }

    // Static pages — emit per locale using the locale-specific slugs
    // resolved from config so DE keeps "impressum" while EN/HU/CS use
    // "imprint", etc.
    $staticPageDefs = [
        ['priority' => '0.5', 'resolve' => fn($l) => config("locales.contact_slugs.{$l}")],
        ['priority' => '0.3', 'resolve' => fn($l) => config("locales.legal_slugs.{$l}.imprint")],
        ['priority' => '0.3', 'resolve' => fn($l) => config("locales.legal_slugs.{$l}.privacy")],
        ['priority' => '0.3', 'resolve' => fn($l) => config("locales.legal_slugs.{$l}.terms")],
        ['priority' => '0.4', 'resolve' => fn($l) => config("locales.auth_slugs.{$l}.login")],
        ['priority' => '0.3', 'resolve' => fn($l) => config("locales.cancellation_slugs.{$l}")],
        ['priority' => '0.2', 'resolve' => fn($l) => config("locales.legal_slugs.{$l}.cookies")],
    ];
    foreach ($supportedLocales as $locale) {
        // Nudge crawl budget toward the active markets (HU/CS/PL); the
        // now-secondary DE/EN static pages get a small penalty.
        $priorityOffset = in_array($locale, ['hu', 'cs', 'pl'], true) ? 0.0 : -0.1;
        foreach ($staticPageDefs as $page) {
            $slug = $page['resolve']($locale);
            if (!is_string($slug) || $slug === '') continue;
            $urls[] = [
                'loc'        => "{$baseUrl}/{$locale}/{$slug}",
                'priority'   => (string) max(0.1, (float)$page['priority'] + $priorityOffset),
                'changefreq' => 'monthly',
            ];
        }
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
    foreach ($urls as $u) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>{$u['loc']}</loc>\n";
        $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
        $xml .= "    <priority>{$u['priority']}</priority>\n";
        $xml .= "  </url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
});

/*
|--------------------------------------------------------------------------
| Stripe Webhook (no locale, no CSRF)
|--------------------------------------------------------------------------
*/
// Stripe webhooks — one route per Stripe account (same pattern as conversie-pdf).
// Each account is configured in Stripe Dashboard to point to its endpoint.
Route::post('/stripe/webhook',      [WebhookController::class, 'handle'])->name('stripe.webhook');
Route::post('/stripe/webhook-jack', [WebhookController::class, 'handleJack'])->name('stripe.webhook.jack');

/*
|--------------------------------------------------------------------------
| API routes (no locale prefix)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::post('/upload', [UploadController::class, 'store']);

    // Payment — 3-step flow delegated to BO (same as contract-kit)
    Route::post('/payment/create-customer', [\App\Http\Controllers\Api\PaymentController::class, 'createCustomer']);
    Route::post('/payment/pay-trial', [\App\Http\Controllers\Api\PaymentController::class, 'payTrial']);
    Route::post('/payment/create-subscription', [\App\Http\Controllers\Api\PaymentController::class, 'createSubscription']);
});

Route::middleware(['paywall'])->prefix('api')->group(function () {
    Route::post('/convert', [ConversionController::class, 'convert']);
    Route::post('/sign', [SignatureController::class, 'sign']);
});

// Polling endpoint for the confirmation page (no paywall, otherwise the
// user couldn't check their own job status from a guest session).
Route::post('/api/log/event', function (\Illuminate\Http\Request $request) {
    $event = $request->input('event', 'unknown');

    // Tag entries from internal IPs (dev/QA) so analysis can filter them
    // out of conversion ratios. Set INTERNAL_IPS=ip1,ip2 in .env.
    $internalIps = array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('INTERNAL_IPS', ''))
    )));
    $isInternal = !empty($internalIps) && in_array($request->ip(), $internalIps, true);

    \Illuminate\Support\Facades\Log::channel('activity')->info("client_event", [
        'event'       => $event,
        'ip'          => $request->ip(),
        'user'        => $request->user() ? $request->user()->id : null,
        'path'        => $request->headers->get('referer', ''),
        'is_internal' => $isInternal,
    ]);
    return response()->json(['ok' => true]);
});

Route::get('/api/convert/status/{id}', [ConversionController::class, 'status'])
     ->name('api.convert.status');

// Webhook from the conversion-service. Public (no auth, no CSRF) so the
// external worker can POST back. Tampering is bounded by requiring a known
// job_id that's still in cache.
Route::post('/api/webhooks/conversion', [ConversionController::class, 'webhook'])
     ->name('api.webhooks.conversion')
     ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

/*
|--------------------------------------------------------------------------
| Download (no locale prefix, auth required)
|--------------------------------------------------------------------------
*/
Route::get('/download/document/{id}', [DownloadController::class, 'downloadDocument'])
     ->name('download.document')
     ->middleware('auth');

Route::get('/download/{token}', [DownloadController::class, 'download'])
     ->middleware('paywall')
     ->name('download');

/*
|--------------------------------------------------------------------------
| All localized routes under /{locale}
|--------------------------------------------------------------------------
*/
Route::prefix('{locale}')
     ->where(['locale' => 'de|en|hu|cs|pl'])
     ->middleware(['locale', 'resolve-vad'])
     ->group(function () {

    /*
    |----------------------------------------------------------------------
    | Homepage
    |----------------------------------------------------------------------
    */
    Route::get('/', [HomeController::class, 'index'])->name('home');

    /*
    |----------------------------------------------------------------------
    | Tool pages — all slugs (DE + EN + aliases)
    |----------------------------------------------------------------------
    */
    $allToolSlugs = [
        // DE slugs
        'pdf-zusammenfuegen'    => ['merge',       'PDF zusammenfügen'],
        'pdf-komprimieren'      => ['compress',     'PDF komprimieren'],
        'jpg-zu-pdf'            => ['jpg-to-pdf',   'JPG zu PDF'],
        'pdf-zu-word'           => ['pdf-to-word',  'PDF zu Word'],
        'word-zu-pdf'           => ['word-to-pdf',  'Word zu PDF'],
        'pdf-zu-jpg'            => ['pdf-to-jpg',   'PDF zu JPG'],
        'pdf-trennen'           => ['split',        'PDF trennen'],
        'pdf-bearbeiten'        => ['edit',         'PDF bearbeiten'],
        'pdf-unterzeichnen'     => ['sign',         'PDF unterzeichnen'],
        'pdf-in-excel'          => ['pdf-to-excel', 'PDF in Excel'],
        'excel-zu-pdf'          => ['excel-to-pdf', 'Excel zu PDF'],
        // DE new tools
        'pdf-drehen'            => ['rotate',        'PDF drehen'],
        'pdf-schuetzen'         => ['protect',       'PDF schützen'],
        'pdf-entsperren'        => ['unlock',        'PDF entsperren'],
        'wasserzeichen-hinzufuegen' => ['watermark', 'Wasserzeichen hinzufügen'],
        'seitenzahlen-hinzufuegen'  => ['page-numbers', 'Seitenzahlen hinzufügen'],
        'pdf-zu-powerpoint'     => ['pdf-to-ppt',   'PDF zu PowerPoint'],
        'powerpoint-zu-pdf'     => ['ppt-to-pdf',   'PowerPoint zu PDF'],
        'pdf-zu-png'            => ['pdf-to-png',   'PDF zu PNG'],
        'png-zu-pdf'            => ['png-to-pdf',   'PNG zu PDF'],
        'text-erkennen-ocr'     => ['ocr',          'Text erkennen (OCR)'],
        'seiten-entfernen'      => ['remove-pages', 'Seiten entfernen'],
        'seiten-extrahieren'    => ['extract-pages', 'Seiten extrahieren'],
        'html-zu-pdf'           => ['html-to-pdf',  'HTML zu PDF'],
        'pdf-optimieren'        => ['optimize',     'PDF optimieren'],
        // DE aliases
        'pdf-verkleinern'       => ['compress',     'PDF verkleinern'],
        'pdf-in-word-umwandeln' => ['pdf-to-word',  'PDF in Word umwandeln'],
        'jpg-in-pdf-umwandeln'  => ['jpg-to-pdf',   'JPG in PDF umwandeln'],
        'pdf-in-jpg-umwandeln'  => ['pdf-to-jpg',   'PDF in JPG umwandeln'],
        // EN slugs
        'merge-pdf'             => ['merge',        'Merge PDF'],
        'compress-pdf'          => ['compress',      'Compress PDF'],
        'jpg-to-pdf'            => ['jpg-to-pdf',    'JPG to PDF'],
        'pdf-to-word'           => ['pdf-to-word',   'PDF to Word'],
        'word-to-pdf'           => ['word-to-pdf',   'Word to PDF'],
        'pdf-to-jpg'            => ['pdf-to-jpg',    'PDF to JPG'],
        'split-pdf'             => ['split',         'Split PDF'],
        'edit-pdf'              => ['edit',          'Edit PDF'],
        'sign-pdf'              => ['sign',          'Sign PDF'],
        'pdf-to-excel'          => ['pdf-to-excel',  'PDF to Excel'],
        'excel-to-pdf'          => ['excel-to-pdf',  'Excel to PDF'],
        // EN new tools
        'rotate-pdf'            => ['rotate',        'Rotate PDF'],
        'protect-pdf'           => ['protect',       'Protect PDF'],
        'unlock-pdf'            => ['unlock',        'Unlock PDF'],
        'add-watermark'         => ['watermark',     'Add Watermark'],
        'add-page-numbers'      => ['page-numbers',  'Add Page Numbers'],
        'pdf-to-powerpoint'     => ['pdf-to-ppt',    'PDF to PowerPoint'],
        'powerpoint-to-pdf'     => ['ppt-to-pdf',    'PowerPoint to PDF'],
        'pdf-to-png'            => ['pdf-to-png',    'PDF to PNG'],
        'png-to-pdf'            => ['png-to-pdf',    'PNG to PDF'],
        'ocr-pdf'               => ['ocr',           'OCR — Recognize Text'],
        'remove-pages'          => ['remove-pages',  'Remove Pages'],
        'extract-pages'         => ['extract-pages', 'Extract Pages'],
        'html-to-pdf'           => ['html-to-pdf',   'HTML to PDF'],
        'optimize-pdf'          => ['optimize',      'Optimize PDF'],
        // Image to PDF (any image format)
        'bild-zu-pdf'           => ['image-to-pdf',  'Bild zu PDF'],
        'image-to-pdf'          => ['image-to-pdf',  'Image to PDF'],
        // EN aliases
        'reduce-pdf-size'       => ['compress',      'Reduce PDF Size'],
    ];

    foreach ($allToolSlugs as $slug => [$toolKey, $pageTitle]) {
        Route::get("/{$slug}", [ToolController::class, 'show'])
             ->defaults('tool', $toolKey)
             ->defaults('pageTitle', $pageTitle);
    }

    /*
    |----------------------------------------------------------------------
    | Auth — all locale slugs
    |----------------------------------------------------------------------
    */
    // DE auth
    Route::get('/anmelden', [LoginController::class, 'showForm'])->name('login.de');
    Route::post('/anmelden', [LoginController::class, 'login']);
    Route::post('/abmelden', [LoginController::class, 'logout'])->name('logout.de');
    Route::get('/passwort-reset', [PasswordResetController::class, 'showForm'])->name('password.request.de');
    Route::post('/passwort-reset', [PasswordResetController::class, 'sendResetLink'])->name('password.email.de');
    Route::get('/passwort-reset/{token}', [PasswordResetController::class, 'showResetConfirmForm'])->name('password.reset.de');
    Route::post('/passwort-reset-speichern', [PasswordResetController::class, 'reset'])->name('password.update.de');
    // EN auth
    Route::get('/login', [LoginController::class, 'showForm'])->name('login.en');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout.en');
    Route::get('/password-reset', [PasswordResetController::class, 'showForm'])->name('password.request.en');
    Route::post('/password-reset', [PasswordResetController::class, 'sendResetLink'])->name('password.email.en');
    Route::get('/password-reset/{token}', [PasswordResetController::class, 'showResetConfirmForm'])->name('password.reset.en');
    Route::post('/password-reset-save', [PasswordResetController::class, 'reset'])->name('password.update.en');

    // Old standalone checkout removed — payments go through the modal
    // (/api/payment/create-customer → pay-trial → create-subscription).
    // Kept route name 'checkout.start' as redirect to home for any stale links.
    Route::get('/checkout/start', function () {
        return redirect()->route('home', ['locale' => app()->getLocale()]);
    })->name('checkout.start');

    /*
    |----------------------------------------------------------------------
    | Confirmation page (after every successful tool conversion)
    |----------------------------------------------------------------------
    */
    Route::get('/confirmation', [ConfirmationController::class, 'show'])->name('confirmation');

    /*
    |----------------------------------------------------------------------
    | Dashboard
    |----------------------------------------------------------------------
    */
    // Public demo (gated by PAYMENT_BYPASS_IPS so only allowlisted IPs can
    // hit it). Lets us preview the dashboard UI while the shared DB is
    // unreachable — renders the index view with fake data, no login.
    Route::get('/dashboard/demo', [DashboardController::class, 'demo'])
         ->middleware(\App\Http\Middleware\Paywall::class)
         ->name('dashboard.demo');

    Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/downloads', [DashboardController::class, 'downloads'])->name('downloads');
        Route::get('/billing', [DashboardController::class, 'billing'])->name('billing');
        Route::get('/billing/portal', [DashboardController::class, 'billingPortal'])->name('billing.portal');
        Route::post('/billing/cancel', [DashboardController::class, 'cancelSubscription'])->name('billing.cancel');
        Route::get('/profil', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profil', [DashboardController::class, 'updateProfile'])->name('profile.update');
    });

    /*
    |----------------------------------------------------------------------
    | Legal — all locale slugs
    |----------------------------------------------------------------------
    */
    /*
    |----------------------------------------------------------------------
    | Contact — all locale slugs
    |----------------------------------------------------------------------
    */
    Route::get('/kontakt',  [ContactController::class, 'show'])->name('contact.show.de');
    Route::post('/kontakt', [ContactController::class, 'send'])->name('contact.send.de');
    Route::get('/contact',  [ContactController::class, 'show'])->name('contact.show.en');
    Route::post('/contact', [ContactController::class, 'send'])->name('contact.send.en');

    // Cancellation
    Route::get('/kuendigen',  [\App\Http\Controllers\CancellationController::class, 'form'])->name('cancellation.form.de');
    Route::post('/kuendigen', [\App\Http\Controllers\CancellationController::class, 'process'])->name('cancellation.process.de');
    Route::get('/cancel',     [\App\Http\Controllers\CancellationController::class, 'form'])->name('cancellation.form.en');
    Route::post('/cancel',    [\App\Http\Controllers\CancellationController::class, 'process'])->name('cancellation.process.en');

    // Cookie Policy
    Route::get('/cookie-richtlinie', [LegalController::class, 'cookiePolicy'])->name('cookies.de');
    Route::get('/cookie-policy',     [LegalController::class, 'cookiePolicy'])->name('cookies.en');

    // DE legal
    Route::get('/impressum', [LegalController::class, 'imprint'])->name('impressum.de');
    Route::get('/datenschutz', [LegalController::class, 'privacy'])->name('datenschutz.de');
    Route::get('/agb', [LegalController::class, 'terms'])->name('agb.de');
    // EN legal
    Route::get('/imprint', [LegalController::class, 'imprint'])->name('impressum.en');
    Route::get('/privacy', [LegalController::class, 'privacy'])->name('datenschutz.en');
    Route::get('/terms', [LegalController::class, 'terms'])->name('agb.en');

    /*
    |----------------------------------------------------------------------
    | HU + CS reuse the English URI surface above. Because the URI prefix
    | is `{locale}`, the same `/login`, `/contact`, `/imprint`, etc.
    | routes already match `/hu/login`, `/cs/contact`, etc. — no extra
    | route registrations are needed here. Views build action URLs
    | directly from `config('locales.*_slugs.*')` instead of relying on
    | locale-suffixed route names (Laravel deduplicates duplicate URIs
    | by name, so `route('contact.send.hu')` would be unreliable).
    |----------------------------------------------------------------------
    */
});

/*
|--------------------------------------------------------------------------
| Named route aliases — fallback short names used in views
| These redirect to the locale-appropriate version
|--------------------------------------------------------------------------
*/
Route::get('/registrieren', function () { return redirect('/' . config('locales.default', 'en')); })->name('register');

// Contact fallback — redirect to locale-appropriate slug
Route::get('/contact-redirect', function () {
    $locale = session('locale', config('locales.default', 'en'));
    $slug   = config("locales.contact_slugs.{$locale}", 'kontakt');
    return redirect("/{$locale}/{$slug}");
})->name('contact');

// Auth fallbacks (Laravel's auth middleware looks for 'login')
Route::get('/login', function () {
    $locale = session('locale', config('locales.default', 'en'));
    $slug = config("locales.auth_slugs.{$locale}.login", 'anmelden');
    return redirect("/{$locale}/{$slug}");
})->name('login');

Route::post('/logout', function () {
    $locale = session('locale', config('locales.default', 'en'));
    $slug = config("locales.auth_slugs.{$locale}.logout", 'abmelden');
    return redirect("/{$locale}/{$slug}");
})->name('logout');

Route::get('/password-reset', function () {
    $locale = session('locale', config('locales.default', 'en'));
    $slug = config("locales.auth_slugs.{$locale}.password_reset", 'passwort-reset');
    return redirect("/{$locale}/{$slug}");
})->name('password.request');

// Default named routes Laravel's password broker looks up. The actual
// request form lives under the localized routes; these just redirect.
Route::get('/password-reset/{token}', function ($token) {
    $locale = session('locale', config('locales.default', 'en'));
    $slug   = config("locales.auth_slugs.{$locale}.password_reset", 'passwort-reset');
    $query  = request()->query();
    $qs     = $query ? ('?' . http_build_query($query)) : '';
    return redirect("/{$locale}/{$slug}/{$token}{$qs}");
})->name('password.reset');

Route::post('/password-reset-save', function () {
    $locale = session('locale', config('locales.default', 'en'));
    $url    = route('password.update.' . $locale);
    return redirect()->to($url);
})->name('password.update');

// Legal fallbacks
Route::get('/impressum-redirect', function () {
    $locale = session('locale', config('locales.default', 'en'));
    $slug = config("locales.legal_slugs.{$locale}.imprint", 'impressum');
    return redirect("/{$locale}/{$slug}");
})->name('impressum');

Route::get('/datenschutz-redirect', function () {
    $locale = session('locale', config('locales.default', 'en'));
    $slug = config("locales.legal_slugs.{$locale}.privacy", 'datenschutz');
    return redirect("/{$locale}/{$slug}");
})->name('datenschutz');

Route::get('/agb-redirect', function () {
    $locale = session('locale', config('locales.default', 'en'));
    $slug = config("locales.legal_slugs.{$locale}.terms", 'agb');
    return redirect("/{$locale}/{$slug}");
})->name('agb');
