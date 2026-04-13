<?php

namespace App\Services;

use App\Exceptions\ConversionServiceException;
use App\Models\ConversionLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ConversionServiceClient
{
    protected string $baseUrl;
    protected string $token;
    protected bool $enabled;

    public function __construct()
    {
        $config = config('services.conversion');

        $this->baseUrl = rtrim($config['url'] ?? '', '/');
        $this->token = $config['token'] ?? '';
        $this->enabled = (bool) ($config['enabled'] ?? false);
    }

    /**
     * Merge multiple PDF files into one.
     *
     * @param  array<string>  $filePaths  Absolute paths to PDF files
     * @return string  Path to the merged PDF in storage/app/temp/
     */
    public function merge(array $filePaths): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_merge';

        try {
            $request = $this->httpClient();

            foreach ($filePaths as $index => $path) {
                $request = $request->attach(
                    "files[{$index}]",
                    file_get_contents($path),
                    basename($path)
                );
            }

            $response = $request->post("{$this->baseUrl}/api/pdf/merge");

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePaths[0] ?? ''), basename($outputPath), 'success', null, $this->totalFileSize($filePaths), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($filePaths), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($filePaths), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($filePaths), $startTime);
            throw ConversionServiceException::conversionFailed('Zusammenführung fehlgeschlagen.');
        }
    }

    /**
     * Compress a PDF file.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  string  $quality   Compression level: low, medium, high
     * @return string  Path to the compressed PDF in storage/app/temp/
     */
    public function compress(string $filePath, string $quality = 'medium'): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_compress';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/pdf/compress", [
                    'quality' => $quality,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('Komprimierung fehlgeschlagen.');
        }
    }

    /**
     * Convert a file from one format to another.
     *
     * The service exposes a separate endpoint for every (from, to) pair;
     * this method routes to the right one. Add new mappings here as the
     * service adds endpoints.
     *
     * @param  string  $filePath  Absolute path to source file
     * @param  string  $from      Source format (e.g. 'pdf', 'docx')
     * @param  string  $to        Target format (e.g. 'docx', 'pdf')
     * @return string  Path to the converted file in storage/app/temp/
     */
    public function convert(string $filePath, string $from, string $to): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = "sofortpdf_{$from}_to_{$to}";

        $endpoint = $this->resolveConvertEndpoint($from, $to);
        if ($endpoint === null) {
            throw ConversionServiceException::conversionFailed("Konvertierung von {$from} nach {$to} wird nicht unterstützt.");
        }

        // Extension for the saved temp file. Some endpoints (pdf-to-png,
        // pdf-to-jpg) stream a ZIP when the source has multiple pages.
        $outputExt = match (true) {
            $to === 'docx' => 'docx',
            $to === 'xlsx' => 'xlsx',
            $to === 'pptx' => 'pptx',
            default => $to,
        };

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}{$endpoint}");

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), $outputExt);

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed("Konvertierung von {$from} nach {$to} fehlgeschlagen.");
        }
    }

    /**
     * Split a PDF into multiple files by page ranges.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  array   $pages     Page ranges, e.g. ['1-3', '4', '5-8']
     * @return array<string>  Paths to the split PDFs in storage/app/temp/
     */
    public function split(string $filePath, array $pages): array
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_split';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/pdf/split", [
                    'pages' => $pages,
                ]);

            $this->ensureSuccessful($response);

            $responseData = $response->json();
            $outputPaths = [];

            if (isset($responseData['files']) && is_array($responseData['files'])) {
                foreach ($responseData['files'] as $fileData) {
                    $outputPaths[] = $this->saveTempFile(
                        base64_decode($fileData['content']),
                        'pdf'
                    );
                }
            }

            $this->logConversion($toolSlug, basename($filePath), implode(',', array_map('basename', $outputPaths)), 'success', null, filesize($filePath), $startTime);

            return $outputPaths;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('PDF-Trennung fehlgeschlagen.');
        }
    }

    /**
     * Convert JPG/image files to a single PDF.
     *
     * @param  array<string>  $imagePaths  Absolute paths to image files
     * @return string  Path to the resulting PDF in storage/app/temp/
     */
    public function jpgToPdf(array $imagePaths): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_jpg_to_pdf';

        try {
            $request = $this->httpClient();

            foreach ($imagePaths as $index => $path) {
                $request = $request->attach(
                    "images[{$index}]",
                    file_get_contents($path),
                    basename($path)
                );
            }

            // Service doesn't have a bulk jpg-to-pdf endpoint, but /api/pdf/merge
            // accepts images and converts them internally before merging.
            $response = $request->post("{$this->baseUrl}/api/pdf/merge");

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($imagePaths[0] ?? ''), basename($outputPath), 'success', null, $this->totalFileSize($imagePaths), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($imagePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($imagePaths), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($imagePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($imagePaths), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($imagePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($imagePaths), $startTime);
            throw ConversionServiceException::conversionFailed('JPG-zu-PDF-Konvertierung fehlgeschlagen.');
        }
    }

    /**
     * Convert a PDF to JPG images (one per page).
     *
     * @param  string  $filePath  Absolute path to PDF
     * @return array<string>  Paths to the resulting JPG images in storage/app/temp/
     */
    public function pdfToJpg(string $filePath): array
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_pdf_to_jpg';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/convert/pdf-to-jpg");

            $this->ensureSuccessful($response);

            $responseData = $response->json();
            $outputPaths = [];

            if (isset($responseData['images']) && is_array($responseData['images'])) {
                foreach ($responseData['images'] as $imageData) {
                    $outputPaths[] = $this->saveTempFile(
                        base64_decode($imageData['content']),
                        'jpg'
                    );
                }
            }

            $this->logConversion($toolSlug, basename($filePath), implode(',', array_map('basename', $outputPaths)), 'success', null, filesize($filePath), $startTime);

            return $outputPaths;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('PDF-zu-JPG-Konvertierung fehlgeschlagen.');
        }
    }

    /**
     * Rotate PDF pages by a given angle.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  int     $angle     Rotation angle: 90, 180, or 270
     * @return string  Path to the rotated PDF in storage/app/temp/
     */
    public function rotate(string $filePath, int $angle): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_rotate';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/pdf/rotate", [
                    'angle' => $angle,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('PDF-Drehung fehlgeschlagen.');
        }
    }

    /**
     * Protect a PDF with a password.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  string  $password  Password to set
     * @return string  Path to the protected PDF in storage/app/temp/
     */
    public function protect(string $filePath, string $password): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_protect';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                // TODO: conversion-service does not expose /api/pdf/protect yet.
                // Keeping the call here for when the endpoint ships; the tool
                // is feature-flagged off in config/tools.php in the meantime.
                ->post("{$this->baseUrl}/api/pdf/protect", [
                    'password' => $password,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('PDF-Schutz fehlgeschlagen.');
        }
    }

    /**
     * Unlock a password-protected PDF.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  string  $password  Password to unlock
     * @return string  Path to the unlocked PDF in storage/app/temp/
     */
    public function unlock(string $filePath, string $password): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_unlock';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/pdf/unlock", [
                    'password' => $password,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('PDF-Entsperrung fehlgeschlagen.');
        }
    }

    /**
     * Add a text watermark to a PDF.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  string  $text      Watermark text
     * @param  float   $opacity   Opacity (0.0 to 1.0)
     * @param  int     $fontSize  Font size in points
     * @param  int     $angle     Rotation angle for the watermark
     * @return string  Path to the watermarked PDF in storage/app/temp/
     */
    public function watermark(string $filePath, string $text, float $opacity = 0.5, int $fontSize = 48, int $angle = 45): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_watermark';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/pdf/watermark", [
                    'text' => $text,
                    'fontSize' => $fontSize,
                    'opacity' => $opacity,
                    'angle' => $angle,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('Wasserzeichen fehlgeschlagen.');
        }
    }

    /**
     * Convert a PDF to PNG images (one per page).
     *
     * @param  string  $filePath  Absolute path to PDF
     * @return array<string>  Paths to the resulting PNG images in storage/app/temp/
     */
    public function pdfToPng(string $filePath): array
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_pdf_to_png';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/convert/pdf-to-png");

            $this->ensureSuccessful($response);

            $responseData = $response->json();
            $outputPaths = [];

            if (isset($responseData['images']) && is_array($responseData['images'])) {
                foreach ($responseData['images'] as $imageData) {
                    $outputPaths[] = $this->saveTempFile(
                        base64_decode($imageData['content']),
                        'png'
                    );
                }
            }

            $this->logConversion($toolSlug, basename($filePath), implode(',', array_map('basename', $outputPaths)), 'success', null, filesize($filePath), $startTime);

            return $outputPaths;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('PDF-zu-PNG-Konvertierung fehlgeschlagen.');
        }
    }

    /**
     * Convert a PDF to PowerPoint.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @return string  Path to the resulting PPTX in storage/app/temp/
     */
    public function pdfToPpt(string $filePath): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_pdf_to_ppt';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/convert/pdf-to-powerpoint");

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pptx');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('PDF-zu-PowerPoint-Konvertierung fehlgeschlagen.');
        }
    }

    /**
     * Convert an Office file (PPT, PPTX, etc.) to PDF.
     *
     * @param  string  $filePath  Absolute path to Office file
     * @return string  Path to the resulting PDF in storage/app/temp/
     */
    public function officeToPdf(string $filePath): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_office_to_pdf';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/convert/office-to-pdf");

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('Office-zu-PDF-Konvertierung fehlgeschlagen.');
        }
    }

    /**
     * Convert PNG images to a single PDF.
     *
     * @param  array<string>  $imagePaths  Absolute paths to PNG files
     * @return string  Path to the resulting PDF in storage/app/temp/
     */
    public function pngToPdf(array $imagePaths): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_png_to_pdf';

        try {
            $request = $this->httpClient();

            foreach ($imagePaths as $index => $path) {
                $request = $request->attach(
                    "images[{$index}]",
                    file_get_contents($path),
                    basename($path)
                );
            }

            // Service image-to-pdf accepts only a single file; for multiple
            // images we submit to /api/pdf/merge which handles image→pdf
            // internally and merges everything in order.
            $response = $request->post("{$this->baseUrl}/api/pdf/merge");

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($imagePaths[0] ?? ''), basename($outputPath), 'success', null, $this->totalFileSize($imagePaths), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($imagePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($imagePaths), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($imagePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($imagePaths), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($imagePaths[0] ?? ''), null, 'failed', $e->getMessage(), $this->totalFileSize($imagePaths), $startTime);
            throw ConversionServiceException::conversionFailed('PNG-zu-PDF-Konvertierung fehlgeschlagen.');
        }
    }

    /**
     * OCR a scanned PDF to produce a searchable PDF.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  string  $language  OCR language(s), e.g. 'deu+eng'
     * @return string  Path to the OCR'd PDF in storage/app/temp/
     */
    public function ocrPdf(string $filePath, string $language = 'deu+eng'): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_ocr';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/ocr/pdf", [
                    'language' => $language,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('OCR fehlgeschlagen.');
        }
    }

    /**
     * Remove specific pages from a PDF.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  string  $pages     Pages to remove, e.g. '1,3,5-7'
     * @return string  Path to the resulting PDF in storage/app/temp/
     */
    public function removePages(string $filePath, string $pages): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_remove_pages';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/pdf/remove-pages", [
                    'pages' => $pages,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('Seiten-Entfernung fehlgeschlagen.');
        }
    }

    /**
     * Extract specific pages from a PDF.
     *
     * @param  string  $filePath  Absolute path to PDF
     * @param  string  $pages     Pages to extract, e.g. '1,3,5-7'
     * @return string  Path to the resulting PDF in storage/app/temp/
     */
    public function extractPages(string $filePath, string $pages): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_extract_pages';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/pdf/extract-pages", [
                    'pages' => $pages,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('Seiten-Extraktion fehlgeschlagen.');
        }
    }

    /**
     * Optimize a PDF (PDF/A compatible).
     *
     * @param  string  $filePath  Absolute path to PDF
     * @return string  Path to the optimized PDF in storage/app/temp/
     */
    public function optimize(string $filePath): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_optimize';

        try {
            $response = $this->httpClient()
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post("{$this->baseUrl}/api/pdf/optimize");

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, basename($filePath), basename($outputPath), 'success', null, filesize($filePath), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, basename($filePath), null, 'failed', $e->getMessage(), filesize($filePath), $startTime);
            throw ConversionServiceException::conversionFailed('PDF-Optimierung fehlgeschlagen.');
        }
    }

    /**
     * Convert HTML to PDF.
     *
     * @param  string  $html  HTML content
     * @return string  Path to the resulting PDF in storage/app/temp/
     */
    public function htmlToPdf(string $html): string
    {
        $this->ensureEnabled();

        $startTime = microtime(true);
        $toolSlug = 'sofortpdf_html_to_pdf';

        try {
            $response = $this->httpClient()
                ->post("{$this->baseUrl}/api/convert/html-to-pdf", [
                    'html' => $html,
                ]);

            $this->ensureSuccessful($response);

            $outputPath = $this->saveTempFile($response->body(), 'pdf');

            $this->logConversion($toolSlug, 'input.html', basename($outputPath), 'success', null, strlen($html), $startTime);

            return $outputPath;
        } catch (ConversionServiceException $e) {
            $this->logConversion($toolSlug, 'input.html', null, 'failed', $e->getMessage(), strlen($html), $startTime);
            throw $e;
        } catch (ConnectionException $e) {
            $this->logConversion($toolSlug, 'input.html', null, 'failed', $e->getMessage(), strlen($html), $startTime);
            throw ConversionServiceException::timeout();
        } catch (\Throwable $e) {
            $this->logConversion($toolSlug, 'input.html', null, 'failed', $e->getMessage(), strlen($html), $startTime);
            throw ConversionServiceException::conversionFailed('HTML-zu-PDF-Konvertierung fehlgeschlagen.');
        }
    }

    /**
     * Build the base HTTP client with auth token and timeout.
     */
    protected function httpClient(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withToken($this->token)->timeout(120);
    }

    /**
     * Resolve a (from, to) pair to the conversion-service endpoint path,
     * or null if the combination isn't supported by the service.
     */
    protected function resolveConvertEndpoint(string $from, string $to): ?string
    {
        $from = strtolower($from);
        $to = strtolower($to);

        // Anything Office-like going to PDF uses the LibreOffice endpoint.
        $officeFormats = ['doc', 'docx', 'odt', 'rtf', 'xls', 'xlsx', 'ods', 'csv', 'ppt', 'pptx', 'odp'];
        if (in_array($from, $officeFormats, true) && $to === 'pdf') {
            return '/api/convert/office-to-pdf';
        }

        $imageFormats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp'];
        if (in_array($from, $imageFormats, true) && $to === 'pdf') {
            return '/api/convert/image-to-pdf';
        }

        return match ("{$from}->{$to}") {
            'pdf->docx' => '/api/convert/pdf-to-word',
            'pdf->xlsx' => '/api/convert/pdf-to-excel',
            'pdf->pptx' => '/api/convert/pdf-to-powerpoint',
            'pdf->jpg', 'pdf->jpeg' => '/api/convert/pdf-to-jpg',
            'pdf->png' => '/api/convert/pdf-to-png',
            'pdf->txt' => '/api/convert/pdf-to-text',
            'pdf->epub' => '/api/convert/pdf-to-epub',
            'pdf->mobi' => '/api/convert/pdf-to-mobi',
            'epub->pdf' => '/api/convert/epub-to-pdf',
            'heic->pdf' => '/api/convert/heic-to-pdf',
            'heic->jpg', 'heic->jpeg' => '/api/convert/heic-to-jpg',
            'svg->png' => '/api/convert/svg-to-png',
            'webp->jpg', 'webp->jpeg' => '/api/convert/webp-to-jpg',
            'webp->png' => '/api/convert/webp-to-png',
            'jpg->webp', 'jpeg->webp' => '/api/convert/jpg-to-webp',
            'png->webp' => '/api/convert/png-to-webp',
            default => null,
        };
    }

    /**
     * Ensure the conversion service is enabled.
     *
     * @throws ConversionServiceException
     */
    protected function ensureEnabled(): void
    {
        if (! $this->enabled) {
            throw ConversionServiceException::serviceUnavailable();
        }
    }

    /**
     * Validate that the HTTP response indicates success.
     *
     * @throws ConversionServiceException
     */
    protected function ensureSuccessful(\Illuminate\Http\Client\Response $response): void
    {
        if ($response->status() === 413) {
            throw ConversionServiceException::fileTooLarge();
        }

        if ($response->status() === 504 || $response->status() === 408) {
            throw ConversionServiceException::timeout();
        }

        if (! $response->successful()) {
            $detail = $response->json('error') ?? $response->json('message') ?? '';
            throw ConversionServiceException::conversionFailed($detail);
        }
    }

    /**
     * Save binary content to a temp file and return the full path.
     */
    protected function saveTempFile(string $content, string $extension): string
    {
        $directory = storage_path('app/temp');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = Str::uuid() . '.' . $extension;
        $path = $directory . '/' . $filename;

        file_put_contents($path, $content);

        return $path;
    }

    /**
     * Log a conversion attempt to the conversion_logs table.
     */
    protected function logConversion(
        string $toolSlug,
        ?string $originalFilename,
        ?string $resultFilename,
        string $status,
        ?string $errorMessage,
        ?int $fileSize,
        float $startTime
    ): void {
        $processingTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        try {
            ConversionLog::create([
                'tool_slug' => $toolSlug,
                'original_filename' => $originalFilename,
                'result_filename' => $resultFilename,
                'status' => $status,
                'error_message' => $errorMessage,
                'file_size' => $fileSize,
                'processing_time_ms' => $processingTimeMs,
            ]);
        } catch (\Throwable $e) {
            // Logging should never cause the main operation to fail
            report($e);
        }
    }

    /**
     * Calculate total file size from an array of file paths.
     */
    protected function totalFileSize(array $paths): int
    {
        $total = 0;
        foreach ($paths as $path) {
            if (is_file($path)) {
                $total += filesize($path);
            }
        }
        return $total;
    }
}
