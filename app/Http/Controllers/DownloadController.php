<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DownloadController extends Controller
{
    /**
     * Download tokens are stored in the cache for both guest and
     * authenticated flows (the local downloads table doesn't exist on the
     * shared DB).
     */
    public function download(Request $request, string $token)
    {
        $cached = Cache::get('guest_download:' . $token);
        if (!is_array($cached)) {
            abort(404, 'Download nicht gefunden.');
        }

        $expiresAt = isset($cached['expires_at']) ? Carbon::parse($cached['expires_at']) : null;
        if ($expiresAt && $expiresAt->isPast()) {
            Cache::forget('guest_download:' . $token);
            abort(410, 'Dieser Download-Link ist abgelaufen.');
        }

        $filePath         = $cached['file_path'] ?? null;
        $originalFilename = $cached['original_filename'] ?? 'download';

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'Datei nicht mehr verfügbar.');
        }

        return response()->download($filePath, $originalFilename);
    }
}
