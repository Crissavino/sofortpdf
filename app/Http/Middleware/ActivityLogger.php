<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Logs key user activity for monitoring. Lightweight — only logs
 * meaningful page views, not every request.
 */
class ActivityLogger
{
    private const TRACKED_PATTERNS = [
        'tool_page'   => '#^[a-z]{2}/(pdf-|merge-|compress-|jpg-|word-|excel-|ppt-|powerpoint-|png-|image-|split-|sign-|rotate-|unlock-|add-|ocr-|remove-|extract-|optimize-|html-|bild-)#',
        'home'        => '#^[a-z]{2}$#',
        'login'       => '#/(anmelden|login)$#',
        'dashboard'   => '#/dashboard#',
        'billing'     => '#/dashboard/billing#',
        'cancel'      => '#/(kuendigen|cancel)$#',
        'contact'     => '#/(kontakt|contact)$#',
        'legal'       => '#/(impressum|imprint|datenschutz|privacy|agb|terms|cookie-)#',
        'confirmation'=> '#/confirmation#',
    ];

    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && !$request->is('api/*') && !$request->ajax()) {
            $path = $request->path();
            $type = $this->detectPageType($path);

            if ($type) {
                $data = [
                    'page'    => $type,
                    'path'    => '/' . $path,
                    'ip'      => $request->ip(),
                    'country' => session('country_code', '?'),
                    'ref'     => $request->headers->get('referer', ''),
                ];

                $user = $request->user();
                if ($user) {
                    $data['user'] = $user->id;
                }

                if ($type === 'tool_page') {
                    // Extract tool name from path
                    $slug = preg_replace('#^[a-z]{2}/#', '', $path);
                    $data['tool'] = $slug;
                }

                $from = session('cameFromAds') ? ' [ads]' : '';
                $gclid = session('gclid') ? ' [gclid]' : '';

                Log::channel('activity')->info(
                    "{$type}{$from}{$gclid}",
                    $data
                );
            }
        }

        return $next($request);
    }

    private function detectPageType(string $path): ?string
    {
        foreach (self::TRACKED_PATTERNS as $type => $pattern) {
            if (preg_match($pattern, $path)) {
                return $type;
            }
        }
        return null;
    }
}
