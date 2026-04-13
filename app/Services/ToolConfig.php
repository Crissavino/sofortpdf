<?php

namespace App\Services;

class ToolConfig
{
    /**
     * Get the full config array for a tool by its key (e.g. 'merge', 'compress').
     */
    public static function get(string $toolKey): ?array
    {
        $config = config("tools.{$toolKey}");

        return is_array($config) ? $config : null;
    }

    /**
     * Check whether a tool is enabled via its feature flag.
     */
    public static function isEnabled(string $toolKey): bool
    {
        return (bool) config("tools.{$toolKey}.enabled", false);
    }

    /**
     * Look up a tool by its URL slug (e.g. 'pdf-zusammenfuegen').
     * Returns the tool config with the tool key added, or null if not found.
     */
    public static function getBySlug(string $slug): ?array
    {
        $tools = config('tools', []);

        foreach ($tools as $key => $tool) {
            if ($key === 'aliases') {
                continue;
            }

            if (is_array($tool) && ($tool['slug'] ?? null) === $slug) {
                return array_merge($tool, ['key' => $key]);
            }
        }

        return null;
    }

    /**
     * Return all enabled tools, suitable for the homepage grid.
     * When a locale other than 'de' is given, locale-specific text
     * (h1, h2, description, meta_description, action_label) is merged in.
     */
    public static function allEnabled(string $locale = 'de'): array
    {
        $tools = config('tools', []);
        $enabled = [];

        foreach ($tools as $key => $tool) {
            if ($key === 'aliases') {
                continue;
            }

            if (is_array($tool) && ! empty($tool['enabled'])) {
                $merged = array_merge($tool, ['key' => $key]);

                // Overlay locale-specific text when not German
                if ($locale !== 'de') {
                    $localeOverrides = config("tools_{$locale}.{$key}", []);
                    if (! empty($localeOverrides)) {
                        $merged = array_merge($merged, $localeOverrides);
                    }

                    // Also set the localized name from locales config
                    $localizedName = config("locales.tool_titles.{$locale}.{$key}");
                    if ($localizedName) {
                        $merged['name'] = $localizedName;
                    }
                }

                $enabled[$key] = $merged;
            }
        }

        return $enabled;
    }

    /**
     * If the given slug is an alias, return the base tool config merged
     * with alias-specific overrides (h1, h2, meta_description, canonical).
     * Returns null if the slug is not a known alias.
     */
    public static function resolveAlias(string $slug): ?array
    {
        $aliases = config('tools.aliases', []);

        if (! isset($aliases[$slug]) || ! is_array($aliases[$slug])) {
            return null;
        }

        $alias = $aliases[$slug];
        $baseToolKey = $alias['tool'] ?? null;

        if (! $baseToolKey) {
            return null;
        }

        $baseTool = static::get($baseToolKey);

        if (! $baseTool) {
            return null;
        }

        return array_merge($baseTool, $alias, [
            'key' => $baseToolKey,
            'alias_slug' => $slug,
        ]);
    }
}
