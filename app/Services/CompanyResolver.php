<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CompanyResolver
{
    private const CACHE_KEY = 'company_info';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Resolve the operating company as a plain array of fields. Tries the
     * shared DB first; on failure or if COMPANY_ID is unset, returns the
     * env fallback so legal pages never render blank.
     */
    public static function current(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $fallback = config('company.fallback');
            $id       = config('company.id');

            if (!$id) {
                return $fallback;
            }

            try {
                $row = Company::find($id);
                if (!$row) {
                    return $fallback;
                }

                // Merge DB values over the fallback so optional fields
                // (representative, register_court, etc.) that don't exist
                // on the shared row still come from env.
                return array_merge($fallback, [
                    'name'                => (string) ($row->name ?? ''),
                    'address'             => (string) ($row->address ?? ''),
                    'city'                => (string) ($row->city ?? ''),
                    'country'             => (string) ($row->country ?? ''),
                    'postcode'            => (string) ($row->postcode ?? ''),
                    'phone'               => (string) ($row->phone ?? ''),
                    'vat_number'          => (string) ($row->vat_number ?? ''),
                    'registration_number' => (string) ($row->registration_number ?? ''),
                    'num_reg_com'         => (string) ($row->num_reg_com ?? ''),
                ]);
            } catch (\Throwable $e) {
                Log::warning('CompanyResolver: DB read failed, using env fallback', [
                    'error' => $e->getMessage(),
                ]);
                return $fallback;
            }
        });
    }

    /**
     * Convenience: get a single field with an empty-string default.
     */
    public static function field(string $key): string
    {
        return (string) (self::current()[$key] ?? '');
    }

    /**
     * Full single-line address (for inline references in legal copy).
     */
    public static function fullAddressLine(): string
    {
        $c = self::current();
        $parts = array_filter([
            $c['address'] ?? '',
            trim(($c['postcode'] ?? '') . ' ' . ($c['city'] ?? '')),
            $c['country'] ?? '',
        ]);
        return implode(', ', $parts);
    }
}
