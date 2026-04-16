<?php

namespace App\Classes;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * IP geolocation helper. Cascades through providers (ipdata → ipinfo →
 * api.ipinfo.io/lite) and caches the normalized result for 30 days.
 *
 * Honors Cloudflare's CF-IPCOUNTRY shortcut when only the country code is
 * needed — avoids any HTTP call.
 *
 * Mirrors the conversie-pdf / contract-kit implementation so behavior stays
 * consistent across brands.
 */
class GetIpInformation
{
    private const CACHE_TTL = 2592000; // 30 days
    private const PROVIDERS = ['ipdata', 'ipinfo', 'apiIpInfo'];

    /**
     * @param  string|null  $ip
     * @param  string       $purpose  location|address|city|region|state|country|countrycode
     */
    public static function get($ip = null, string $purpose = 'location', bool $deep_detect = true)
    {
        $purpose = strtolower(trim($purpose));
        $purpose = str_replace(['name', "\n", "\t", ' ', '-', '_'], '', $purpose);
        $support = ['country', 'countrycode', 'state', 'region', 'city', 'location', 'address', 'visitor'];

        if (!in_array($purpose, $support, true)) {
            return null;
        }

        $ip = self::detectIp($ip, $deep_detect);
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        // Cloudflare shortcut for country-only queries
        if ($purpose === 'countrycode' && !empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            return strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
        }

        $cacheKey = 'ipgeo:' . $ip;
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return self::shapeOutput($cached, $purpose);
        }

        $data = null;
        foreach (self::PROVIDERS as $provider) {
            try {
                $data = self::fetchFrom($provider, $ip);
                if ($data) {
                    Log::info("[GetIpInformation] Fetched from {$provider} for {$ip} country=" . ($data['country_code'] ?? 'N/A'));
                    break;
                }
            } catch (\Throwable $e) {
                Log::info("[GetIpInformation] {$provider} error for {$ip}: " . $e->getMessage());
                continue;
            }
        }

        if (!$data) {
            return null;
        }

        Cache::put($cacheKey, $data, self::CACHE_TTL);

        return self::shapeOutput($data, $purpose);
    }

    /* ---------- Helpers ---------- */

    private static function detectIp($ip, bool $deep_detect): string
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;

            if ($deep_detect) {
                if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
                    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $candidate) {
                        $candidate = trim($candidate);
                        if (filter_var($candidate, FILTER_VALIDATE_IP) && !self::isPrivateIp($candidate)) {
                            $ip = $candidate;
                            break;
                        }
                    }
                } elseif (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                }
            }
        }

        return $ip ?: '127.0.0.1';
    }

    private static function isPrivateIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    private static function httpGet(string $url, int $timeoutMs = 1000): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_FOLLOWLOCATION    => true,
            CURLOPT_CONNECTTIMEOUT_MS => $timeoutMs,
            CURLOPT_TIMEOUT_MS        => $timeoutMs,
            CURLOPT_HTTPHEADER        => ['Accept: application/json'],
            CURLOPT_USERAGENT         => 'IpGeo/1.0',
            CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
        ]);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err || $code >= 400 || $resp === false) {
            Log::info("[GetIpInformation][HTTP] err={$err} code={$code} url={$url}");
            return null;
        }

        return $resp;
    }

    private static function normalize(array $src): array
    {
        return [
            'city'           => $src['city'] ?? null,
            'region'         => $src['region'] ?? null,
            'country'        => $src['country'] ?? null,
            'country_code'   => $src['country_code'] ?? null,
            'continent'      => $src['continent'] ?? null,
            'continent_code' => $src['continent_code'] ?? null,
        ];
    }

    private static function shapeOutput(array $norm, string $purpose)
    {
        switch ($purpose) {
            case 'location':
                return [
                    'city'           => $norm['city'],
                    'state'          => $norm['region'],
                    'country'        => $norm['country'],
                    'country_code'   => $norm['country_code'],
                    'continent'      => $norm['continent'],
                    'continent_code' => $norm['continent_code'],
                ];
            case 'address':
                return implode(', ', array_filter([$norm['city'], $norm['region'], $norm['country']]));
            case 'city':
                return $norm['city'];
            case 'region':
            case 'state':
                return $norm['region'];
            case 'country':
                return $norm['country'];
            case 'countrycode':
                return $norm['country_code'] ? strtolower($norm['country_code']) : null;
            default:
                return null;
        }
    }

    /* ---------- Providers ---------- */

    private static function fetchFrom(string $provider, string $ip): ?array
    {
        switch ($provider) {
            case 'ipdata':
                $key = config('services.ipdata.key');
                if (!$key) {
                    return null;
                }
                $url = "https://api.ipdata.co/{$ip}?api-key={$key}";
                $raw = self::httpGet($url);
                if (!$raw) {
                    return null;
                }
                $j = json_decode($raw, true);
                if (empty($j['country_code'])) {
                    return null;
                }
                return self::normalize([
                    'city'           => $j['city'] ?? null,
                    'region'         => $j['region'] ?? null,
                    'country'        => $j['country_name'] ?? null,
                    'country_code'   => $j['country_code'] ?? null,
                    'continent'      => $j['continent_name'] ?? null,
                    'continent_code' => $j['continent_code'] ?? null,
                ]);

            case 'ipinfo':
                $key       = config('services.ipinfo.key');
                $tokenPart = $key ? "?token={$key}" : '';
                $url       = "https://ipinfo.io/{$ip}{$tokenPart}";
                $raw       = self::httpGet($url);
                if (!$raw) {
                    return null;
                }
                $j = json_decode($raw, true);
                if (empty($j['country'])) {
                    return null;
                }
                return self::normalize([
                    'country'        => $j['country'] ?? null,
                    'country_code'   => $j['country'] ?? null,
                    'continent'      => $j['continent'] ?? null,
                    'continent_code' => $j['continent_code'] ?? null,
                ]);

            case 'apiIpInfo':
                $key = config('services.ipinfo.key');
                if (!$key) {
                    return null;
                }
                $url = "https://api.ipinfo.io/lite/{$ip}?token={$key}";
                $raw = self::httpGet($url);
                if (!$raw) {
                    return null;
                }
                $j = json_decode($raw, true);
                if (empty($j['country'])) {
                    return null;
                }
                return self::normalize([
                    'city'           => $j['city'] ?? null,
                    'region'         => $j['region'] ?? null,
                    'country'        => $j['country'] ?? null,
                    'country_code'   => $j['country_code'] ?? null,
                    'continent'      => $j['continent'] ?? null,
                    'continent_code' => $j['continent_code'] ?? null,
                ]);
        }

        return null;
    }
}
