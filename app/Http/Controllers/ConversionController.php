<?php

namespace App\Http\Controllers;

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

        // Smart tool swap: if the uploaded file doesn't match the selected
        // tool, auto-switch to the correct one. E.g., user is on "PDF to Word"
        // but uploads a .docx → swap to "Word to PDF" silently.
        $originalTool = $tool;
        $tool = $this->autoDetectTool($tool, $originalNames);

        // If tool was swapped, log it for analytics
        if ($tool !== $originalTool) {
            Log::info("Tool auto-swapped for user experience", [
                'requested' => $originalTool,
                'actual'    => $tool,
                'file'      => $originalNames[0] ?? null,
            ]);
        }

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

        // Store in session so the confirmation page can pick it up without
        // needing the job_id in the URL (cleaner URLs, better for GTM).
        session(['last_job_id' => $jobId]);

        return response()->json([
            'job_id' => $jobId,
            'confirmation_url' => route('confirmation', ['locale' => $locale]),
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

        // Paid users: store in permanent directory. Guest/bypass: temp (cleaned hourly).
        $isPaid = !empty($entry['user_id']);
        $dir    = $isPaid ? 'app/documents' : 'app/temp';
        $outputPath = storage_path($dir . '/' . (string) Str::uuid() . '.' . $outputExt);
        if (! is_dir(dirname($outputPath))) {
            @mkdir(dirname($outputPath), 0755, true);
        }
        $file->move(dirname($outputPath), basename($outputPath));

        // Detect actual file type via magic bytes — the CS may report
        // output_extension=jpg but deliver a ZIP (multi-page pdf-to-jpg).
        if (file_exists($outputPath)) {
            $magic = file_get_contents($outputPath, false, null, 0, 4);
            if ($magic === "PK\x03\x04" && strtolower($outputExt) !== 'zip') {
                $outputExt = 'zip';
                $newPath = preg_replace('/\.[^.]+$/', '.zip', $outputPath);
                rename($outputPath, $newPath);
                $outputPath = $newPath;
            }
        }

        // Issue a download token using the same DB-or-cache rules as before.
        $user = ! empty($entry['user_id']) ? \App\Models\Customer::find($entry['user_id']) : null;
        $originalName = $entry['output_filename'] ?? ('converted.' . $outputExt);

        // Honor the stored original extension if it doesn't match the result.
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

        // Send download-ready email (same as conversie-pdf's sendMailDownload)
        if ($user) {
            $locale = $entry['locale'] ?? 'de';
            $downloadUrl = url($tokenInfo['download_url']);
            $tool = $entry['tool'] ?? 'convert';
            app(\App\Services\EmailService::class)->sendDownloadReady($user, $originalName, $downloadUrl, $tool, $locale);
        }

        \Illuminate\Support\Facades\Log::channel('activity')->info('conversion_complete', [
            'tool'     => $entry['tool'] ?? 'unknown',
            'filename' => $originalName,
            'user_id'  => $entry['user_id'] ?? null,
        ]);

        // Save document to the shared `documents` table (same as conversie-pdf)
        $this->saveDocument($entry, $originalName, $outputExt, $outputPath);

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
        $useMerge = $tool === 'merge';

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

        // Set output_extension so the CS saves with the right extension
        // and the webhook receives the correct file type.
        $toolExtensions = [
            'pdf-to-word' => 'docx', 'pdf-to-excel' => 'xlsx', 'pdf-to-powerpoint' => 'pptx',
            'pdf-to-jpg' => 'jpg', 'pdf-to-png' => 'png',
            'office-to-pdf' => 'pdf', 'compress' => 'pdf', 'rotate' => 'pdf',
            'unlock' => 'pdf', 'watermark' => 'pdf', 'optimize' => 'pdf',
            'ocr-pdf' => 'pdf', 'remove-pages' => 'pdf', 'extract-pages' => 'pdf',
            'split-by-ranges' => 'zip',
        ];
        if (!isset($params['output_extension']) && isset($toolExtensions[$operation])) {
            $params['output_extension'] = $toolExtensions[$operation];
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
    /**
     * Auto-detect the correct tool based on the uploaded file extension.
     * If the user is on "PDF to Word" but uploads a .docx, silently swap
     * to "Word to PDF". Works for all conversion pairs.
     */
    /**
     * Map tool slug → pdf_services.id (same IDs as conversie-pdf).
     */
    protected function toolToPdfServiceId(string $tool): int
    {
        return match ($tool) {
            'word-to-pdf'   => 1,
            'pdf-to-word'   => 2,
            'excel-to-pdf'  => 3,
            'pdf-to-excel'  => 4,
            'ppt-to-pdf'    => 5,
            'pdf-to-ppt'    => 6,
            'image-to-pdf'  => 7,
            'jpg-to-pdf'    => 7,
            'pdf-to-jpg'    => 8,
            'png-to-pdf'    => 9,
            'pdf-to-png'    => 10,
            'merge'         => 11,
            'split'         => 12,
            'edit'          => 13,
            'sign'          => 14,
            'compress'      => 15,
            'protect'       => 22,
            'unlock'        => 23,
            default         => 1,
        };
    }

    protected function autoDetectTool(string $requestedTool, array $filenames): string
    {
        if (empty($filenames)) {
            return $requestedTool;
        }

        $ext = strtolower(pathinfo($filenames[0], PATHINFO_EXTENSION));

        // Map of: tool → expected input extension(s)
        $expectedInputs = [
            'pdf-to-word'  => ['pdf'],
            'pdf-to-excel' => ['pdf'],
            'pdf-to-ppt'   => ['pdf'],
            'pdf-to-jpg'   => ['pdf'],
            'pdf-to-png'   => ['pdf'],
            'word-to-pdf'  => ['doc', 'docx'],
            'excel-to-pdf' => ['xls', 'xlsx'],
            'ppt-to-pdf'   => ['ppt', 'pptx'],
            'jpg-to-pdf'   => ['jpg', 'jpeg'],
            'png-to-pdf'   => ['png'],
            'compress'     => ['pdf'],
            'merge'        => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xls', 'xlsx', 'ppt', 'pptx'],
            'rotate'       => ['pdf'],
            'split'        => ['pdf'],
            'sign'         => ['pdf'],
            'unlock'       => ['pdf'],
            'watermark'    => ['pdf'],
            'ocr'          => ['pdf', 'jpg', 'jpeg', 'png'],
            'remove-pages' => ['pdf'],
            'extract-pages'=> ['pdf'],
            'optimize'     => ['pdf'],
        ];

        // Reverse map: extension → what tool converts it TO PDF (or from PDF)
        $extToTool = [
            'doc'  => 'word-to-pdf',
            'docx' => 'word-to-pdf',
            'xls'  => 'excel-to-pdf',
            'xlsx' => 'excel-to-pdf',
            'ppt'  => 'ppt-to-pdf',
            'pptx' => 'ppt-to-pdf',
            'jpg'  => 'jpg-to-pdf',
            'jpeg' => 'jpg-to-pdf',
            'png'  => 'png-to-pdf',
        ];

        // Reverse: PDF uploaded on a "X-to-PDF" tool → swap to "PDF-to-X"
        $pdfSwaps = [
            'word-to-pdf'  => 'pdf-to-word',
            'excel-to-pdf' => 'pdf-to-excel',
            'ppt-to-pdf'   => 'pdf-to-ppt',
            'jpg-to-pdf'   => 'pdf-to-jpg',
            'png-to-pdf'   => 'pdf-to-png',
        ];

        $expected = $expectedInputs[$requestedTool] ?? null;

        // If the file matches the expected input, no swap needed
        if ($expected && in_array($ext, $expected, true)) {
            return $requestedTool;
        }

        // Case 1: user is on "PDF to Word" but uploaded a .docx → swap to "Word to PDF"
        if (str_starts_with($requestedTool, 'pdf-to-') && isset($extToTool[$ext])) {
            Log::info("Auto-swap tool: {$requestedTool} → {$extToTool[$ext]} (uploaded .{$ext})");
            return $extToTool[$ext];
        }

        // Case 2: user is on "Word to PDF" but uploaded a .pdf → swap to "PDF to Word"
        if ($ext === 'pdf' && isset($pdfSwaps[$requestedTool])) {
            Log::info("Auto-swap tool: {$requestedTool} → {$pdfSwaps[$requestedTool]} (uploaded .pdf)");
            return $pdfSwaps[$requestedTool];
        }

        // Case 3: user is on compress/merge/etc but uploaded a non-PDF → convert to PDF first
        if ($ext !== 'pdf' && isset($extToTool[$ext]) && in_array($requestedTool, ['compress', 'rotate', 'split', 'unlock', 'watermark', 'remove-pages', 'extract-pages', 'optimize'])) {
            Log::info("Auto-swap tool: {$requestedTool} → {$extToTool[$ext]} (uploaded .{$ext} on PDF-only tool)");
            return $extToTool[$ext];
        }

        return $requestedTool;
    }

    protected function toolToOperation(string $tool): ?string
    {
        return match ($tool) {
            'compress'      => 'compress',
            'pdf-to-word'   => 'pdf-to-word',
            'pdf-to-excel'  => 'pdf-to-excel',
            'pdf-to-ppt'    => 'pdf-to-powerpoint',
            'pdf-to-jpg'    => 'pdf-to-jpg',
            'pdf-to-png'    => 'pdf-to-png',
            'image-to-pdf'  => 'image-to-pdf',
            'jpg-to-pdf'    => 'image-to-pdf',
            'png-to-pdf'    => 'image-to-pdf',
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

    private function saveDocument(array $entry, string $originalName, string $outputExt, string $outputPath): void
    {
        try {
            $srcName = $entry['original_filename'] ?? $entry['original_names'][0] ?? 'unknown';
            $srcExt  = pathinfo($srcName, PATHINFO_EXTENSION) ?: 'pdf';
            $tool    = $entry['tool'] ?? 'convert';

            // Update pdf_service_id on customer (same as conversie-pdf)
            $customerId = $entry['user_id'] ?? null;
            if ($customerId) {
                $customer = \App\Models\Customer::find($customerId);
                if ($customer && !$customer->pdf_service_id) {
                    $customer->pdf_service_id = $this->toolToPdfServiceId($tool);
                    $customer->save();
                }
            }

            \App\Models\Document::create([
                'name'               => base64_encode($srcName),
                'service'            => $tool,
                'file_path'          => $outputPath,
                'source_name'        => $srcName,
                'source_extension'   => $srcExt,
                'target_name'        => $originalName,
                'target_extension'   => $outputExt,
                'targer_url'         => null,
                'customer_id'        => $entry['user_id'] ?? null,
                'download'           => 0,
                'task_id'            => null,
                'pdf_service_id'     => $this->toolToPdfServiceId($tool),
                'website_id'         => (int) config('services.bo.website_id'),
                'document_status_id' => 3, // completed
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Document save failed', ['error' => $e->getMessage()]);
        }
    }

    private function logConversion(array $entry, string $status, ?string $resultPath, ?string $errorMessage): void
    {
        // Legacy — replaced by saveDocument() above.
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
        // Auth-Path used to write a Download row to a local table, but that
        // table doesn't exist on the shared DB; cache-backed tokens cover
        // both authenticated and guest flows uniformly. Kept the $user
        // arg so the call sites don't change.

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
            'image-to-pdf' => "{$base}.pdf",
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
