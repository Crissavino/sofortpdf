<?php

namespace App\Exceptions;

use Exception;

class ConversionServiceException extends Exception
{
    public static function serviceUnavailable(): self
    {
        return new self('Der Konvertierungsdienst ist derzeit nicht verfügbar. Bitte versuchen Sie es später erneut.');
    }

    public static function conversionFailed(string $detail = ''): self
    {
        $message = 'Die Konvertierung ist fehlgeschlagen.';
        if ($detail) {
            $message .= ' ' . $detail;
        }
        return new self($message);
    }

    public static function timeout(): self
    {
        return new self('Die Konvertierung hat zu lange gedauert. Bitte versuchen Sie es mit einer kleineren Datei.');
    }

    public static function fileTooLarge(): self
    {
        return new self('Die Datei ist zu groß. Maximale Dateigröße: ' . env('MAX_UPLOAD_SIZE_MB', 50) . ' MB');
    }
}
