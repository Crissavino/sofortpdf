<?php

namespace App\Http\Controllers;

use App\Services\PaywallBypass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    public function sign(Request $request)
    {
        // /api/sign sits outside the localized route group → set locale
        // manually so __() and route('confirmation') work correctly.
        $locale = $this->resolveLocale($request);
        App::setLocale($locale);

        $request->validate([
            'pdf_base64' => 'required|string',
            'signature_png_base64' => 'required|string',
            'positions' => 'required|array|min:1',
            'positions.*.page' => 'required|integer|min:1',
            'positions.*.x' => 'required|numeric',
            'positions.*.y' => 'required|numeric',
            'positions.*.width' => 'required|numeric',
            'positions.*.height' => 'required|numeric',
        ]);

        // Decode PDF base64 to temp file
        $pdfData = base64_decode($request->input('pdf_base64'));
        $pdfUuid = Str::uuid();
        $pdfPath = storage_path("app/temp/{$pdfUuid}.pdf");
        file_put_contents($pdfPath, $pdfData);

        // Decode signature PNG base64 to temp file
        $sigData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('signature_png_base64')));
        $sigPath = storage_path("app/temp/{$pdfUuid}_sig.png");
        file_put_contents($sigPath, $sigData);

        // Apply signature using FPDI
        $outputPath = $this->applySignature($pdfPath, $sigPath, $request->input('positions'));

        $user = $request->user();
        $bypass = PaywallBypass::applies($request);
        $originalName = 'unterschrieben.pdf';

        // Log conversion (best-effort)
        try {
            \App\Models\ConversionLog::create([
                'customer_id' => $user?->id,
                'tool_slug' => 'sofortpdf_sign',
                'original_filename' => 'document.pdf',
                'result_filename' => basename($outputPath),
                'status' => 'completed',
                'file_size' => filesize($outputPath),
            ]);
        } catch (\Throwable $e) {
            Log::warning('ConversionLog write failed (sign)', ['error' => $e->getMessage()]);
        }

        // Issue download token (DB for logged-in user, cache otherwise)
        $token = null;
        if ($user && ! $bypass) {
            try {
                $download = \App\Models\Download::createToken($user->id, $outputPath, $originalName);
                $token = $download->token;
            } catch (\Throwable $e) {
                Log::warning('Sign Download row insert failed, falling back to cache', ['error' => $e->getMessage()]);
            }
        }
        if (! $token) {
            $token = Str::random(64);
            $ttlHours = (int) config('sofortpdf.guest_download_ttl_hours', 4);
            Cache::put('guest_download:' . $token, [
                'file_path' => $outputPath,
                'original_filename' => $originalName,
                'expires_at' => now()->addHours($ttlHours)->toIso8601String(),
            ], now()->addHours($ttlHours));
        }

        // Clean up temp signature
        @unlink($sigPath);

        return response()->json([
            'download_url' => route('download', $token),
            'confirmation_url' => route('confirmation', ['locale' => $locale, 't' => $token]),
            'message' => __('tool.ready_for_download'),
        ]);
    }

    /**
     * Best-effort locale detection (same logic as ConversionController).
     */
    private function resolveLocale(Request $request): string
    {
        $supported = (array) config('locales.supported', ['de', 'en']);
        $default = (string) config('locales.default', 'de');

        $candidate = (string) session('locale', '');
        if (in_array($candidate, $supported, true)) {
            return $candidate;
        }

        $referer = $request->headers->get('referer') ?: '';
        if ($referer !== '') {
            $path = parse_url($referer, PHP_URL_PATH) ?: '';
            $first = trim(explode('/', ltrim($path, '/'))[0] ?? '', '/');
            if (in_array($first, $supported, true)) {
                return $first;
            }
        }

        return $default;
    }

    private function applySignature(string $pdfPath, string $sigPath, array $positions): string
    {
        // Use FPDI to import the original PDF and overlay signature images
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($pdfPath);

        // Group positions by page
        $positionsByPage = collect($positions)->groupBy('page');

        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // If this page has signatures, overlay them
            if ($positionsByPage->has($i)) {
                foreach ($positionsByPage->get($i) as $pos) {
                    // Positions are in percentage of page size, convert to mm
                    $x = ($pos['x'] / 100) * $size['width'];
                    $y = ($pos['y'] / 100) * $size['height'];
                    $w = ($pos['width'] / 100) * $size['width'];
                    $h = ($pos['height'] / 100) * $size['height'];

                    $pdf->Image($sigPath, $x, $y, $w, $h, 'PNG');
                }
            }
        }

        $outputUuid = Str::uuid();
        $outputPath = storage_path("app/temp/{$outputUuid}_signed.pdf");
        $pdf->Output('F', $outputPath);

        return $outputPath;
    }
}
