@php
    $navItems = [
        ['route' => 'dashboard.index', 'label' => __('dashboard.nav_overview'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>'],
        ['route' => 'dashboard.downloads', 'label' => __('dashboard.nav_downloads'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
        ['route' => 'dashboard.billing', 'label' => __('dashboard.nav_subscription'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
        ['route' => 'dashboard.profile', 'label' => __('dashboard.nav_profile'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
    ];
@endphp

{{-- Desktop Sidebar --}}
<aside class="hidden lg:block w-64 shrink-0">
    <nav class="sticky top-24 space-y-1">
        @foreach($navItems as $item)
            @php
                // Treat the demo route as if we were on the overview for
                // sidebar-active styling purposes.
                $isActive = request()->routeIs($item['route'])
                    || ($item['route'] === 'dashboard.index' && request()->routeIs('dashboard.demo'));
            @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ $isActive ? 'bg-brand-50 text-brand-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</aside>

{{-- Mobile Tabs --}}
<div class="lg:hidden mb-6 -mx-4 px-4 overflow-x-auto">
    <div class="flex gap-1 border-b border-slate-200 min-w-max">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                      {{ request()->routeIs($item['route']) ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</div>
