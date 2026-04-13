<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function sign(Request $request)
    {
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

        // Create download token
        $download = \App\Models\Download::createToken(
            $request->user()->id,
            $outputPath,
            'unterschrieben.pdf'
        );

        // Log conversion
        \App\Models\ConversionLog::create([
            'customer_id' => $request->user()->id,
            'tool_slug' => 'sofortpdf_sign',
            'original_filename' => 'document.pdf',
            'result_filename' => basename($outputPath),
            'status' => 'completed',
            'file_size' => filesize($outputPath),
        ]);

        // Clean up temp signature
        @unlink($sigPath);

        return response()->json([
            'download_url' => route('download', $download->token),
            'message' => 'PDF erfolgreich unterzeichnet.',
        ]);
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
