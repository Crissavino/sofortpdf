@extends('layouts.app')

@section('title', __('auth_ui.register_title_suffix'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">{{ __('auth_ui.register_heading') }}</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('auth_ui.name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- E-Mail --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('auth_ui.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Passwort --}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('auth_ui.password') }}</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Passwort bestaetigen --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('auth_ui.password_confirm') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    {{ __('auth_ui.register_free') }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                {{ __('auth_ui.already_registered') }}
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">{{ __('auth_ui.login_now') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection
