<?php

namespace App\Http\Controllers;

use App\Exceptions\ConversionServiceException;
use App\Models\ConversionLog;
use App\Models\Download;
use App\Services\ConversionServiceClient;
use App\Services\PaywallBypass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConversionController extends Controller
{
    public function convert(Request $request, ConversionServiceClient $client)
    {
        $request->validate([
            'tool' => 'required|string',
            'file_ids' => 'required|array|min:1',
        ]);

        // /api/convert lives outside the localized route group, so SetLocale
        // never runs here. Pull the locale the user picked from session and
        // apply it manually — otherwise __() falls back to German and
        // route() complains about the missing {locale} parameter.
        $locale = $this->resolveLocale($request);
        App::setLocale($locale);

        $tool = $request->input('tool');
        $uploadedFiles = session('upload_files', []);
        $user = $request->user();
        $bypass = PaywallBypass::applies($request);

        if (empty($uploadedFiles)) {
            return response()->json(['message' => 'Keine Dateien gefunden. Bitte laden Sie erneut hoch.'], 422);
        }

        $filePaths = collect($uploadedFiles)->pluck('path')->toArray();
        $originalNames = collect($uploadedFiles)->pluck('original_name')->toArray();
        $startTime = microtime(true);

        try {
            $resultPath = match ($tool) {
                'merge' => $client->merge($filePaths),
                'compress' => $client->compress($filePaths[0]),
                'jpg-to-pdf' => $client->jpgToPdf($filePaths),
                'pdf-to-word' => $client->convert($filePaths[0], 'pdf', 'docx'),
                'word-to-pdf' => $client->convert($filePaths[0], 'docx', 'pdf'),
                'pdf-to-jpg' => $client->pdfToJpg($filePaths[0]),
                'split' => $client->split($filePaths[0], $request->input('pages', [])),
                'pdf-to-excel' => $client->convert($filePaths[0], 'pdf', 'xlsx'),
                'excel-to-pdf' => $client->convert($filePaths[0], 'xlsx', 'pdf'),
                'rotate' => $client->rotate($filePaths[0], (int) $request->input('angle', 90)),
                'protect' => $client->protect($filePaths[0], $request->input('password', '')),
                'unlock' => $client->unlock($filePaths[0], $request->input('password', '')),
                'watermark' => $client->watermark(
                    $filePaths[0],
                    $request->input('text', 'WATERMARK'),
                    (float) $request->input('opacity', 0.5),
                    (int) $request->input('fontSize', 48),
                    (int) $request->input('angle', 45)
                ),
                'pdf-to-ppt' => $client->pdfToPpt($filePaths[0]),
                'ppt-to-pdf' => $client->officeToPdf($filePaths[0]),
                'pdf-to-png' => $client->pdfToPng($filePaths[0]),
                'png-to-pdf' => $client->pngToPdf($filePaths),
                'ocr' => $client->ocrPdf($filePaths[0], $request->input('language', 'deu+eng')),
                'remove-pages' => $client->removePages($filePaths[0], $request->input('pages', '')),
                'extract-pages' => $client->extractPages($filePaths[0], $request->input('pages', '')),
                'html-to-pdf' => $client->htmlToPdf($request->input('html', '')),
                'optimize' => $client->optimize($filePaths[0]),
                default => throw ConversionServiceException::conversionFailed('Unbekanntes Tool.'),
            };

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            // Handle array results (split, pdf-to-jpg)
            $isArray = is_array($resultPath);
            $primaryPath = $isArray ? $resultPath[0] : $resultPath;
            $originalName = $this->generateOutputName($originalNames[0] ?? 'datei', $tool);

            // Log conversion (best-effort; never block the response)
            try {
                ConversionLog::create([
                    'customer_id' => $user?->id,
                    'tool_slug' => "sofortpdf_{$tool}",
                    'original_filename' => $originalNames[0] ?? 'unknown',
                    'result_filename' => basename($primaryPath),
                    'status' => 'completed',
                    'file_size' => file_exists($primaryPath) ? filesize($primaryPath) : 0,
                    'processing_time_ms' => $processingTime,
                ]);
            } catch (\Throwable $e) {
                Log::warning('ConversionLog write failed', ['error' => $e->getMessage()]);
            }

            // Issue a download token. With a logged-in user we use the
            // downloads table; in payment-bypass mode (or guest) we keep
            // the mapping in cache to avoid the customer_id FK in the
            // shared database.
            $tokenInfo = $this->issueDownloadToken($user, $primaryPath, $originalName, $bypass);

            // Clear session — but read the upload_tool first so we don't
            // strip context the rest of the response might want later.
            session()->forget(['upload_files', 'upload_tool']);

            return response()->json([
                'download_url' => $tokenInfo['download_url'],
                'confirmation_url' => route('confirmation', ['locale' => $locale, 't' => $tokenInfo['token']]),
                'filename' => $originalName,
                'message' => __('tool.ready_for_download'),
            ]);

        } catch (ConversionServiceException $e) {
            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            try {
                ConversionLog::create([
                    'customer_id' => $user?->id,
                    'tool_slug' => "sofortpdf_{$tool}",
                    'original_filename' => $originalNames[0] ?? 'unknown',
                    'result_filename' => '',
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'processing_time_ms' => $processingTime,
                ]);
            } catch (\Throwable $logErr) {
                Log::warning('ConversionLog write failed', ['error' => $logErr->getMessage()]);
            }

            return response()->json(['message' => $e->getMessage()], 500);

        } catch (\Exception $e) {
            Log::error("Conversion failed: {$e->getMessage()}", ['tool' => $tool, 'user' => $user?->id]);
            return response()->json(['message' => __('tool.error_generic')], 500);
        }
    }

    /**
     * Best-effort locale detection for API requests that bypass SetLocale.
     * Order: session → Referer URL → default. Always returns a supported locale.
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

    /**
     * Issue a single-use download token. Falls back to a cache-backed
     * mapping when there's no authenticated user (guest / bypass mode)
     * so we don't need to write a downloads row keyed on customer_id.
     *
     * @return array{token: string, download_url: string}
     */
    private function issueDownloadToken($user, string $primaryPath, string $originalName, bool $bypass): array
    {
        // Logged-in user, normal flow
        if ($user && ! $bypass) {
            try {
                $download = Download::createToken($user->id, $primaryPath, $originalName);
                return [
                    'token' => $download->token,
                    'download_url' => route('download', $download->token),
                ];
            } catch (\Throwable $e) {
                Log::warning('Download row insert failed, falling back to cache', ['error' => $e->getMessage()]);
            }
        }

        // Guest / bypass / DB write failure → cache token
        $token = Str::random(64);
        $ttlHours = (int) config('sofortpdf.guest_download_ttl_hours', 4);
        Cache::put('guest_download:' . $token, [
            'file_path' => $primaryPath,
            'original_filename' => $originalName,
            'expires_at' => now()->addHours($ttlHours)->toIso8601String(),
        ], now()->addHours($ttlHours));

        return [
            'token' => $token,
            'download_url' => route('download', $token),
        ];
    }

    private function generateOutputName(string $originalName, string $tool): string
    {
        $base = pathinfo($originalName, PATHINFO_FILENAME);
        return match ($tool) {
            'merge' => "{$base}_zusammengefuegt.pdf",
            'compress' => "{$base}_komprimiert.pdf",
            'jpg-to-pdf' => "{$base}.pdf",
            'pdf-to-word' => "{$base}.docx",
            'word-to-pdf' => "{$base}.pdf",
            'pdf-to-jpg' => "{$base}.jpg",
            'split' => "{$base}_getrennt.pdf",
            'pdf-to-excel' => "{$base}.xlsx",
            'excel-to-pdf' => "{$base}.pdf",
            'rotate' => "{$base}_gedreht.pdf",
            'protect' => "{$base}_geschuetzt.pdf",
            'unlock' => "{$base}_entsperrt.pdf",
            'watermark' => "{$base}_wasserzeichen.pdf",
            'pdf-to-ppt' => "{$base}.pptx",
            'ppt-to-pdf' => "{$base}.pdf",
            'pdf-to-png' => "{$base}.png",
            'png-to-pdf' => "{$base}.pdf",
            'ocr' => "{$base}_ocr.pdf",
            'remove-pages' => "{$base}_bereinigt.pdf",
            'extract-pages' => "{$base}_extrakt.pdf",
            'html-to-pdf' => "{$base}.pdf",
            'optimize' => "{$base}_optimiert.pdf",
            default => "{$base}_converted.pdf",
        };
    }
}
