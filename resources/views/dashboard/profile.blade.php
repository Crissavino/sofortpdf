@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-6 lg:gap-10">
        @include('dashboard.partials.sidebar')

        <div class="flex-1 min-w-0">
            <h1 class="font-display font-bold text-2xl text-slate-900 mb-6">{{ __('dashboard.profile_heading') }}</h1>

            <form method="POST" action="{{ route('dashboard.profile.update') }}">
                @csrf
                @method('PUT')

                {{-- Persönliche Daten --}}
                <div class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
                    <h2 class="font-display font-bold text-lg text-slate-900 mb-4">{{ __('dashboard.personal_info') }}</h2>

                    <div class="space-y-4 max-w-lg">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('dashboard.label_name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('dashboard.label_email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors"
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Passwort ändern --}}
                <div class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
                    <h2 class="font-display font-bold text-lg text-slate-900 mb-1">{{ __('dashboard.change_password') }}</h2>
                    <p class="text-sm text-slate-500 mb-4">{{ __('dashboard.change_password_hint') }}</p>

                    <div class="space-y-4 max-w-lg">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('dashboard.current_password') }}</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('dashboard.new_password') }}</label>
                            <input type="password" id="password" name="password"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('dashboard.confirm_password') }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors">
                        </div>
                    </div>
                </div>

                {{-- Speichern --}}
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg shadow-sm shadow-brand-600/25 hover:shadow-brand-600/40 transition-all">
                        {{ __('dashboard.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
