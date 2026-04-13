@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex gap-10">
        @include('dashboard.partials.sidebar')

        <div class="flex-1 min-w-0">
            <h1 class="font-display font-bold text-2xl text-slate-900 mb-6">{{ __('dashboard.index_heading') }}</h1>

            {{-- Abonnement-Status --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
                <h2 class="font-display font-bold text-lg text-slate-900 mb-3">{{ __('dashboard.subscription_status') }}</h2>

                @if($subscription && $subscription->status === 'active')
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ __('dashboard.status_active') }}
                        </span>
                        <span class="text-sm text-slate-500">
                            {{ __('dashboard.next_payment_on', ['date' => $subscription->current_period_end->format(__('dashboard.date_format_short'))]) }}
                        </span>
                    </div>
                @elseif($subscription && $subscription->status === 'trialing')
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            {{ __('dashboard.status_trial') }}
                        </span>
                        <span class="text-sm text-slate-500">
                            {{ __('dashboard.trial_ends_on', ['date' => $subscription->trial_ends_at->format(__('dashboard.date_format_short'))]) }}
                        </span>
                    </div>
                @elseif($subscription && $subscription->status === 'canceled')
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            {{ __('dashboard.status_canceled') }}
                        </span>
                        <span class="text-sm text-slate-500">
                            {{ __('dashboard.access_until', ['date' => $subscription->current_period_end->format(__('dashboard.date_format_short'))]) }}
                        </span>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-600 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            {{ __('dashboard.status_none') }}
                        </span>
                        <a href="{{ route('checkout.start') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                            {{ __('dashboard.subscribe_now') }} &rarr;
                        </a>
                    </div>
                @endif
            </div>

            {{-- Letzte Konvertierungen --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6">
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
            </div>
        </div>
    </div>
</div>
@endsection
