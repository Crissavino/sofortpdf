<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class ConfirmationController extends Controller
{
    /**
     * GET /{locale}/confirmation[?cGF5bWVudFN1Y2Nlc3M=]
     *
     * Shown after every successful tool conversion. The job_id is read
     * from the session (set by ConversionController) so it doesn't need
     * to be in the URL. Legacy ?t= param is still supported as fallback.
     *
     * The optional cGF5bWVudFN1Y2Nlc3M= flag (base64 of "paymentSuccess")
     * is present when the user just completed a trial payment.
     */
    public function show(Request $request)
    {
        // Job ID: session first, URL fallback for legacy/direct links
        $token = (string) ($request->query('t', '') ?: session('last_job_id', ''));
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
                // 2) Direct download token (cache only — the local
                // downloads table doesn't exist on the shared DB).
                $cached = Cache::get('guest_download:' . $token);
                if (is_array($cached)) {
                    $filename = $cached['original_filename'] ?? null;
                    $tokenValid = true;
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
