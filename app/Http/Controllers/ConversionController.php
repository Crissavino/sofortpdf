<?php

namespace App\Http\Controllers;

use App\Models\ConversionLog;
use App\Services\PaywallBypass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConversionController extends Controller
{
    /**
     * POST /api/convert
     *
     * Kicks off an async conversion on the external conversion-service and
     * returns a job_id immediately. The conversion-service processes the
     * work on its own queue and hits back /api/webhooks/conversion when it
     * completes. The browser polls GET /api/convert/status/{id} until the
     * cache entry flips to completed/failed.
     */
    public function convert(Request $request)
    {
        $request->validate([
            'tool' => 'required|string',
            'file_ids' => 'required|array|min:1',
        ]);

        // /api/convert lives outside the localized route group, so SetLocale
        // never runs here. Resolve from session/referer so __() + route() work.
        $locale = $this->resolveLocale($request);
        App::setLocale($locale);

        $tool = $request->input('tool');
        $uploadedFiles = session('upload_files', []);
        $user = $request->user();
        $bypass = PaywallBypass::applies($request);

        if (empty($uploadedFiles)) {
            return response()->json(['message' => __('tool.error_generic')], 422);
        }

        $params = $request->except(['tool', 'file_ids']);
        $filePaths = collect($uploadedFiles)->pluck('path')->toArray();
        $originalNames = collect($uploadedFiles)->pluck('original_name')->toArray();

        // Generate a job_id the browser will poll on and the webhook will key against.
        $jobId = (string) Str::uuid();
        $ttl = now()->addHours((int) config('sofortpdf.guest_download_ttl_hours', 4));

        // Seed the cache so the polling endpoint has an answer before the
        // conversion-service acknowledges the dispatch.
        Cache::put($this->jobKey($jobId), [
            'status' => 'pending',
            'tool' => $tool,
            'started_at' => now()->toIso8601String(),
            'original_filename' => $originalNames[0] ?? null,
            'locale' => $locale,
            'user_id' => $user?->id,
            'bypass' => $bypass,
            'input_paths' => $filePaths,
            'original_names' => $originalNames,
            'output_filename' => $this->generateOutputName($originalNames[0] ?? 'datei', $tool),
        ], $ttl);

        // Session data isn't needed for the async flow anymore.
        session()->forget(['upload_files', 'upload_tool']);

        // Dispatch to the external async worker. This HTTP call should be
        // fast (<1s) — the conversion-service enqueues a Laravel job and
        // returns 202. If it fails, we fall back to the sync path so the
        // user still gets their file eventually.
        try {
            $this->dispatchToConversionService($jobId, $tool, $filePaths, $params, $ttl);
        } catch (\Throwable $e) {
            Log::error('Async dispatch failed, job marked failed', [
                'job_id' => $jobId,
                'tool' => $tool,
                'error' => $e->getMessage(),
            ]);
            Cache::put($this->jobKey($jobId), array_merge(Cache::get($this->jobKey($jobId), []), [
                'status' => 'failed',
                'message' => __('tool.error_generic'),
                'failed_at' => now()->toIso8601String(),
            ]), $ttl);
        }

        return response()->json([
            'job_id' => $jobId,
            'confirmation_url' => route('confirmation', ['locale' => $locale, 't' => $jobId]),
            'message' => __('tool.processing'),
        ]);
    }

    /**
     * GET /api/convert/status/{id}
     * Polled by the confirmation page until the job reports completed / failed.
     */
    public function status(Request $request, string $id)
    {
        App::setLocale($this->resolveLocale($request));
        $entry = Cache::get($this->jobKey($id));
        if (! is_array($entry)) {
            return response()->json(['status' => 'unknown'], 404);
        }

        // Keep the response surface minimal — internal bookkeeping fields
        // shouldn't leak to the browser.
        $publicKeys = ['status', 'tool', 'message', 'download_url', 'download_token',
                       'filename', 'original_filename', 'started_at', 'completed_at', 'failed_at'];
        return response()->json(array_intersect_key($entry, array_flip($publicKeys)));
    }

    /**
     * POST /api/webhooks/conversion
     * Called by the conversion-service when a job finishes (multipart/form-data
     * with `document_id`, `success`, `file`, `output_extension`, optional `error`).
     */
    public function webhook(Request $request)
    {
        $documentId = (string) $request->input('document_id', '');
        $success = (string) $request->input('success', '0');

        if ($documentId === '') {
            return response()->json(['ok' => false, 'message' => 'document_id required'], 422);
        }

        $entry = Cache::get($this->jobKey($documentId));
        if (! is_array($entry)) {
            // Unknown or expired job — swallow silently so cs doesn't retry.
            return response()->json(['ok' => true, 'status' => 'unknown']);
        }

        $ttl = now()->addHours((int) config('sofortpdf.guest_download_ttl_hours', 4));

        if ($success !== '1') {
            $rawError = (string) $request->input('error', '');
            if ($rawError !== '') {
                Log::warning('Conversion callback reported failure', [
                    'job_id' => $documentId,
                    'tool' => $entry['tool'] ?? null,
                    'error' => $rawError,
                ]);
            }

            // Honor the locale the user was on when they kicked off the
            // job — the webhook itself has no locale context (called by
            // the conversion-service over HTTP), so __() would fall back
            // to default (de) and leak German into /en confirmation pages.
            App::setLocale($entry['locale'] ?? config('locales.default', 'de'));

            Cache::put($this->jobKey($documentId), array_merge($entry, [
                'status' => 'failed',
                'message' => __('confirmation.failed_message_fallback'),
                'failed_at' => now()->toIso8601String(),
            ]), $ttl);

            $this->logConversion($entry, 'failed', null, $rawError ?: 'dispatch failed');
            $this->cleanupInputs($entry);
            return response()->json(['ok' => true, 'status' => 'failed']);
        }

        $file = $request->file('file');
        if (! $file) {
            return response()->json(['ok' => false, 'message' => 'file missing'], 422);
        }

        $outputExt = (string) ($request->input('output_extension') ?: $file->getClientOriginalExtension() ?: 'pdf');
        $outputPath = storage_path('app/temp/' . (string) Str::uuid() . '.' . $outputExt);
        if (! is_dir(dirname($outputPath))) {
            @mkdir(dirname($outputPath), 0755, true);
        }
        $file->move(dirname($outputPath), basename($outputPath));

        // Issue a download token using the same DB-or-cache rules as before.
        $user = ! empty($entry['user_id']) ? \App\Models\User::find($entry['user_id']) : null;
        $originalName = $entry['output_filename'] ?? ('converted.' . $outputExt);

        // Honor the stored original extension if it doesn't match the result
        // (e.g. compress preserves input extension; pdf-to-jpg returns zip).
        if (! str_ends_with(strtolower($originalName), '.' . strtolower($outputExt))) {
            $originalName = pathinfo($originalName, PATHINFO_FILENAME) . '.' . $outputExt;
        }

        $tokenInfo = $this->issueDownloadToken($user, $outputPath, $originalName, (bool) ($entry['bypass'] ?? false));

        Cache::put($this->jobKey($documentId), array_merge($entry, [
            'status' => 'completed',
            'download_url' => $tokenInfo['download_url'],
            'download_token' => $tokenInfo['token'],
            'filename' => $originalName,
            'completed_at' => now()->toIso8601String(),
        ]), $ttl);

        $this->logConversion($entry, 'completed', $outputPath, null);
        $this->cleanupInputs($entry);

        return response()->json(['ok' => true, 'status' => 'completed']);
    }

    // ─── Dispatch helpers ─────────────────────────────────────────────────

    /**
     * POST to conversion-service /api/async/convert or /api/async/merge.
     * Tools that don't map to those endpoints fall through to a small
     * synchronous fallback path (none today — every live tool is covered).
     */
    protected function dispatchToConversionService(
        string $jobId,
        string $tool,
        array $filePaths,
        array $params,
        $ttl
    ): void {
        $baseUrl = rtrim((string) config('services.conversion.url', ''), '/');
        $token = (string) config('services.conversion.token', '');
        if ($baseUrl === '') {
            throw new \RuntimeException('conversion service url not configured');
        }

        $callbackUrl = route('api.webhooks.conversion', [], true);
        // Laravel's HTTP client uses Http::attach() for file parts (which
        // flips the body into multipart/form-data); plain scalar fields are
        // passed as the second arg of post() and end up in the same
        // multipart body. Mixing the two was the bug that made scalar
        // params silently vanish.
        $client = Http::withToken($token)->timeout(20); // ACK only, not result

        // merge + multi-image tools use /api/async/merge (the service
        // already handles heterogeneous inputs internally).
        $useMerge = in_array($tool, ['merge', 'jpg-to-pdf', 'png-to-pdf'], true);

        if ($useMerge) {
            foreach ($filePaths as $i => $path) {
                $client = $client->attach("files[{$i}]", file_get_contents($path), basename($path));
            }
            // Merge defaults to CloudConvert (tested + known good).
            $response = $client->post("{$baseUrl}/api/async/merge", [
                'callback_url' => $callbackUrl,
                'document_id' => $jobId,
            ]);
            if (! $response->successful()) {
                throw new \RuntimeException('async merge dispatch failed: ' . $response->status() . ' ' . $response->body());
            }
            return;
        }

        $operation = $this->toolToOperation($tool);
        if ($operation === null) {
            throw new \RuntimeException("no async operation mapping for tool '{$tool}'");
        }

        // File part stays on attach(); everything else rides in the fields array.
        $client = $client->attach('file', file_get_contents($filePaths[0]), basename($filePaths[0]));

        $fields = [
            'operation' => $operation,
            'callback_url' => $callbackUrl,
            'document_id' => $jobId,
        ];

        // CloudConvert only covers format-conversion + compress. PDF-
        // manipulation and OCR operations have no CC equivalent — force
        // them onto the local (Gotenberg/LibreOffice/OCRmyPDF) path. CC
        // will silently re-export the input file unchanged when sent an
        // operation it doesn't recognise, which surfaces here as a
        // mislabelled file (e.g. PDF bytes saved as .zip for split).
        $localOnlyOps = [
            'remove-pages', 'extract-pages',
            'split', 'split-by-ranges', 'split-every-page',
            'rotate', 'unlock', 'watermark', 'optimize',
            'ocr-pdf', 'ocr-image', 'html-to-pdf', 'url-to-pdf',
        ];
        if (in_array($operation, $localOnlyOps, true)) {
            $fields['engine'] = 'local';
        }

        // For the split tool, the picker submits ranges in the `pages`
        // field as a comma-separated string ("1-3,4-5,6-8"). cs's
        // split-by-ranges operation wants those as a JSON array, so
        // translate before forwarding.
        if ($tool === 'split' && isset($params['pages']) && $params['pages'] !== '') {
            $rangeArray = array_values(array_filter(array_map('trim', explode(',', (string) $params['pages']))));
            if (! empty($rangeArray)) {
                $params['ranges'] = json_encode($rangeArray);
            }
            unset($params['pages']);
        }

        // Forward tool params the cs convertAsync validator allows:
        // quality, password, pages, ranges, text, fontSize, opacity, angle,
        // language, output_extension, rotations.
        foreach (['quality', 'password', 'pages', 'ranges', 'text', 'fontSize',
                  'opacity', 'angle', 'language', 'output_extension', 'rotations'] as $k) {
            if (isset($params[$k]) && $params[$k] !== '' && $params[$k] !== null) {
                $fields[$k] = (string) $params[$k];
            }
        }

        $response = $client->post("{$baseUrl}/api/async/convert", $fields);
        if (! $response->successful()) {
            throw new \RuntimeException('async convert dispatch failed: ' . $response->status() . ' ' . $response->body());
        }
    }

    /**
     * Map our tool keys to the operation strings the conversion-service accepts.
     * Returns null when there's no async path (caller falls back).
     */
    protected function toolToOperation(string $tool): ?string
    {
        return match ($tool) {
            'compress'      => 'compress',
            'pdf-to-word'   => 'pdf-to-word',
            'pdf-to-excel'  => 'pdf-to-excel',
            'pdf-to-ppt'    => 'pdf-to-powerpoint',
            'pdf-to-jpg'    => 'pdf-to-jpg',
            'pdf-to-png'    => 'pdf-to-png',
            'word-to-pdf',
            'excel-to-pdf',
            'ppt-to-pdf'    => 'office-to-pdf',
            'rotate'        => 'rotate',
            'unlock'        => 'unlock',
            'watermark'     => 'watermark',
            'ocr'           => 'ocr-pdf',
            'remove-pages'  => 'remove-pages',
            'extract-pages' => 'extract-pages',
            // Split with multiple ranges → ZIP of separate PDFs (the picker
            // only emits a value when there are 2+ groups). cs's
            // split-by-ranges expects a JSON array in `ranges`; we
            // synthesise that from the comma-separated `pages` string in
            // dispatchToConversionService below.
            'split'         => 'split-by-ranges',
            'optimize'      => 'optimize',
            'html-to-pdf'   => 'html-to-pdf',
            default         => null,
        };
    }

    // ─── Shared utilities ─────────────────────────────────────────────────

    private function jobKey(string $id): string
    {
        return 'conversion_job:' . $id;
    }

    private function logConversion(array $entry, string $status, ?string $resultPath, ?string $errorMessage): void
    {
        try {
            ConversionLog::create([
                'customer_id' => $entry['user_id'] ?? null,
                'tool_slug' => 'sofortpdf_' . ($entry['tool'] ?? 'unknown'),
                'original_filename' => $entry['original_filename'] ?? ($entry['original_names'][0] ?? 'unknown'),
                'result_filename' => $resultPath ? basename($resultPath) : '',
                'status' => $status,
                'error_message' => $errorMessage,
                'file_size' => ($resultPath && file_exists($resultPath)) ? filesize($resultPath) : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('ConversionLog write failed', ['error' => $e->getMessage()]);
        }
    }

    private function cleanupInputs(array $entry): void
    {
        foreach ((array) ($entry['input_paths'] ?? []) as $p) {
            @unlink($p);
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
     * so we don't depend on the shared customer_id FK.
     *
     * @return array{token: string, download_url: string}
     */
    private function issueDownloadToken($user, string $primaryPath, string $originalName, bool $bypass): array
    {
        if ($user && ! $bypass) {
            try {
                $download = \App\Models\Download::createToken($user->id, $primaryPath, $originalName);
                return [
                    'token' => $download->token,
                    'download_url' => route('download', $download->token),
                ];
            } catch (\Throwable $e) {
                Log::warning('Download row insert failed, falling back to cache', ['error' => $e->getMessage()]);
            }
        }

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
            'merge' => "{$base}_merged.pdf",
            'compress' => "{$base}_compressed.pdf",
            'jpg-to-pdf' => "{$base}.pdf",
            'pdf-to-word' => "{$base}.docx",
            'word-to-pdf' => "{$base}.pdf",
            'pdf-to-jpg' => "{$base}.jpg",
            'split' => "{$base}_split.pdf",
            'pdf-to-excel' => "{$base}.xlsx",
            'excel-to-pdf' => "{$base}.pdf",
            'rotate' => "{$base}_rotated.pdf",
            'protect' => "{$base}_protected.pdf",
            'unlock' => "{$base}_unlocked.pdf",
            'watermark' => "{$base}_watermarked.pdf",
            'pdf-to-ppt' => "{$base}.pptx",
            'ppt-to-pdf' => "{$base}.pdf",
            'pdf-to-png' => "{$base}.png",
            'png-to-pdf' => "{$base}.pdf",
            'ocr' => "{$base}_ocr.pdf",
            'remove-pages' => "{$base}_cleaned.pdf",
            'extract-pages' => "{$base}_extracted.pdf",
            'html-to-pdf' => "{$base}.pdf",
            'optimize' => "{$base}_optimized.pdf",
            default => "{$base}_converted.pdf",
        };
    }
}
