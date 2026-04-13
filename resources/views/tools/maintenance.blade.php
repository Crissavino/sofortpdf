@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h1 class="font-display font-extrabold text-3xl text-slate-900 mb-3">{{ $toolName }}</h1>
        <p class="text-slate-500 text-lg mb-8">{{ __('tool.maintenance_body') }}</p>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('common.back') }}
        </a>
    </section>
@endsection
