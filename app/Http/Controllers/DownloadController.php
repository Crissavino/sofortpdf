<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function download(Request $request, string $token)
    {
        $download = Download::where('token', $token)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$download) {
            abort(404, 'Download nicht gefunden.');
        }

        if ($download->isExpired()) {
            abort(410, 'Dieser Download-Link ist abgelaufen.');
        }

        $filePath = $download->file_path;

        if (!file_exists($filePath)) {
            abort(404, 'Datei nicht mehr verfügbar.');
        }

        return response()->download($filePath, $download->original_filename);
    }
}
