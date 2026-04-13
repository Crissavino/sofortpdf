@extends('layouts.app')

@php
    use App\Services\LocaleHelper;

    // Time-of-day greeting (server time, good enough for a welcome line)
    $hour = (int) now()->format('G');
    if ($hour < 12)        $greetingKey = 'dashboard.welcome_morning';
    elseif ($hour < 18)    $greetingKey = 'dashboard.welcome_afternoon';
    else                   $greetingKey = 'dashboard.welcome_evening';

    // First name for a friendlier tone
    $firstName = trim(explode(' ', (string) $user->name)[0] ?? '');
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex gap-10">
        @include('dashboard.partials.sidebar')

        <div class="flex-1 min-w-0 space-y-8">

            {{-- ═════ Welcome header ═════ --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                        {{ __($greetingKey, ['name' => $firstName ?: $user->email]) }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">{{ __('dashboard.welcome_sub') }}</p>
                </div>

                {{-- Subscription pill --}}
                <div>
                    @if($subscription && $subscription->status === 'active')
                        <a href="{{ route('dashboard.billing') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ __('dashboard.status_active') }}
                        </a>
                    @elseif($subscription && $subscription->status === 'trialing')
                        <a href="{{ route('dashboard.billing') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            {{ __('dashboard.status_trial') }}
                        </a>
                    @elseif($subscription && $subscription->status === 'canceled')
                        <a href="{{ route('dashboard.billing') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            {{ __('dashboard.status_canceled') }}
                        </a>
                    @else
                        <a href="{{ route('checkout.start') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-brand-600 text-white hover:bg-brand-700 transition-colors shadow-sm">
                            {{ __('dashboard.subscribe_now') }} &rarr;
                        </a>
                    @endif
                </div>
            </div>

            {{-- ═════ Stats row ═════ --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-100 rounded-2xl p-5">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('dashboard.stat_this_month') }}</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-display font-extrabold text-3xl text-slate-900">{{ $stats['this_month'] }}</span>
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline></svg>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('dashboard.stat_total') }}</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-display font-extrabold text-3xl text-slate-900">{{ $stats['total'] }}</span>
                        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v16h16"/><path d="M4 16l4-4 4 4 8-8"/></svg>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('dashboard.stat_top_tool') }}</p>
                    <div class="mt-2">
                        @if($stats['top_tool'])
                            @php $topTitle = LocaleHelper::toolTitle($stats['top_tool']); @endphp
                            <a href="{{ LocaleHelper::toolUrl($stats['top_tool']) }}"
                               class="inline-flex items-center gap-2 font-display font-bold text-lg text-slate-900 hover:text-brand-600 transition-colors">
                                {{ $topTitle }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="text-slate-400 text-sm">{{ __('dashboard.stat_none') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ═════ Quick access tools ═════ --}}
            @if(!empty($quickTools))
                <section>
                    <div class="flex items-end justify-between mb-4">
                        <div>
                            <h2 class="font-display font-bold text-lg text-slate-900">{{ __('dashboard.quick_access_heading') }}</h2>
                            <p class="text-sm text-slate-500">{{ __('dashboard.quick_access_sub') }}</p>
                        </div>
                        <a href="{{ route('home') }}#tools" class="text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                            {{ __('dashboard.view_all') }} &rarr;
                        </a>
                    </div>

                    @php
                        $toolColorMap = [
                            'merge'    => ['bg' => 'bg-blue-50',    'icon' => 'text-blue-500'],
                            'compress' => ['bg' => 'bg-amber-50',   'icon' => 'text-amber-500'],
                            'image'    => ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-500'],
                            'word'     => ['bg' => 'bg-indigo-50',  'icon' => 'text-indigo-500'],
                            'split'    => ['bg' => 'bg-violet-50',  'icon' => 'text-violet-500'],
                            'edit'     => ['bg' => 'bg-orange-50',  'icon' => 'text-orange-500'],
                            'sign'     => ['bg' => 'bg-rose-50',    'icon' => 'text-rose-500'],
                            'excel'    => ['bg' => 'bg-green-50',   'icon' => 'text-green-600'],
                        ];
                        $defaultToolColor = ['bg' => 'bg-slate-50', 'icon' => 'text-slate-500'];
                    @endphp

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($quickTools as $tool)
                            @php $tc = $toolColorMap[$tool['icon']] ?? $defaultToolColor; @endphp
                            <a href="{{ LocaleHelper::toolUrl($tool['key']) }}"
                               class="group bg-white border border-slate-100 rounded-xl p-4 hover:border-brand-200 hover:shadow-sm transition-all">
                                <div class="w-10 h-10 rounded-xl {{ $tc['bg'] }} flex items-center justify-center mb-3">
                                    @include('partials.tool-icon', ['icon' => $tool['icon'], 'size' => 'w-5 h-5'])
                                </div>
                                <h3 class="font-display font-bold text-sm text-slate-900 group-hover:text-brand-700 transition-colors">{{ $tool['name'] }}</h3>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ═════ Recent activity ═════ --}}
            <section class="bg-white border border-slate-100 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display font-bold text-lg text-slate-900">{{ __('dashboard.recent_conversions') }}</h2>
                    @if($recentConversions->count() > 0)
                        <a href="{{ route('dashboard.downloads') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                            {{ __('dashboard.view_all') }} &rarr;
                        </a>
                    @endif
                </div>

                @if($recentConversions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="text-left font-medium text-slate-500 pb-3 pr-4">{{ __('dashboard.col_date') }}</th>
                                    <th class="text-left font-medium text-slate-500 pb-3 pr-4">Tool</th>
                                    <th class="text-left font-medium text-slate-500 pb-3 pr-4">{{ __('dashboard.col_filename') }}</th>
                                    <th class="text-left font-medium text-slate-500 pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($recentConversions as $conversion)
                                    <tr>
                                        <td class="py-3 pr-4 text-slate-600 whitespace-nowrap">{{ $conversion->created_at->format(__('dashboard.date_format')) }}</td>
                                        <td class="py-3 pr-4 text-slate-600">{{ str_replace('sofortpdf_', '', $conversion->tool_slug) }}</td>
                                        <td class="py-3 pr-4 text-slate-900 font-medium truncate max-w-[200px]">{{ $conversion->original_filename }}</td>
                                        <td class="py-3">
                                            @if($conversion->status === 'completed')
                                                <span class="inline-flex items-center gap-1 text-emerald-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    {{ __('dashboard.conv_done') }}
                                                </span>
                                            @elseif($conversion->status === 'failed')
                                                <span class="inline-flex items-center gap-1 text-red-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    {{ __('dashboard.conv_failed') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-amber-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ __('dashboard.conv_processing') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-slate-500 text-sm">{{ __('dashboard.no_conversions') }}</p>
                        <a href="{{ route('home') }}" class="inline-block mt-3 text-sm font-medium text-brand-600 hover:text-brand-700">
                            {{ __('dashboard.try_tool_now') }} &rarr;
                        </a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
