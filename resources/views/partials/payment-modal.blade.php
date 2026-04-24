{{-- =====================================================================
     Payment modal — vanilla JS, single instance per page.

     Rendered once inside layouts/app.blade.php. Hidden by default. Opened
     imperatively via window.SofortpdfPaymentModal.open({ file, filename,
     fileSize, onSuccess, onClose }).

     Reuses the existing Stripe flow (/checkout/create-subscription +
     /checkout/confirm-payment) so the server-side is unchanged.
     ===================================================================== --}}

@php
    $loc = app()->getLocale();
    $isEn = $loc === 'en';
    $trialPrice = $pricing['trial'] ?? 0.69;
    $trialMarketingPrice = $pricing['trial_marketing'] ?? 2.00;
    $trialDays = (int) config('services.stripe.trial_days', 2);
    $subscriptionPrice = $pricing['subscription'] ?? 39.90;
    $currency = $pricing['symbol'] ?? '€';
    $trialPriceFormatted = $isEn
        ? number_format($trialPrice, 2, '.', ',') . ' ' . $currency
        : number_format($trialPrice, 2, ',', '.') . ' ' . $currency;
    $trialMarketingFormatted = $isEn
        ? number_format($trialMarketingPrice, 2, '.', ',') . ' ' . $currency
        : number_format($trialMarketingPrice, 2, ',', '.') . ' ' . $currency;
    $subscriptionPriceFormatted = $isEn
        ? number_format($subscriptionPrice, 2, '.', ',') . ' ' . $currency
        : number_format($subscriptionPrice, 2, ',', '.') . ' ' . $currency;

    // Build JSON payloads for the inline <script> in PHP — using @json with
    // multi-line array literals trips Blade's parser on nested __() calls.
    $__spmJsMessages = json_encode([
        'stripeLocale' => $loc,
        'processing'   => __('payment.processing'),
        'payButton'    => __('payment.pay_button', ['price' => $trialPriceFormatted]),
        'errName'      => __('payment.err_name'),
        'errEmail'     => __('payment.err_email'),
        'errGeneric'   => __('payment.err_generic'),
        'tcRequired'   => __('payment.tc_required'),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    // Stripe public key from the VAD-resolved account (bo_stripe_accounts),
    // falling back to the .env value if StripeService hasn't resolved yet.
    $__stripePublicKey = '';
    try {
        $__stripeAccount = app(\App\Services\Payment\StripeService::class)->getStripeAccount();
        $__stripePublicKey = $__stripeAccount->public_api_key ?? '';
    } catch (\Throwable $e) {}
    if (!$__stripePublicKey) {
        $__stripePublicKey = (string) config('services.stripe.key', '');
    }
    $__spmJsConfig = json_encode([
        'stripeKey' => $__stripePublicKey,
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $__spmFilesCountJson = json_encode(__('payment.files_count'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    // Strike-through uses the marketing trial price (e.g., €2.00 → €0.69).
    $hasTrialDiscount = $trialMarketingPrice > $trialPrice;
    $discountPct = $hasTrialDiscount
        ? (int) round((($trialMarketingPrice - $trialPrice) / $trialMarketingPrice) * 100)
        : 0;
@endphp

<div id="sofortpdf-payment-modal" class="spm-root" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="spm-backdrop" data-spm-close></div>
    <div class="spm-zoom-backdrop" data-spm-zoom-backdrop></div>

    <div class="spm-panel" role="document">
        <button type="button" class="spm-close" data-spm-close aria-label="{{ __('payment.close_button') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
        </button>

        {{-- Hidden preview container — used only for zoom overlay --}}
        <div class="spm-preview" data-spm-preview style="display:none;">
            <div class="spm-preview-placeholder">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span class="spm-preview-ext" data-spm-preview-ext>PDF</span>
            </div>
        </div>

        <div class="spm-body">
            {{-- File header: badge + filename + Preview link --}}
            <div class="spm-file-header">
                <div class="spm-file-info">
                    <span class="spm-file-badge" data-spm-file-ext>PDF</span>
                    <span class="spm-file-name" data-spm-filename>&nbsp;</span>
                </div>
                <button type="button" class="spm-preview-link" data-spm-open-preview>{{ __('payment.preview_link') }}</button>
            </div>

            {{-- Price line --}}
            @php
                $trialNum = $isEn ? number_format($trialPrice, 2, '.', ',') : number_format($trialPrice, 2, ',', '.');
                $marketingNum = $isEn ? number_format($trialMarketingPrice, 2, '.', ',') : number_format($trialMarketingPrice, 2, ',', '.');
            @endphp
            <div class="spm-price-line">
                <span class="spm-price-total-label">Total:</span>
                <span class="spm-price-current">{{ $trialNum }} {{ $currency }}</span>
                @if ($hasTrialDiscount)
                    <span class="spm-price-strike">{{ $marketingNum }} {{ $currency }}</span>
                @endif
            </div>

            <form id="spm-form" class="spm-form" novalidate>
                @csrf

                {{-- Secure payment label + card brand images --}}
                <div class="spm-secure-header">
                    <span class="spm-secure-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 12 15 16 10" stroke="#059669" stroke-width="2.5"/></svg>
                        {{ __('payment.secure_payment') }}
                    </span>
                    <span class="spm-card-brands">
                        <img src="https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4bdb47b03ac81d9945bfe.svg" alt="Visa" class="spm-card-img">
                        <img src="https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130711885b5e41b28c9848f.svg" alt="Mastercard" class="spm-card-img">
                    </span>
                </div>

                {{-- Email --}}
                <div class="spm-field">
                    <label for="spm-email">{{ __('payment.form_email') }}</label>
                    <input type="email" id="spm-email" autocomplete="email" required
                           value="{{ auth()->check() ? auth()->user()->email : '' }}">
                </div>

                {{-- Name --}}
                <div class="spm-field">
                    <label for="spm-name">{{ __('payment.form_full_name') }}</label>
                    <input type="text" id="spm-name" autocomplete="name" required
                           value="{{ auth()->check() ? auth()->user()->name : '' }}">
                </div>

                {{-- Card number — full width --}}
                <div class="spm-field">
                    <label>{{ __('payment.form_card_details') }}</label>
                    <div id="spm-card-number" class="spm-stripe"></div>
                </div>

                {{-- Expiry + CVC — 50/50 row --}}
                <div class="spm-field-grid">
                    <div class="spm-field">
                        <div id="spm-card-expiry" class="spm-stripe"></div>
                    </div>
                    <div class="spm-field">
                        <div id="spm-card-cvc" class="spm-stripe"></div>
                    </div>
                </div>

                {{-- Error area --}}
                <div id="spm-error" class="spm-error" hidden></div>

                {{-- Submit button --}}
                <button type="submit" id="spm-submit" class="spm-submit">
                    <svg id="spm-submit-lock" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span id="spm-submit-text">{{ __('payment.pay_button', ['price' => $trialPriceFormatted]) }}</span>
                    <svg id="spm-submit-spinner" hidden class="spm-spin" width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-opacity="0.25"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </button>

                {{-- AGB checkbox with expandable details --}}
                <label class="spm-tc">
                    <input type="checkbox" id="spm-tc">
                    <span>
                        {{ __('payment.tc_label') }}
                        <details class="spm-tc-details">
                            <summary>{{ __('payment.tc_details_label') }}</summary>
                            <p>{{ __('payment.tc_text', [
                                'days' => $trialDays,
                                'trialPrice' => $trialPriceFormatted,
                                'fullPrice' => $subscriptionPriceFormatted,
                            ]) }}</p>
                        </details>
                    </span>
                </label>

                {{-- Bank statement note --}}
                <p class="spm-bank-note">{{ __('payment.bank_statement', ['name' => 'sofortpdf.com']) }}</p>
            </form>
        </div>
    </div>
</div>

{{-- Bottom bar — appears after user closes the payment modal --}}
<div id="spm-bottom-bar" class="spm-bottom-bar" hidden>
    <div class="spm-bottom-inner">
        <div class="spm-bottom-text">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ __('payment.bottom_bar_text') }}</span>
        </div>
        <button type="button" class="spm-bottom-btn" data-spm-reopen>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            {{ __('payment.bottom_bar_button') }}
        </button>
    </div>
</div>

<style>
    /* ── Modal root & backdrop ── */
    .spm-root {
        position: fixed; inset: 0; z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        padding: 0;
        opacity: 0; pointer-events: none;
        transition: opacity 220ms cubic-bezier(0.23, 1, 0.32, 1);
    }
    .spm-root.spm-open {
        opacity: 1; pointer-events: auto;
    }
    .spm-backdrop {
        position: absolute; inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
    }

    /* ── Panel ── */
    .spm-panel {
        position: relative;
        width: 100%; max-width: 480px;
        max-height: 96vh;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 24px 60px -12px rgba(15, 23, 42, 0.35);
        overflow: hidden;
        display: flex; flex-direction: column;
        transform: translateY(20px) scale(0.98);
        transition: transform 260ms cubic-bezier(0.23, 1, 0.32, 1);
        margin: auto 16px;
    }
    .spm-root.spm-open .spm-panel {
        transform: translateY(0) scale(1);
    }
    @media (max-width: 520px) {
        .spm-panel {
            max-height: 100vh;
            border-radius: 14px 14px 0 0;
            margin: 0;
            align-self: flex-end;
        }
        .spm-root { align-items: flex-end; }
    }

    /* ── Close button ── */
    .spm-close {
        position: absolute; top: 14px; right: 14px;
        width: 32px; height: 32px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 0;
        color: #475569;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: background-color 160ms ease-out;
        z-index: 2;
    }
    .spm-close:hover { background: #e2e8f0; }

    /* ── Body ── */
    .spm-body {
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding: 24px 24px 20px;
        overflow-y: auto;
    }

    /* ── File header ── */
    .spm-file-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-right: 28px; /* room for close button */
    }
    .spm-file-info {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .spm-file-badge {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        background: #eef4ff;
        border: 1px solid #dbeafe;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.06em;
        color: #3b6cf5;
        text-transform: uppercase;
    }
    .spm-file-name {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .spm-preview-link {
        flex-shrink: 0;
        background: none;
        border: 0;
        color: #3b6cf5;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
        text-underline-offset: 2px;
        padding: 0;
        font-family: inherit;
    }
    .spm-preview-link:hover { color: #2554c7; }

    /* ── Price line ── */
    .spm-price-line {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    .spm-price-total-label {
        font-size: 14px;
        font-weight: 500;
        color: #64748b;
    }
    .spm-price-current {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        letter-spacing: -0.3px;
    }
    .spm-price-strike {
        font-size: 14px;
        color: #94a3b8;
        text-decoration: line-through;
        font-weight: 500;
    }

    /* ── Form ── */
    .spm-form { display: flex; flex-direction: column; gap: 12px; }

    /* ── Secure header ── */
    .spm-secure-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 8px 0 10px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 2px;
    }
    .spm-secure-label {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: #059669;
    }
    .spm-card-brands {
        display: inline-flex; align-items: center; gap: 6px;
    }
    .spm-card-img {
        height: 24px;
        width: auto;
        flex-shrink: 0;
    }

    /* ── Fields ── */
    .spm-field label {
        display: block;
        font-size: 12px; color: #334155; font-weight: 600;
        margin-bottom: 6px;
    }
    .spm-field input[type="text"],
    .spm-field input[type="email"],
    .spm-stripe {
        width: 100%; min-height: 42px;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: inherit; font-size: 14px;
        background: #fff; color: #0f172a;
        transition: border-color 160ms ease-out, box-shadow 160ms ease-out;
        box-sizing: border-box;
    }
    .spm-field input:focus {
        outline: none;
        border-color: #3b6cf5;
        box-shadow: 0 0 0 3px rgba(59, 108, 245, 0.12);
    }
    .spm-stripe.StripeElement--focus {
        border-color: #3b6cf5;
        box-shadow: 0 0 0 3px rgba(59, 108, 245, 0.12);
    }
    .spm-stripe.StripeElement--invalid { border-color: #ef4444; }

    .spm-field-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }
    .spm-field-grid .spm-field { margin: 0; }

    /* ── Error ── */
    .spm-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12px; color: #b91c1c;
    }

    /* ── Submit button ── */
    .spm-submit {
        width: 100%;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 14px 20px;
        background: #22c55e;
        color: #fff;
        border: 0;
        border-radius: 8px;
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        font-weight: 800;
        font-size: 15px;
        text-transform: uppercase;
        cursor: pointer;
        transition: transform 160ms cubic-bezier(0.23, 1, 0.32, 1),
                    background-color 180ms ease-out,
                    box-shadow 180ms ease-out;
        box-shadow: 0 6px 16px -4px rgba(34, 197, 94, 0.4);
    }
    .spm-submit:hover:not(:disabled) {
        background: #16a34a;
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -6px rgba(34, 197, 94, 0.5);
    }
    .spm-submit:active:not(:disabled) { transform: scale(0.98); }
    .spm-submit:disabled { opacity: 0.6; cursor: not-allowed; }
    @keyframes spm-spin { to { transform: rotate(360deg); } }
    .spm-spin { animation: spm-spin 0.9s linear infinite; }

    /* ── T&C ── */
    .spm-tc {
        display: flex; align-items: flex-start; gap: 8px;
        font-size: 11px; color: #64748b; line-height: 1.5;
        cursor: pointer;
    }
    .spm-tc input { margin-top: 3px; }
    .spm-tc-details { display: inline; }
    .spm-tc-details summary {
        display: inline; color: #3b6cf5; cursor: pointer;
        text-decoration: underline; text-underline-offset: 2px;
    }
    .spm-tc-details p { margin-top: 6px; color: #94a3b8; }

    /* ── Bank note ── */
    .spm-bank-note {
        font-size: 10px; color: #94a3b8; text-align: center;
        margin-top: 4px;
    }

    /* ── Preview (hidden, used for zoom overlay) ── */
    .spm-preview {
        position: relative;
        aspect-ratio: 3 / 4;
        background: linear-gradient(180deg, #f8fafc 0%, #eef4ff 100%);
        border-radius: 10px;
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        cursor: zoom-in;
    }
    .spm-preview.is-zoomed {
        display: flex !important;
        position: fixed;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 90vw; height: 90vh;
        max-width: 700px;
        z-index: 10001;
        aspect-ratio: auto;
        border-radius: 14px;
        box-shadow: 0 24px 60px -12px rgba(15,23,42,0.5);
        cursor: zoom-out;
        background: #fff;
        padding: 16px;
    }
    .spm-preview.is-zoomed img,
    .spm-preview.is-zoomed canvas {
        max-width: 100%; max-height: 100%;
        object-fit: contain;
    }
    .spm-preview img,
    .spm-preview canvas {
        max-width: 100%; max-height: 100%;
        object-fit: contain;
        border-radius: 6px;
        box-shadow: 0 4px 12px -4px rgba(15, 23, 42, 0.15);
    }
    .spm-preview-placeholder {
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        color: #94a3b8;
    }
    .spm-preview-ext {
        font-size: 10px; font-weight: 800; letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 2px 10px;
        border-radius: 999px;
        background: #e2e8f0; color: #475569;
    }
    .spm-preview .spm-docx-preview {
        width: 100%; height: 100%;
        overflow: hidden;
        background: #fff;
    }
    .spm-preview .spm-docx-preview .docx-wrapper {
        padding: 0; background: transparent;
        overflow: hidden;
    }
    .spm-preview .spm-docx-preview .docx {
        transform-origin: top left;
        box-shadow: none !important;
        margin: 0 !important;
        background: #fff;
    }

    /* Multi-file stacked previews (merge tool) */
    .spm-preview.spm-preview-stack {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
        padding: 10px;
        align-items: stretch;
        background: #fff;
    }
    .spm-preview-stack .spm-stack-item {
        position: relative;
        aspect-ratio: 3 / 4;
        background: linear-gradient(180deg, #f8fafc 0%, #eef4ff 100%);
        border-radius: 6px;
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }
    .spm-preview-stack .spm-stack-item img,
    .spm-preview-stack .spm-stack-item canvas {
        max-width: 100%; max-height: 100%;
        object-fit: contain;
    }
    .spm-preview-stack .spm-stack-item .spm-stack-badge {
        position: absolute; top: 3px; left: 3px;
        background: rgba(15, 23, 42, 0.85);
        color: #fff;
        font-size: 9px; font-weight: 700;
        padding: 1px 5px;
        border-radius: 999px;
        line-height: 1.2;
    }
    .spm-preview-stack .spm-stack-ext {
        font-size: 8px; font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 1px 6px;
        border-radius: 999px;
        background: #e2e8f0; color: #475569;
    }
    .spm-preview-stack .spm-stack-more {
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #64748b;
        background: #f1f5f9;
    }

    /* ── Zoom backdrop ── */
    .spm-zoom-backdrop {
        display: none;
        position: fixed; inset: 0; z-index: 10000;
        background: rgba(15,23,42,0.6);
        backdrop-filter: blur(4px);
    }
    .spm-zoom-backdrop.is-active { display: block; }

    /* ── Body scroll lock ── */
    body.spm-lock { overflow: hidden; }

    /* ── Bottom bar ── */
    .spm-bottom-bar {
        position: fixed; bottom: 0; left: 0; right: 0;
        z-index: 9998;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        box-shadow: 0 -4px 20px -4px rgba(15,23,42,0.12);
        padding: 12px 20px;
        transform: translateY(100%);
        transition: transform 300ms cubic-bezier(0.23,1,0.32,1);
    }
    .spm-bottom-bar.is-visible {
        transform: translateY(0);
    }
    .spm-bottom-inner {
        max-width: 480px; margin: 0 auto;
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
    }
    .spm-bottom-text {
        display: flex; align-items: center; gap: 10px;
        font-size: 14px; font-weight: 600; color: #0f172a;
    }
    .spm-bottom-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 24px;
        background: #22c55e;
        color: #fff;
        border: 0; border-radius: 10px;
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        font-weight: 700; font-size: 14px;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 4px 12px -3px rgba(34,197,94,0.4);
        transition: background 160ms ease-out, transform 160ms ease-out;
    }
    .spm-bottom-btn:hover { background: #16a34a; transform: translateY(-1px); }
    @media (max-width: 480px) {
        .spm-bottom-inner { flex-direction: column; text-align: center; }
        .spm-bottom-btn { width: 100%; justify-content: center; }
    }

    @media (prefers-reduced-motion: reduce) {
        .spm-root, .spm-panel { transition: none !important; }
        .spm-spin { animation: none !important; }
    }
</style>

<script>
(function() {
    if (window.SofortpdfPaymentModal) return; // idempotent

    var root = document.getElementById('sofortpdf-payment-modal');
    if (!root) return;

    // --- i18n for JS-side messages -----------------------------------------
    var __m = {!! $__spmJsMessages !!};
    var __config = {!! $__spmJsConfig !!};
    var __filesCountLabel = {!! $__spmFilesCountJson !!};

    // --- DOM refs ----------------------------------------------------------
    var previewWrap    = root.querySelector('[data-spm-preview]');
    var previewExt     = root.querySelector('[data-spm-preview-ext]');
    var fileBadge      = root.querySelector('[data-spm-file-ext]');
    var filenameEl     = root.querySelector('[data-spm-filename]');
    var previewLink    = root.querySelector('[data-spm-open-preview]');
    var form           = root.querySelector('#spm-form');
    var errorEl        = root.querySelector('#spm-error');
    var submitBtn      = root.querySelector('#spm-submit');
    var submitText     = root.querySelector('#spm-submit-text');
    var submitLock     = root.querySelector('#spm-submit-lock');
    var submitSpinner  = root.querySelector('#spm-submit-spinner');
    var nameInput      = root.querySelector('#spm-name');
    var emailInput     = root.querySelector('#spm-email');
    var tcCheckbox     = root.querySelector('#spm-tc');

    // --- State -------------------------------------------------------------
    var stripe = null;
    var elements = null;
    var cardNumberElement = null;
    var cardExpiryElement = null;
    var cardCvcElement = null;
    var stripeReady = false;
    var onSuccessCb = null;
    var onCloseCb = null;

    // --- Helpers -----------------------------------------------------------
    function formatSize(bytes) {
        if (!bytes && bytes !== 0) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    function escapeHtml(s) {
        return String(s || '').replace(/[&<>"']/g, function(c) {
            return ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' })[c];
        });
    }

    function showError(msg) {
        errorEl.textContent = msg || __m.errGeneric;
        errorEl.hidden = false;
    }
    function hideError() { errorEl.hidden = true; errorEl.textContent = ''; }

    function setLoading(loading) {
        submitBtn.disabled = loading;
        submitText.textContent = loading ? __m.processing : __m.payButton;
        if (submitLock) submitLock.hidden = loading;
        if (submitSpinner) submitSpinner.hidden = !loading;
    }

    // --- Preview builders --------------------------------------------------
    async function renderPreview(files) {
        // Single-file mode
        if (files.length === 1) {
            previewWrap.classList.remove('spm-preview-stack');
            previewWrap.innerHTML = await buildSinglePreviewHtml(files[0]);
            return;
        }

        // Multi-file: stacked grid (up to 5 visible, extra as "+N" tile)
        previewWrap.classList.add('spm-preview-stack');
        var maxVisible = 5;
        var visible = files.slice(0, maxVisible);
        var remaining = files.length - visible.length;

        previewWrap.innerHTML = visible.map(function(f, i) {
            return '<div class="spm-stack-item" data-i="' + i + '">' +
                        '<span class="spm-stack-badge">' + (i + 1) + '</span>' +
                        '<div class="spm-stack-body">' +
                            '<span class="spm-stack-ext">' + extensionFrom(f.name).toUpperCase() + '</span>' +
                        '</div>' +
                   '</div>';
        }).join('') + (remaining > 0
            ? '<div class="spm-stack-item spm-stack-more">+' + remaining + '</div>'
            : '');

        // Replace placeholders with real previews asynchronously
        visible.forEach(function(f, i) {
            buildSinglePreviewHtml(f).then(function(html) {
                var tile = previewWrap.querySelector('[data-i="' + i + '"]');
                if (!tile) return;
                tile.innerHTML = '<span class="spm-stack-badge">' + (i + 1) + '</span>' + html;
            }).catch(function() { /* keep placeholder */ });
        });
    }

    async function buildSinglePreviewHtml(file) {
        if (!file) return placeholderHtml('file');
        var ext = extensionFrom(file.name);

        if (['jpg','jpeg','png','gif','webp','bmp'].indexOf(ext) !== -1) {
            return '<img src="' + URL.createObjectURL(file) + '" alt="">';
        }
        if (ext === 'pdf') {
            try {
                await ensurePdfJs();
                if (window['pdfjs-dist/build/pdf'] || window.pdfjsLib) {
                    return await renderPdfPage1(file);
                }
            } catch (e) { /* fall through to placeholder */ }
        }
        if (ext === 'docx') {
            try {
                return await renderDocxPreview(file);
            } catch (e) { /* fall through */ }
        }
        return placeholderHtml(ext);
    }

    function placeholderHtml(ext) {
        ext = (ext || 'file').toLowerCase();
        return '<div class="spm-preview-placeholder">' +
                '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>' +
                '<span class="spm-preview-ext">' + ext.toUpperCase() + '</span>' +
            '</div>';
    }

    function extensionFrom(name) {
        if (!name) return 'file';
        var parts = String(name).split('.');
        return (parts.length > 1) ? parts.pop().toLowerCase() : 'file';
    }

    async function renderPdfPage1(file) {
        var pdfjs = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
        if (!pdfjs || !pdfjs.getDocument) throw new Error('pdfjs not ready');
        var ab = await file.arrayBuffer();
        var pdf = await pdfjs.getDocument({ data: ab }).promise;
        var page = await pdf.getPage(1);
        var unscaled = page.getViewport({ scale: 1 });
        var targetHeight = 360;
        var scale = targetHeight / unscaled.height;
        var viewport = page.getViewport({ scale: scale * (window.devicePixelRatio || 1) });
        var canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
        return '<img src="' + canvas.toDataURL('image/png') + '" alt="">';
    }

    // Lazy-load PDF.js if not already present
    var __pdfJsLoading = null;
    function ensurePdfJs() {
        var existing = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
        if (existing && existing.getDocument) return Promise.resolve();
        if (__pdfJsLoading) return __pdfJsLoading;
        __pdfJsLoading = new Promise(function(resolve, reject) {
            var s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
            s.onload = function() {
                try {
                    var pdfjs = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
                    if (pdfjs && pdfjs.GlobalWorkerOptions) {
                        pdfjs.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                    }
                } catch (e) { /* non-fatal */ }
                resolve();
            };
            s.onerror = function() { __pdfJsLoading = null; reject(new Error('PDF.js failed to load')); };
            document.head.appendChild(s);
        });
        return __pdfJsLoading;
    }

    // Lazy-load docx-preview
    var __docxLoading = null;
    function ensureDocxPreview() {
        if (window.docx && window.docx.renderAsync) return Promise.resolve();
        if (__docxLoading) return __docxLoading;
        __docxLoading = new Promise(function(resolve, reject) {
            var deps = [
                'https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js',
                'https://cdn.jsdelivr.net/npm/docx-preview@0.3.3/dist/docx-preview.min.js',
            ];
            var remaining = deps.length;
            deps.forEach(function(src) {
                var s = document.createElement('script');
                s.src = src;
                s.onload = function() { if (--remaining === 0) resolve(); };
                s.onerror = function() { __docxLoading = null; reject(new Error('docx-preview failed to load')); };
                document.head.appendChild(s);
            });
        });
        return __docxLoading;
    }
    async function renderDocxPreview(file) {
        await ensureDocxPreview();
        if (!window.docx || !window.docx.renderAsync) throw new Error('docx not ready');
        var host = document.createElement('div');
        host.className = 'spm-docx-preview';
        await window.docx.renderAsync(file, host, null, {
            className: 'docx',
            inWrapper: true,
            ignoreWidth: false,
            ignoreHeight: false,
            ignoreFonts: true,
            breakPages: true,
            ignoreLastRenderedPageBreak: true,
            experimental: false,
            useBase64URL: false,
        });
        var pages = host.querySelectorAll('.docx > section, .docx > .docx_page');
        if (pages.length > 1) {
            for (var i = 1; i < pages.length; i++) pages[i].remove();
        }
        requestAnimationFrame(function() {
            var docEl = host.querySelector('.docx');
            if (docEl && docEl.offsetWidth > 0 && previewWrap.offsetWidth > 0) {
                var scale = (previewWrap.clientWidth - 8) / docEl.offsetWidth;
                if (scale > 0 && scale < 1) {
                    docEl.style.transform = 'scale(' + scale.toFixed(3) + ')';
                }
            }
        });
        return host.outerHTML;
    }

    // Transliterate non-Latin characters to Latin for Stripe billing name.
    function sanitizeStripeName(input) {
        if (!input) return '';
        var s = String(input).normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        s = s.replace(/ß/g, 'ss')
             .replace(/Ä/g, 'Ae').replace(/ä/g, 'ae')
             .replace(/Ö/g, 'Oe').replace(/ö/g, 'oe')
             .replace(/Ü/g, 'Ue').replace(/ü/g, 'ue');
        s = s.replace(/[Șș]/g, 's').replace(/[Țț]/g, 't');
        s = s.replace(/[Łł]/g, function(c) { return c === 'Ł' ? 'L' : 'l'; });
        s = s.replace(/[^\x20-\x7E]/g, '');
        return s.trim();
    }

    // --- Stripe init (lazy) -------------------------------------------------
    function ensureStripeLoaded() {
        return new Promise(function(resolve, reject) {
            if (window.Stripe) return resolve(window.Stripe);
            var s = document.createElement('script');
            s.src = 'https://js.stripe.com/v3/';
            s.onload = function() { resolve(window.Stripe); };
            s.onerror = function() { reject(new Error('Stripe failed to load')); };
            document.head.appendChild(s);
        });
    }

    var stripeElementStyle = {
        base: {
            fontFamily: 'Inter, "DM Sans", system-ui, sans-serif',
            fontSize: '14px',
            color: '#0f172a',
            '::placeholder': { color: '#94a3b8' },
        },
        invalid: { color: '#ef4444' },
    };

    async function initStripe() {
        if (stripeReady) return;
        if (!__config.stripeKey) { console.error('Payment modal: stripeKey is empty'); return; }
        await ensureStripeLoaded();
        console.log('Stripe loaded, mounting card elements...');
        stripe = window.Stripe(__config.stripeKey);
        elements = stripe.elements({ locale: __m.stripeLocale });

        cardNumberElement = elements.create('cardNumber', { style: stripeElementStyle });
        cardExpiryElement = elements.create('cardExpiry', { style: stripeElementStyle });
        cardCvcElement = elements.create('cardCvc', { style: stripeElementStyle });

        cardNumberElement.mount(root.querySelector('#spm-card-number'));
        cardExpiryElement.mount(root.querySelector('#spm-card-expiry'));
        cardCvcElement.mount(root.querySelector('#spm-card-cvc'));

        cardNumberElement.on('change', function(event) {
            if (event.error) showError(event.error.message);
            else hideError();
        });
        cardExpiryElement.on('change', function(event) {
            if (event.error) showError(event.error.message);
            else hideError();
        });
        cardCvcElement.on('change', function(event) {
            if (event.error) showError(event.error.message);
            else hideError();
        });

        stripeReady = true;
    }

    // --- Submit handler ----------------------------------------------------
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        hideError();

        if (!tcCheckbox.checked) { showError(__m.tcRequired); return; }

        var fullName = nameInput ? nameInput.value.trim() : '';
        var email    = emailInput ? emailInput.value.trim() : '';

        if (!fullName) { showError(__m.errName); return; }
        if (!email || email.indexOf('@') < 0) { showError(__m.errEmail); return; }

        // GTM: try to pay
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'try_to_pay' });

        // Log to backend
        var _csrfPay = document.querySelector('meta[name="csrf-token"]');
        fetch('/api/log/event', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfPay ? _csrfPay.content : '' },
            body: JSON.stringify({ event: 'payment_attempted' })
        }).catch(function() {});

        setLoading(true);

        function fail(error, msg) {
            window.dataLayer.push({ event: 'payment_failed', error: error });
            showError(msg || __m.errGeneric);
            setLoading(false);
        }

        try {
            var sanitizedName = sanitizeStripeName(fullName) || fullName;
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            var headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf };
            var locale = '{{ app()->getLocale() }}';

            // Create a fresh Stripe PaymentMethod every attempt
            var pm = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumberElement,
                billing_details: { name: sanitizedName, email: email },
            });
            if (pm.error) { fail(pm.error.message, pm.error.message); return; }

            var paymentMethodId = pm.paymentMethod.id;

            // ═══ Step 1: Create customer (local + BO → Stripe) ═══
            var step1 = await fetch('/api/payment/create-customer', {
                method: 'POST', headers: headers,
                body: JSON.stringify({
                    full_name: sanitizedName,
                    email: email,
                    payment_method_id: paymentMethodId,
                }),
            });
            var step1Data = await step1.json();
            console.log('Step 1:', step1.status, step1Data);
            if (!step1Data.success) { fail('create_customer', step1Data.message); return; }

            // GTM: signup (new customer created during payment)
            window.dataLayer.push({ event: 'signup', method: 'payment' });

            // Update CSRF token (session may have rotated after login)
            if (step1Data.csrf_token) {
                csrf = step1Data.csrf_token;
                headers['X-CSRF-TOKEN'] = csrf;
                var metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) metaTag.setAttribute('content', csrf);
            }

            // ═══ Step 2: Pay trial (BO → Stripe charge) ═══
            var step2 = await fetch('/api/payment/pay-trial', {
                method: 'POST', headers: headers,
                body: JSON.stringify({ payment_method_id: paymentMethodId }),
            });
            var step2Data = await step2.json();
            console.log('Step 2:', step2.status, step2Data);
            if (!step2Data.success) { fail('pay_trial', step2Data.message); return; }

            // Handle 3D Secure if required
            if (step2Data.paymentIntent && step2Data.paymentIntent.status === 'requires_action') {
                var confirmRes = await stripe.confirmCardPayment(step2Data.paymentIntent.client_secret, {
                    payment_method: paymentMethodId,
                });
                if (confirmRes.error) { fail('3ds', confirmRes.error.message); return; }
            }

            // ═══ Step 3: Create subscription (BO → Stripe subscription) ═══
            var step3 = await fetch('/api/payment/create-subscription', {
                method: 'POST', headers: headers,
                body: JSON.stringify({ payment_method_id: paymentMethodId }),
            });
            var step3Data = await step3.json();
            console.log('Step 3:', step3.status, step3Data);
            if (!step3Data.success) { fail('subscription', step3Data.message); return; }

            // GTM: purchase event (GA4 ecommerce + Google Ads conversion)
            window.dataLayer.push({
                event: 'purchase',
                ecommerce: {
                    transaction_id: step1Data.customer_id + '-' + Date.now(),
                    value: {{ $pricing['trial'] ?? 0.69 }},
                    currency: '{{ $pricing['currency'] ?? "EUR" }}',
                    items: [{
                        item_name: 'sofortpdf Trial',
                        price: {{ $pricing['trial'] ?? 0.69 }},
                        quantity: 1,
                    }]
                }
            });

            // Payment succeeded — update paywall flag so the convert button
            // passes the server-side check, then re-trigger the conversion.
            window.__sofortpdfTrialJustPaid = true;
            if (window.sofortpdfPaywall) {
                window.sofortpdfPaywall.userHasSubscription = true;
            }
            setLoading(false);
            close(true);
            if (typeof onSuccessCb === 'function') {
                onSuccessCb();
            } else {
                window.location.reload();
            }
        } catch (err) {
            console.error('Payment flow error:', err);
            fail('exception', err.message || __m.errGeneric);
        }
    });

    // --- Open / close ------------------------------------------------------
    var bottomBar = document.getElementById('spm-bottom-bar');
    var lastOpenOptions = null; // remember so the bar can reopen the modal

    function open(options) {
        options = options || {};
        lastOpenOptions = options;
        onSuccessCb = options.onSuccess || null;
        onCloseCb = options.onClose || null;

        // Hide the bottom bar while the modal is open
        if (bottomBar) { bottomBar.hidden = true; bottomBar.classList.remove('is-visible'); }

        // Normalize: accept either `file` singular or `files` array.
        var files = [];
        if (Array.isArray(options.files)) {
            files = options.files.filter(Boolean);
        } else if (options.file) {
            files = [options.file];
        }

        // Reset UI
        hideError();
        setLoading(false);
        tcCheckbox.checked = false;
        if (nameInput)  nameInput.value  = options.defaultName  || nameInput.value || '';
        if (emailInput) emailInput.value = options.defaultEmail || emailInput.value || '';

        // Label: single file → name. Multi → "First-file.pdf + N more".
        if (files.length === 0) {
            filenameEl.textContent = options.filename || '';
            var ext0 = (extensionFrom(options.filename || '') || 'file').toUpperCase();
            if (fileBadge) fileBadge.textContent = ext0;
            previewWrap.classList.remove('spm-preview-stack');
            previewWrap.innerHTML =
                '<div class="spm-preview-placeholder">' +
                    '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>' +
                    '<span class="spm-preview-ext">' + ext0 + '</span>' +
                '</div>';
        } else if (files.length === 1) {
            filenameEl.textContent = files[0].name;
            var ext1 = (extensionFrom(files[0].name) || 'file').toUpperCase();
            if (fileBadge) fileBadge.textContent = ext1;
            renderPreview(files).catch(function() { /* ignore */ });
        } else {
            filenameEl.textContent = files[0].name + ' +' + (files.length - 1);
            if (fileBadge) fileBadge.textContent = files.length + '\u00d7';
            renderPreview(files).catch(function() { /* ignore */ });
        }

        root.classList.add('spm-open');
        root.setAttribute('aria-hidden', 'false');
        document.body.classList.add('spm-lock');

        // GTM: payment form opened
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'payment_form_opened' });

        // Log to backend
        var _csrf = document.querySelector('meta[name="csrf-token"]');
        fetch('/api/log/event', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf ? _csrf.content : '' },
            body: JSON.stringify({ event: 'payment_modal_opened' })
        }).catch(function() {});

        initStripe().catch(function(e) { console.error('initStripe error:', e); showError(__m.errGeneric); });
    }

    function close(silent) {
        // Close zoom if open
        if (previewWrap && previewWrap.classList.contains('is-zoomed')) {
            previewWrap.classList.remove('is-zoomed');
            if (zoomBackdrop) zoomBackdrop.classList.remove('is-active');
        }

        root.classList.remove('spm-open');
        root.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('spm-lock');

        // Show the bottom bar unless payment just succeeded (silent=true)
        if (!silent && bottomBar && lastOpenOptions) {
            bottomBar.hidden = false;
            requestAnimationFrame(function() {
                bottomBar.classList.add('is-visible');
            });
        }

        var cb = onCloseCb; onCloseCb = null;
        if (!silent && typeof cb === 'function') cb();
    }

    // Close via backdrop / close button
    root.querySelectorAll('[data-spm-close]').forEach(function(el) {
        el.addEventListener('click', function() { close(); });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && root.classList.contains('spm-open')) close();
    });

    // Track T&C details expand — GA4 event
    var tcDetails = root.querySelector('.spm-tc-details');
    if (tcDetails) {
        tcDetails.addEventListener('toggle', function() {
            if (tcDetails.open) {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ event: 'tc_details_viewed' });
            }
        });
    }

    // Preview zoom — moves the preview OUT of the panel into `body` so it
    // escapes the panel's overflow:hidden + transform clipping context.
    var zoomBackdrop = root.querySelector('[data-spm-zoom-backdrop]');
    var previewParent = previewWrap ? previewWrap.parentNode : null;
    var previewNextSibling = previewWrap ? previewWrap.nextSibling : null;

    function openZoom() {
        if (!previewWrap) return;
        // Show the preview (it's hidden by default in the new layout)
        previewWrap.style.display = '';
        // Move to body to escape clipping
        document.body.appendChild(previewWrap);
        previewWrap.classList.add('is-zoomed');
        if (zoomBackdrop) { document.body.appendChild(zoomBackdrop); zoomBackdrop.classList.add('is-active'); }
    }
    function closeZoom() {
        if (!previewWrap) return;
        previewWrap.classList.remove('is-zoomed');
        if (zoomBackdrop) zoomBackdrop.classList.remove('is-active');
        // Move back into the modal panel and hide again
        if (previewParent) {
            if (previewNextSibling) { previewParent.insertBefore(previewWrap, previewNextSibling); }
            else { previewParent.appendChild(previewWrap); }
        }
        previewWrap.style.display = 'none';
        if (zoomBackdrop) root.insertBefore(zoomBackdrop, root.children[1]);
    }

    // Preview link triggers zoom
    if (previewLink) {
        previewLink.addEventListener('click', function(e) {
            e.preventDefault();
            openZoom();
        });
    }

    // Click on zoomed preview closes it
    if (previewWrap) {
        previewWrap.addEventListener('click', function(e) {
            e.stopPropagation();
            if (previewWrap.classList.contains('is-zoomed')) {
                closeZoom();
            }
        });
    }
    if (zoomBackdrop) {
        zoomBackdrop.addEventListener('click', function(e) {
            e.stopPropagation();
            closeZoom();
        });
    }

    // Bottom bar "Download Now" → reopen the modal with the same options
    if (bottomBar) {
        bottomBar.querySelector('[data-spm-reopen]').addEventListener('click', function() {
            if (lastOpenOptions) {
                open(lastOpenOptions);
            }
        });
    }

    // Public API
    window.SofortpdfPaymentModal = {
        open: open,
        close: close,
    };
})();
</script>
