@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-6 lg:gap-10">
        @include('dashboard.partials.sidebar')

        <div class="flex-1 min-w-0">
            <h1 class="font-display font-bold text-2xl text-slate-900 mb-6">Downloads</h1>

            <div class="bg-white border border-slate-200 rounded-xl p-6">
                @if($conversions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="text-left font-medium text-slate-500 pb-3 pr-4">{{ __('dashboard.col_date') }}</th>
                                    <th class="text-left font-medium text-slate-500 pb-3 pr-4">Tool</th>
                                    <th class="text-left font-medium text-slate-500 pb-3 pr-4">{{ __('dashboard.col_filename') }}</th>
                                    <th class="text-left font-medium text-slate-500 pb-3 pr-4">{{ __('dashboard.downloads_col_status') }}</th>
                                    <th class="text-left font-medium text-slate-500 pb-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($conversions as $conversion)
                                    <tr>
                                        <td class="py-3 pr-4 text-slate-600 whitespace-nowrap">{{ $conversion->create_time->format(__('dashboard.date_format')) }}</td>
                                        <td class="py-3 pr-4 text-slate-600">{{ $conversion->tool_slug }}</td>
                                        <td class="py-3 pr-4 text-slate-900 font-medium truncate max-w-[200px]">{{ $conversion->original_filename }}</td>
                                        <td class="py-3 pr-4">
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
                                        <td class="py-3">
                                            @if($conversion->status === 'completed' && $conversion->file_path && file_exists($conversion->file_path))
                                                <a href="{{ route('download.document', $conversion->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-medium rounded-lg transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-slate-400">&mdash;</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $conversions->links() }}
                    </div>
                @else
                    <div class="text-center py-14">
                        <svg class="w-14 h-14 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="font-display font-bold text-slate-700 mb-1">{{ __('dashboard.downloads_empty_title') }}</h3>
                        <p class="text-slate-500 text-sm mb-4">{{ __('dashboard.downloads_empty_desc') }}</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg shadow-sm shadow-brand-600/25 hover:shadow-brand-600/40 transition-all">
                            {{ __('dashboard.try_tool_now') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
