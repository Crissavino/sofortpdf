<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

        if ($token !== '') {
            // Cache-backed token (guest / payment-bypass flow)
            $cached = Cache::get('guest_download:' . $token);
            if (is_array($cached)) {
                $filename = $cached['original_filename'] ?? null;
                $tokenValid = true;
            } else {
                // DB-backed token (authenticated flow)
                $row = Download::where('token', $token)->first();
                if ($row && ! $row->isExpired()) {
                    $filename = $row->original_filename;
                    $tokenValid = true;
                }
            }

            if ($tokenValid) {
                $downloadUrl = route('download', $token);
            }
        }

        return view('confirmation', [
            'token' => $token,
            'filename' => $filename,
            'downloadUrl' => $downloadUrl,
            'tokenValid' => $tokenValid,
            'paymentSuccess' => $paymentSuccess,
            'pageTitle' => __('confirmation.meta_title'),
            'metaDescription' => __('confirmation.meta_description'),
            'slug' => 'confirmation',
        ]);
    }
}
