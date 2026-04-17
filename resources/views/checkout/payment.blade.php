@extends('layouts.app')

@php
    $isEnLocale = app()->getLocale() === 'en';
    $trialPriceFormatted = $isEnLocale
        ? number_format($trialPrice, 2, '.', ',')
        : number_format($trialPrice, 2, ',', '.');
    $subscriptionPriceFormatted = $isEnLocale
        ? number_format($subscriptionPrice, 2, '.', ',')
        : number_format($subscriptionPrice, 2, ',', '.');
@endphp

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <h1 class="font-display font-extrabold text-2xl text-slate-900">{{ __('checkout.payment_heading') }}</h1>
        <p class="text-slate-500 mt-2 text-sm">{{ __('checkout.payment_subheading', ['days' => $trialDays, 'price' => $trialPriceFormatted]) }}</p>
    </div>

    {{-- Pricing card --}}
    <div class="bg-brand-50/50 border border-brand-100 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-display font-bold text-slate-900 text-sm">{{ __('checkout.plan_name') }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('checkout.plan_tagline') }}</p>
            </div>
            <div class="text-right">
                <p class="font-display font-bold text-brand-700">{{ $trialPriceFormatted }} {{ $pricing['symbol'] ?? '€' }}</p>
                <p class="text-xs text-slate-400">{{ __('checkout.then_per_month', ['price' => $subscriptionPriceFormatted]) }}</p>
            </div>
        </div>
    </div>

    {{-- Payment form --}}
    <form id="payment-form" class="space-y-4">
        @csrf

        {{-- E-Mail-Adresse --}}
        @guest
        <div>
            <label for="account-email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('checkout.email_label') }}</label>
            <input type="email" id="account-email" placeholder="{{ __('checkout.email_placeholder') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-colors text-sm bg-white">
        </div>

        {{-- Vollstaendiger Name --}}
        <div>
            <label for="account-name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('checkout.full_name_label') }}</label>
            <input type="text" id="account-name" placeholder="{{ __('checkout.full_name_placeholder') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-colors text-sm bg-white">
        </div>

        <div class="border-t border-slate-100 pt-4"></div>
        @endguest

        {{-- Karteninhaber --}}
        <div>
            <label for="card-name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('checkout.cardholder_label') }}</label>
            <input type="text" id="card-name" placeholder="{{ __('checkout.cardholder_placeholder') }}" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-colors text-sm bg-white">
        </div>

        {{-- Kartennummer --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('checkout.card_number_label') }}</label>
            <div id="card-number" class="stripe-element px-4 py-3 rounded-xl border border-slate-200 bg-white transition-colors"></div>
        </div>

        {{-- Ablaufdatum + CVC --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('checkout.expiry_label') }}</label>
                <div id="card-expiry" class="stripe-element px-4 py-3 rounded-xl border border-slate-200 bg-white transition-colors"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">CVC</label>
                <div id="card-cvc" class="stripe-element px-4 py-3 rounded-xl border border-slate-200 bg-white transition-colors"></div>
            </div>
        </div>

        {{-- Fehleranzeige --}}
        <div id="card-errors" class="hidden bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-sm text-red-600"></div>

        {{-- Submit --}}
        <button type="submit" id="submit-btn"
                class="w-full flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-display font-bold py-3.5 rounded-xl shadow-lg shadow-brand-600/25 hover:shadow-brand-600/40 transition-all text-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <svg id="btn-lock" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span id="btn-text">{{ __('checkout.submit_try_now', ['price' => $trialPriceFormatted]) }}</span>
            <svg id="btn-spinner" class="hidden animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
        </button>

        <p class="text-xs text-center text-slate-400 leading-relaxed">
            {!! __('checkout.terms_disclaimer_html', [
                'terms_url' => route('agb'),
                'days' => $trialDays,
                'price' => $subscriptionPriceFormatted,
            ]) !!}
        </p>
    </form>

    {{-- Trust signals --}}
    @include('partials.trust-signals')
</div>

<style>
    .stripe-element {
        min-height: 44px;
        display: flex;
        align-items: center;
    }
    .stripe-element.StripeElement--focus {
        border-color: rgb(59 108 245 / 0.5);
        box-shadow: 0 0 0 3px rgb(59 108 245 / 0.1);
    }
    .stripe-element.StripeElement--invalid {
        border-color: rgb(239 68 68 / 0.5);
    }
</style>
@endsection

@push('head')
<script src="https://js.stripe.com/v3/"></script>
@endpush

