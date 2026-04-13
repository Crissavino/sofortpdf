@extends('layouts.app')

@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <h1 class="font-display font-extrabold text-2xl text-slate-900">{{ $isEn ? 'Unlock now' : 'Jetzt freischalten' }}</h1>
        <p class="text-slate-500 mt-2 text-sm">{{ $isEn ? $trialDays . ' days trial for only ' . number_format($trialPrice, 2, '.', ',') . ' &euro;' : $trialDays . ' Tage testen für nur ' . number_format($trialPrice, 2, ',', '.') . ' &euro;' }}</p>
    </div>

    {{-- Pricing card --}}
    <div class="bg-brand-50/50 border border-brand-100 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-display font-bold text-slate-900 text-sm">sofortpdf Pro</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $isEn ? 'All PDF tools — unlimited' : 'Alle PDF-Tools — unbegrenzt' }}</p>
            </div>
            <div class="text-right">
                <p class="font-display font-bold text-brand-700">{{ number_format($trialPrice, 2, ',', '.') }} &euro;</p>
                <p class="text-xs text-slate-400">{{ $isEn ? 'then ' . number_format($subscriptionPrice, 2, '.', ',') . ' &euro;/month' : 'dann ' . number_format($subscriptionPrice, 2, ',', '.') . ' &euro;/Monat' }}</p>
            </div>
        </div>
    </div>

    {{-- Payment form --}}
    <form id="payment-form" class="space-y-4">
        @csrf

        {{-- E-Mail-Adresse --}}
        @guest
        <div>
            <label for="account-email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ $isEn ? 'Email address' : 'E-Mail-Adresse' }}</label>
            <input type="email" id="account-email" placeholder="{{ $isEn ? 'your@email.com' : 'ihre@email.de' }}" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-colors text-sm bg-white">
        </div>

        {{-- Vollstaendiger Name --}}
        <div>
            <label for="account-name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ $isEn ? 'Full name' : 'Vollständiger Name' }}</label>
            <input type="text" id="account-name" placeholder="{{ $isEn ? 'John Doe' : 'Max Mustermann' }}" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-colors text-sm bg-white">
        </div>

        <div class="border-t border-slate-100 pt-4"></div>
        @endguest

        {{-- Karteninhaber --}}
        <div>
            <label for="card-name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ $isEn ? 'Cardholder' : 'Karteninhaber' }}</label>
            <input type="text" id="card-name" placeholder="{{ $isEn ? 'John Doe' : 'Max Mustermann' }}" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-colors text-sm bg-white">
        </div>

        {{-- Kartennummer --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $isEn ? 'Card number' : 'Kartennummer' }}</label>
            <div id="card-number" class="stripe-element px-4 py-3 rounded-xl border border-slate-200 bg-white transition-colors"></div>
        </div>

        {{-- Ablaufdatum + CVC --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $isEn ? 'Expiry date' : 'Ablaufdatum' }}</label>
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
            <span id="btn-text">{{ $isEn ? 'Try now for ' . number_format($trialPrice, 2, '.', ',') . ' &euro;' : 'Jetzt für ' . number_format($trialPrice, 2, ',', '.') . ' &euro; testen' }}</span>
            <svg id="btn-spinner" class="hidden animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
        </button>

        <p class="text-xs text-center text-slate-400 leading-relaxed">
            @if($isEn)
                By clicking the button you agree to our
                <a href="{{ route('agb') }}" class="underline hover:text-slate-600" target="_blank">Terms</a>.
                Your trial ends after {{ $trialDays }} days.
                After that, {{ number_format($subscriptionPrice, 2, '.', ',') }} &euro;/month will be charged.
                Cancel anytime.
            @else
                Mit dem Klick auf den Button stimmen Sie unseren
                <a href="{{ route('agb') }}" class="underline hover:text-slate-600" target="_blank">AGB</a> zu.
                Ihr Testzeitraum endet nach {{ $trialDays }} Tagen.
                Danach werden {{ number_format($subscriptionPrice, 2, ',', '.') }} &euro;/Monat berechnet.
                Jederzeit kündbar.
            @endif
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
    const __isEn = {{ $isEn ? 'true' : 'false' }};
    const stripe = Stripe('{{ $stripeKey }}');
    const elements = stripe.elements({
        locale: __isEn ? 'en' : 'de',
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
        btnText.textContent = loading
            ? (__isEn ? 'Processing\u2026' : 'Wird verarbeitet\u2026')
            : (__isEn ? 'Try now for {{ number_format($trialPrice, 2, ".", ",") }} \u20AC' : 'Jetzt f\u00FCr {{ number_format($trialPrice, 2, ",", ".") }} \u20AC testen');
        btnLock.classList.toggle('hidden', loading);
        btnSpinner.classList.toggle('hidden', !loading);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideError();

        const cardName = document.getElementById('card-name').value.trim();
        if (!cardName) {
            showError(__isEn ? 'Please enter the cardholder name.' : 'Bitte geben Sie den Namen des Karteninhabers ein.');
            return;
        }

        // E-Mail und Name fuer anonyme Benutzer
        const emailEl = document.getElementById('account-email');
        const nameEl = document.getElementById('account-name');
        const accountEmail = emailEl ? emailEl.value.trim() : '';
        const accountName = nameEl ? nameEl.value.trim() : '';

        if (emailEl && !accountEmail) {
            showError(__isEn ? 'Please enter your email address.' : 'Bitte geben Sie Ihre E-Mail-Adresse ein.');
            return;
        }
        if (nameEl && !accountName) {
            showError(__isEn ? 'Please enter your full name.' : 'Bitte geben Sie Ihren vollständigen Namen ein.');
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
            showError(__isEn ? 'An error occurred. Please try again.' : 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
            setLoading(false);
        }
    });
})();
</script>
@endpush
