<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class LocaleHelper
{
    /**
     * Get the localized URL for a tool by its key.
     */
    public static function toolUrl(string $toolKey, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $slug = config("locales.tool_slugs.{$locale}.{$toolKey}", $toolKey);
        return "/{$locale}/{$slug}";
    }

    /**
     * Get the localized page title for a tool.
     */
    public static function toolTitle(string $toolKey, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        return config("locales.tool_titles.{$locale}.{$toolKey}", $toolKey);
    }

    /**
     * Get current page URL in a different locale.
     */
    public static function switchLocaleUrl(string $targetLocale): string
    {
        $currentLocale = App::getLocale();
        $path = request()->path();

        // Remove current locale prefix
        $pathWithoutLocale = preg_replace('#^' . $currentLocale . '/?#', '', $path);

        // Check if it's a tool page — find the toolKey from the current slug
        $currentToolSlugs = config("locales.tool_slugs.{$currentLocale}", []);
        $currentAliases = config("locales.aliases.{$currentLocale}", []);

        $toolKey = null;

        // Check regular tool slugs
        foreach ($currentToolSlugs as $key => $slug) {
            if ($pathWithoutLocale === $slug) {
                $toolKey = $key;
                break;
            }
        }

        // Check aliases
        if (!$toolKey && isset($currentAliases[$pathWithoutLocale])) {
            $toolKey = $currentAliases[$pathWithoutLocale];
        }

        if ($toolKey) {
            $targetSlug = config("locales.tool_slugs.{$targetLocale}.{$toolKey}", $pathWithoutLocale);
            return "/{$targetLocale}/{$targetSlug}";
        }

        // Auth routes
        $authSlugs = config("locales.auth_slugs.{$currentLocale}", []);
        foreach ($authSlugs as $key => $slug) {
            if ($pathWithoutLocale === $slug) {
                $targetSlug = config("locales.auth_slugs.{$targetLocale}.{$key}", $slug);
                return "/{$targetLocale}/{$targetSlug}";
            }
        }

        // Legal routes
        $legalSlugs = config("locales.legal_slugs.{$currentLocale}", []);
        foreach ($legalSlugs as $key => $slug) {
            if ($pathWithoutLocale === $slug) {
                $targetSlug = config("locales.legal_slugs.{$targetLocale}.{$key}", $slug);
                return "/{$targetLocale}/{$targetSlug}";
            }
        }

        // Default: just swap the prefix
        return "/{$targetLocale}/{$pathWithoutLocale}";
    }
}
