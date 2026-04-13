<?php

namespace App\Exceptions;

use Exception;

class ConversionServiceException extends Exception
{
    public static function serviceUnavailable(): self
    {
        return new self(__('errors.service_unavailable'));
    }

    /**
     * Generic "conversion failed" exception. The $detail argument is kept
     * for backwards compatibility with older call sites but is no longer
     * surfaced to the user — server-side detail strings tend to be raw
     * English stack traces that confuse people more than they help.
     */
    public static function conversionFailed(string $detail = ''): self
    {
        return new self(__('errors.conversion_failed'));
    }

    public static function timeout(): self
    {
        return new self(__('errors.timeout'));
    }

    public static function fileTooLarge(): self
    {
        return new self(__('errors.file_too_large', ['size' => (int) env('MAX_UPLOAD_SIZE_MB', 50)]));
    }

    public static function unsupportedInput(): self
    {
        return new self(__('errors.unsupported_input'));
    }
}
