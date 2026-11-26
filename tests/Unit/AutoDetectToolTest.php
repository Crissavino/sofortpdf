<?php

namespace Tests\Unit;

use App\Http\Controllers\ConversionController;
use Tests\TestCase;
use ReflectionClass;

/**
 * autoDetectTool() swaps the requested tool when the uploaded file doesn't
 * match it. OpenDocument/RTF were missing from the map, so a .odt uploaded on
 * "PDF to Word" stayed on pdf-to-word and reached CloudConvert with a
 * mismatched input_format.
 */
class AutoDetectToolTest extends TestCase
{
    private function detect(string $requestedTool, array $filenames): string
    {
        $controller = (new ReflectionClass(ConversionController::class))
            ->newInstanceWithoutConstructor();

        $method = new \ReflectionMethod(ConversionController::class, 'autoDetectTool');
        $method->setAccessible(true);

        return $method->invoke($controller, $requestedTool, $filenames);
    }

    /** @dataProvider openDocumentUploads */
    public function test_open_document_uploads_swap_to_the_office_to_pdf_tool(
        string $requested,
        string $filename,
        string $expected
    ): void {
        $this->assertSame($expected, $this->detect($requested, [$filename]));
    }

    public static function openDocumentUploads(): array
    {
        return [
            'odt on pdf-to-word' => ['pdf-to-word', 'vertrag.odt', 'word-to-pdf'],
            'rtf on pdf-to-word' => ['pdf-to-word', 'vertrag.rtf', 'word-to-pdf'],
            'ods on pdf-to-excel' => ['pdf-to-excel', 'budget.ods', 'excel-to-pdf'],
            'odp on pdf-to-ppt' => ['pdf-to-ppt', 'deck.odp', 'ppt-to-pdf'],
            'odt on compress' => ['compress', 'vertrag.odt', 'word-to-pdf'],
        ];
    }

    /** @dataProvider unchangedUploads */
    public function test_matching_uploads_keep_their_tool(
        string $requested,
        string $filename
    ): void {
        $this->assertSame($requested, $this->detect($requested, [$filename]));
    }

    public static function unchangedUploads(): array
    {
        return [
            'pdf on pdf-to-word' => ['pdf-to-word', 'akte.pdf'],
            'docx on word-to-pdf' => ['word-to-pdf', 'akte.docx'],
            'odt on word-to-pdf' => ['word-to-pdf', 'akte.odt'],
            'ods on excel-to-pdf' => ['excel-to-pdf', 'budget.ods'],
            'odp on ppt-to-pdf' => ['ppt-to-pdf', 'deck.odp'],
        ];
    }

    public function test_existing_docx_swap_still_works(): void
    {
        $this->assertSame('word-to-pdf', $this->detect('pdf-to-word', ['akte.docx']));
    }

    public function test_pdf_on_an_x_to_pdf_tool_still_swaps_back(): void
    {
        $this->assertSame('pdf-to-word', $this->detect('word-to-pdf', ['akte.pdf']));
    }
}
