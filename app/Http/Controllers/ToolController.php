<?php

namespace App\Http\Controllers;

use App\Services\ToolConfig;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function show(Request $request)
    {
        $tool = $request->route()->defaults['tool'] ?? $request->route('tool');
        $pageTitle = $request->route()->defaults['pageTitle'] ?? '';
        $locale = $request->route('locale', 'de');

        $toolConfig = ToolConfig::get($tool);

        if (!$toolConfig) {
            abort(404);
        }

        // Check feature flag
        if (!($toolConfig['enabled'] ?? false)) {
            return view('tools.maintenance', [
                'toolName' => $pageTitle ?: $toolConfig['name'],
                'pageTitle' => ($pageTitle ?: $toolConfig['name']) . __('tool.maintenance_suffix'),
                'metaDescription' => $toolConfig['meta_description'] ?? '',
                'slug' => $request->path(),
            ]);
        }

        // Merge locale-specific overrides (English from tools_en config, German stays as default)
        $localeOverrides = ($locale !== 'de') ? config("tools_{$locale}.{$tool}", []) : [];

        $h1 = $localeOverrides['h1'] ?? $toolConfig['h1'] ?? $pageTitle;
        $h2 = $localeOverrides['h2'] ?? $toolConfig['h2'] ?? $toolConfig['description'] ?? '';
        $metaDesc = $localeOverrides['meta_description'] ?? $toolConfig['meta_description'] ?? '';
        $actionLabel = $localeOverrides['action_label'] ?? $toolConfig['action_label'] ?? __('tool.default_action_label');
        $description = $localeOverrides['description'] ?? $toolConfig['description'] ?? '';

        // Use localized title from locales config as fallback
        $localizedTitle = config("locales.tool_titles.{$locale}.{$tool}");
        if ($localizedTitle) {
            $h1 = $localeOverrides['h1'] ?? $localizedTitle;
        }

        // Choose view: sign tool has its own view
        $view = $tool === 'sign' ? 'tools.sign' : 'tools.show';

        return view($view, [
            'tool' => $tool,
            'toolKey' => $tool,
            'toolConfig' => array_merge($toolConfig, $localeOverrides),
            'h1' => $h1,
            'h2' => $h2,
            'pageTitle' => $h1 . __('tool.title_suffix'),
            'metaDescription' => $metaDesc,
            'slug' => $request->path(),
            'canonical' => null,
            'accept' => $toolConfig['accept'] ?? '.pdf',
            'multiple' => $toolConfig['multiple'] ?? false,
            'maxFiles' => $toolConfig['max_files'] ?? 1,
            'actionLabel' => $actionLabel,
        ]);
    }
}
