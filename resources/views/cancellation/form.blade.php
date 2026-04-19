@extends('layouts.app')

@section('title', __('cancellation.title'))

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <div class="text-center mb-8">
        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <h1 class="font-display font-extrabold text-2xl text-slate-900 mb-2">{{ __('cancellation.title') }}</h1>
        <p class="text-slate-500 text-sm">{{ __('cancellation.subtitle') }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <form method="POST" action="{{ route('cancellation.process.' . app()->getLocale()) }}">
                @csrf

                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('cancellation.email_label') }}</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', auth()->user()->email ?? '') }}"
                           required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors"
                           placeholder="{{ __('cancellation.email_placeholder') }}">
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-sm text-amber-800">{{ __('cancellation.warning') }}</p>
                    </div>
                </div>

                <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-display font-bold px-6 py-3 rounded-xl transition text-sm">
                    {{ __('cancellation.cancel_button') }}
                </button>

                {{-- Confirmation modal --}}
                <div id="cancelModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('cancelModal').classList.add('hidden')"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5 text-center">
                            <h3 class="text-lg font-display font-bold text-white">{{ __('cancellation.confirm_title') }}</h3>
                        </div>
                        <div class="px-6 py-5">
                            <p class="text-sm text-slate-600 text-center">{{ __('cancellation.confirm_body') }}</p>
                        </div>
                        <div class="px-6 pb-6 flex gap-3">
                            <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')"
                                    class="flex-1 px-4 py-3 text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                                {{ __('cancellation.keep_subscription') }}
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-3 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-xl transition">
                                {{ __('cancellation.confirm_cancel_button') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="text-center text-xs text-slate-400 mt-6">
        {{ __('cancellation.help_text') }}
        <a href="{{ route('contact.show.' . app()->getLocale()) }}" class="text-brand-600 hover:text-brand-700 underline underline-offset-2">{{ __('cancellation.contact_support') }}</a>
    </p>
</div>
@endsection
