<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DownloadController extends Controller
{
    public function download(Request $request, string $token)
    {
        // 1) Cache-backed guest token (payment-bypass / guest issuance)
        $cached = Cache::get('guest_download:' . $token);
        if (is_array($cached)) {
            $expiresAt = isset($cached['expires_at']) ? Carbon::parse($cached['expires_at']) : null;
            if ($expiresAt && $expiresAt->isPast()) {
                Cache::forget('guest_download:' . $token);
                abort(410, 'Dieser Download-Link ist abgelaufen.');
            }

            $filePath = $cached['file_path'] ?? null;
            $originalFilename = $cached['original_filename'] ?? 'download';

            if (! $filePath || ! file_exists($filePath)) {
                abort(404, 'Datei nicht mehr verfügbar.');
            }

            return response()->download($filePath, $originalFilename);
        }

        // 2) DB-backed token (authenticated flow)
        $query = Download::where('token', $token);
        if ($user = $request->user()) {
            $query->where('customer_id', $user->id);
        }
        $download = $query->first();

        if (! $download) {
            abort(404, 'Download nicht gefunden.');
        }

        if ($download->isExpired()) {
            abort(410, 'Dieser Download-Link ist abgelaufen.');
        }

        $filePath = $download->file_path;

        if (! file_exists($filePath)) {
            abort(404, 'Datei nicht mehr verfügbar.');
        }

        return response()->download($filePath, $download->original_filename);
    }
}
