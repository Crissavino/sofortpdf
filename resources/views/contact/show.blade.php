@extends('layouts.app')

@section('title', __('contact_ui.title_suffix'))

@section('content')
<section class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-brand-50/40 to-white pointer-events-none"></div>

    <div class="relative max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">
        <div class="text-center mb-10">
            <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('contact_ui.heading') }}</h1>
            <p class="mt-3 text-lg text-slate-500">{{ __('contact_ui.sub') }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            @if(session('status'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.send.' . app()->getLocale()) }}" class="space-y-5">
                @csrf

                {{-- Honeypot: visually hidden, bots fill it --}}
                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('contact_ui.label_name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('contact_ui.label_email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('contact_ui.label_message') }}</label>
                    <textarea id="message" name="message" rows="6" required
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors resize-y @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <p class="text-xs text-slate-500">{{ __('contact_ui.privacy_notice') }}</p>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-display font-bold px-6 py-3 rounded-lg shadow-sm shadow-brand-600/25 hover:shadow-brand-600/40 transition-all text-sm">
                    {{ __('contact_ui.submit') }}
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
