@extends('layouts.app')

@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', ($isEn ? 'Login' : 'Anmelden') . ' - SofortPDF')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">{{ $isEn ? 'Login' : 'Anmelden' }}</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- E-Mail --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ $isEn ? 'Email address' : 'E-Mail-Adresse' }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Passwort --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ $isEn ? 'Password' : 'Passwort' }}</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">{{ $isEn ? 'Remember me' : 'Angemeldet bleiben' }}</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $isEn ? 'Forgot password?' : 'Passwort vergessen?' }}</a>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    {{ $isEn ? 'Login' : 'Anmelden' }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                {{ $isEn ? 'No account yet?' : 'Noch kein Konto?' }}
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium">{{ $isEn ? 'Register for free' : 'Kostenlos registrieren' }}</a>
            </p>
        </div>
    </div>
</div>
@endsection
