<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class ConfirmationController extends Controller
{
    /**
     * GET /{locale}/confirmation?t=<download_token>[&cGF5bWVudFN1Y2Nlc3M=]
     *
     * Shown after every successful tool conversion. The optional
     * cGF5bWVudFN1Y2Nlc3M= flag (base64 of "paymentSuccess") is added
     * by CheckoutController::success only the first time a user pays
     * for the trial — UI shows an extra confirmation message in that case.
     */
    public function show(Request $request)
    {
        $token = (string) $request->query('t', '');
        $paymentSuccess = $request->query->has('cGF5bWVudFN1Y2Nlc3M=');

        $filename = null;
        $downloadUrl = null;
        $tokenValid = false;
        $jobState = 'unknown';   // ready | processing | failed | unknown
        $jobErrorMessage = null;
        $statusUrl = null;

        if ($token !== '') {
            // 1) Is this a live conversion job? (takes priority — the job
            // cache entry also lives when a download is already issued, and
            // we want consistent state reporting.)
            $jobEntry = Cache::get('conversion_job:' . $token);
            if (is_array($jobEntry)) {
                $statusUrl = URL::to('/api/convert/status/' . $token);
                if ($jobEntry['status'] === 'completed') {
                    $filename = $jobEntry['filename'] ?? null;
                    $downloadUrl = $jobEntry['download_url'] ?? null;
                    $tokenValid = $downloadUrl !== null;
                    $jobState = 'ready';
                } elseif ($jobEntry['status'] === 'failed') {
                    $jobState = 'failed';
                    $jobErrorMessage = $jobEntry['message'] ?? null;
                    $filename = $jobEntry['original_filename'] ?? null;
                } else {
                    $jobState = 'processing';
                    $filename = $jobEntry['original_filename'] ?? null;
                }
            } else {
                // 2) Direct download token (guest cache or DB) — fallback
                // for paths that bypass the async pipeline (signatures,
                // legacy flows).
                $cached = Cache::get('guest_download:' . $token);
                if (is_array($cached)) {
                    $filename = $cached['original_filename'] ?? null;
                    $tokenValid = true;
                } else {
                    $row = Download::where('token', $token)->first();
                    if ($row && ! $row->isExpired()) {
                        $filename = $row->original_filename;
                        $tokenValid = true;
                    }
                }
                if ($tokenValid) {
                    $downloadUrl = route('download', $token);
                    $jobState = 'ready';
                }
            }
        }

        return view('confirmation', [
            'token' => $token,
            'filename' => $filename,
            'downloadUrl' => $downloadUrl,
            'tokenValid' => $tokenValid,
            'paymentSuccess' => $paymentSuccess,
            'jobState' => $jobState,
            'jobErrorMessage' => $jobErrorMessage,
            'statusUrl' => $statusUrl,
            'pageTitle' => __('confirmation.meta_title'),
            'metaDescription' => __('confirmation.meta_description'),
            'slug' => 'confirmation',
        ]);
    }
}
