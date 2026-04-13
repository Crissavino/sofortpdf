<?php

namespace App\Http\Controllers;

use App\Exceptions\ConversionServiceException;
use App\Models\ConversionLog;
use App\Models\Download;
use App\Services\ConversionServiceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversionController extends Controller
{
    public function convert(Request $request, ConversionServiceClient $client)
    {
        $request->validate([
            'tool' => 'required|string',
            'file_ids' => 'required|array|min:1',
        ]);

        $tool = $request->input('tool');
        $uploadedFiles = session('upload_files', []);
        $user = $request->user();

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
                'ocr' => $client->ocrPdf($filePaths[0]),
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

            // Log conversion
            ConversionLog::create([
                'customer_id' => $user->id,
                'tool_slug' => "sofortpdf_{$tool}",
                'original_filename' => $originalNames[0] ?? 'unknown',
                'result_filename' => basename($primaryPath),
                'status' => 'completed',
                'file_size' => file_exists($primaryPath) ? filesize($primaryPath) : 0,
                'processing_time_ms' => $processingTime,
            ]);

            // Create download token
            $download = Download::createToken($user->id, $primaryPath, $originalName);

            // Clear session
            session()->forget(['upload_files', 'upload_tool']);

            return response()->json([
                'download_url' => route('download', $download->token),
                'filename' => $originalName,
                'message' => 'Fertig! Ihre Datei ist bereit zum Herunterladen.',
            ]);

        } catch (ConversionServiceException $e) {
            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            ConversionLog::create([
                'customer_id' => $user->id,
                'tool_slug' => "sofortpdf_{$tool}",
                'original_filename' => $originalNames[0] ?? 'unknown',
                'result_filename' => '',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'processing_time_ms' => $processingTime,
            ]);

            return response()->json(['message' => $e->getMessage()], 500);

        } catch (\Exception $e) {
            Log::error("Conversion failed: {$e->getMessage()}", ['tool' => $tool, 'user' => $user->id]);
            return response()->json(['message' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.'], 500);
        }
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