@push('scripts')
<script>
(function() {
    const __t = @json([
        'stripeLocale'   => $isEnLocale ? 'en' : 'de',
        'processing'     => __('checkout.js_processing'),
        'submitTryNow'   => __('checkout.js_submit_try_now', ['price' => $trialPriceFormatted]),
        'errCardholder'  => __('checkout.js_err_cardholder'),
        'errEmail'       => __('checkout.js_err_email'),
        'errName'        => __('checkout.js_err_name'),
        'errGeneric'     => __('checkout.js_err_generic'),
    ]);

    const stripe = Stripe('{{ $stripeKey }}');
    const elements = stripe.elements({
        locale: __t.stripeLocale,
        fonts: [{ cssSrc: 'https://api.fontshare.com/v2/css?f[]=dm-sans@400,500&display=swap' }],
    });

    const elementStyle = {
        base: {
            fontFamily: '"DM Sans", system-ui, sans-serif',
            fontSize: '14px',
            color: '#1e293b',
            '::placeholder': { color: '#94a3b8' },
        },
        invalid: { color: '#ef4444' },
    };

    const cardNumber = elements.create('cardNumber', { style: elementStyle, showIcon: true });
    const cardExpiry = elements.create('cardExpiry', { style: elementStyle });
    const cardCvc = elements.create('cardCvc', { style: elementStyle });

    cardNumber.mount('#card-number');
    cardExpiry.mount('#card-expiry');
    cardCvc.mount('#card-cvc');

    // Element-Events für Focus-Styling
    [cardNumber, cardExpiry, cardCvc].forEach(el => {
        el.on('focus', () => el._parent && el._parent.classList.add('StripeElement--focus'));
        el.on('blur', () => el._parent && el._parent.classList.remove('StripeElement--focus'));
        el.on('change', (event) => {
            if (event.error) {
                showError(event.error.message);
            } else {
                hideError();
            }
        });
    });

    const form = document.getElementById('payment-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLock = document.getElementById('btn-lock');
    const btnSpinner = document.getElementById('btn-spinner');
    const errorEl = document.getElementById('card-errors');

    function showError(message) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    }

    function hideError() {
        errorEl.classList.add('hidden');
    }

    function setLoading(loading) {
        submitBtn.disabled = loading;
        btnText.textContent = loading ? __t.processing : __t.submitTryNow;
        btnLock.classList.toggle('hidden', loading);
        btnSpinner.classList.toggle('hidden', !loading);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideError();

        const cardName = document.getElementById('card-name').value.trim();
        if (!cardName) {
            showError(__t.errCardholder);
            return;
        }

        // E-Mail und Name fuer anonyme Benutzer
        const emailEl = document.getElementById('account-email');
        const nameEl = document.getElementById('account-name');
        const accountEmail = emailEl ? emailEl.value.trim() : '';
        const accountName = nameEl ? nameEl.value.trim() : '';

        if (emailEl && !accountEmail) {
            showError(__t.errEmail);
            return;
        }
        if (nameEl && !accountName) {
            showError(__t.errName);
            return;
        }

        setLoading(true);

        try {
            // 1. PaymentMethod erstellen
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumber,
                billing_details: { name: cardName, email: accountEmail || undefined },
            });

            if (error) {
                showError(error.message);
                setLoading(false);
                return;
            }

            // 2. Abonnement erstellen
            const payload = { payment_method_id: paymentMethod.id };
            if (accountEmail) payload.email = accountEmail;
            if (accountName) payload.name = accountName;
            @auth
            payload.email = '{{ auth()->user()->email ?? '' }}';
            payload.name = '{{ auth()->user()->name ?? '' }}';
            @endauth

            const res = await fetch('/checkout/create-subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (data.error) {
                showError(data.error);
                setLoading(false);
                return;
            }

            // 3. 3D Secure erforderlich?
            if (data.requires_action) {
                const { error: confirmError } = await stripe.confirmCardPayment(data.client_secret, {
                    payment_method: paymentMethod.id,
                });

                if (confirmError) {
                    showError(confirmError.message);
                    setLoading(false);
                    return;
                }

                // 3DS erfolgreich — Bestätigung an Backend
                const confirmRes = await fetch('/checkout/confirm-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ subscription_id: data.subscription_id }),
                });

                const confirmData = await confirmRes.json();

                if (confirmData.error) {
                    showError(confirmData.error);
                    setLoading(false);
                    return;
                }

                window.location.href = confirmData.redirect_url;
                return;
            }

            // 4. Erfolg — weiterleiten
            if (data.success) {
                window.location.href = data.redirect_url;
            }

        } catch (err) {
            showError(__t.errGeneric);
            setLoading(false);
        }
    });
})();
</script>
@endpush
