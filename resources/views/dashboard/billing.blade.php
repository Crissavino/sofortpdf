@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex gap-10">
        @include('dashboard.partials.sidebar')

        <div class="flex-1 min-w-0">
            <h1 class="font-display font-bold text-2xl text-slate-900 mb-6">{{ __('dashboard.billing_heading') }}</h1>

            {{-- Aktueller Plan --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
                <h2 class="font-display font-bold text-lg text-slate-900 mb-4">{{ __('dashboard.current_plan') }}</h2>

                @if($subscription && $subscription->status === 'active')
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-slate-900">sofortpdf Pro</h3>
                            <p class="text-sm text-slate-500 mt-0.5">{{ __('dashboard.plan_active_desc') }}</p>
                            <p class="text-sm text-slate-700 mt-2 font-medium">
                                {{ __('dashboard.next_payment_amount', ['date' => $subscription->current_period_end->format(__('dashboard.date_format_short'))]) }}<span class="text-brand-600">&euro;39,99</span>
                            </p>
                        </div>
                    </div>

                @elseif($subscription && $subscription->status === 'trialing')
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-slate-900">sofortpdf Pro — {{ __('dashboard.plan_trial_label') }}</h3>
                            <p class="text-sm text-slate-500 mt-0.5">{{ __('dashboard.plan_trial_desc') }}</p>
                            <p class="text-sm text-slate-700 mt-2 font-medium">
                                {{ __('dashboard.your_trial_ends_on', ['date' => $subscription->trial_ends_at->format(__('dashboard.date_format_short'))]) }}
                            </p>
                        </div>
                    </div>

                @elseif($subscription && $subscription->status === 'canceled')
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-slate-900">sofortpdf Pro — {{ __('dashboard.plan_canceled_label') }}</h3>
                            <p class="text-sm text-slate-500 mt-0.5">{{ __('dashboard.plan_canceled_desc') }}</p>
                            <p class="text-sm text-slate-700 mt-2 font-medium">
                                {{ __('dashboard.access_until', ['date' => $subscription->current_period_end->format(__('dashboard.date_format_short'))]) }}
                            </p>
                        </div>
                    </div>

                @else
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-slate-900">{{ __('dashboard.plan_none_title') }}</h3>
                            <p class="text-sm text-slate-500 mt-0.5">{{ __('dashboard.plan_none_desc') }}</p>
                            <a href="{{ route('checkout.start') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg shadow-sm shadow-brand-600/25 hover:shadow-brand-600/40 transition-all">
                                {{ __('dashboard.subscribe_now') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Aktionen --}}
            @if($subscription && in_array($subscription->status, ['active', 'trialing']))
                <div class="bg-white border border-slate-200 rounded-xl p-6">
                    <h2 class="font-display font-bold text-lg text-slate-900 mb-4">{{ __('dashboard.management') }}</h2>

                    <div class="flex flex-wrap gap-3">
                        {{-- Zahlungsmethode ändern --}}
                        <a href="{{ route('dashboard.billing.portal') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            {{ __('dashboard.change_payment_method') }}
                        </a>

                        {{-- Abonnement kündigen --}}
                        <button onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-red-200 hover:border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ __('dashboard.cancel_subscription') }}
                        </button>
                    </div>
                </div>

                {{-- Kündigungs-Dialog --}}
                <div id="cancel-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('cancel-modal').classList.add('hidden')"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                        <h3 class="font-display font-bold text-lg text-slate-900 mb-2">{{ __('dashboard.cancel_confirm_title') }}</h3>
                        <p class="text-sm text-slate-600 mb-6">
                            {{ __('dashboard.cancel_confirm_body') }}
                        </p>
                        <div class="flex gap-3 justify-end">
                            <button onclick="document.getElementById('cancel-modal').classList.add('hidden')"
                                    class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition-colors">
                                {{ __('dashboard.go_back') }}
                            </button>
                            <form method="POST" action="{{ route('dashboard.billing.cancel') }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition-colors">
                                    {{ __('dashboard.yes_cancel_subscription') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
