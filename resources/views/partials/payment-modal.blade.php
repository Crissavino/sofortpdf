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
    $trialPrice = (float) config('services.stripe.trial_price', 1.50);
    $trialDays = (int) config('services.stripe.trial_days', 2);
    $subscriptionPrice = (float) config('services.stripe.subscription_price', 39.99);
    $currency = '€';
    $trialPriceFormatted = $isEn
        ? number_format($trialPrice, 2, '.', ',') . ' ' . $currency
        : number_format($trialPrice, 2, ',', '.') . ' ' . $currency;
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
    $__spmJsConfig = json_encode([
        'stripeKey' => (string) config('services.stripe.key', ''),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $__spmFilesCountJson = json_encode(__('payment.files_count'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    // Discount % for the strike-through: (full - trial) / full
    $discountPct = ($subscriptionPrice > 0 && $subscriptionPrice > $trialPrice)
        ? (int) round((($subscriptionPrice - $trialPrice) / $subscriptionPrice) * 100)
        : 0;
@endphp

<div id="sofortpdf-payment-modal" class="spm-root" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="spm-backdrop" data-spm-close></div>

    <div class="spm-panel" role="document">
        <button type="button" class="spm-close" data-spm-close aria-label="{{ __('payment.close_button') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
        </button>

        {{-- Step progress bar (Form → Payment → Download) --}}
        <div class="spm-steps">
            <div class="spm-step is-current">
                <span class="spm-step-dot">
                    <svg class="spm-step-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span class="spm-step-label">{{ __('payment.step_1') }}</span>
            </div>
            <span class="spm-step-bar"></span>
            <div class="spm-step">
                <span class="spm-step-dot">2</span>
                <span class="spm-step-label">{{ __('payment.step_2') }}</span>
            </div>
            <span class="spm-step-bar"></span>
            <div class="spm-step">
                <span class="spm-step-dot">3</span>
                <span class="spm-step-label">{{ __('payment.step_3') }}</span>
            </div>
        </div>

        {{-- Mobile-only toggle: preview is hidden by default on small screens
             so the payment form is immediately visible. Expands on tap. --}}
        <button type="button" class="spm-preview-toggle" data-spm-preview-toggle aria-expanded="false">
            <svg class="spm-toggle-chev" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            <span data-spm-toggle-label>{{ __('payment.show_preview') }}</span>
            <span class="spm-toggle-filename" data-spm-toggle-filename></span>
        </button>

        <div class="spm-body">
            {{-- ═════ LEFT: FILE PREVIEW + INCLUDED LIST ═════
                 Desktop: inline 5/12 column. Mobile: hidden until the
                 "Show preview" toggle is tapped, then promoted to a full
                 overlay sheet on top of the payment form (close button in
                 the header returns to the form). --}}
            <div class="spm-left">
                <div class="spm-left-header">
                    <button type="button" class="spm-left-back" data-spm-preview-toggle aria-label="{{ __('payment.back_to_form') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
                        <span>{{ __('payment.back_to_form') }}</span>
                    </button>
                    <span class="spm-left-title">{{ __('payment.preview_title') }}</span>
                </div>
                <div class="spm-preview-card">
                    <div class="spm-preview" data-spm-preview>
                        {{-- Corner ribbon badge — JS updates data-ext --}}
                        <span class="spm-preview-ribbon" data-spm-preview-ribbon>PDF</span>
                        <div class="spm-preview-placeholder">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            <span class="spm-preview-ext" data-spm-preview-ext>PDF</span>
                        </div>
                    </div>
                    <div class="spm-preview-meta">
                        <p class="spm-preview-name" data-spm-filename>&nbsp;</p>
                        <p class="spm-preview-size" data-spm-filesize>&nbsp;</p>
                        <p class="spm-preview-count" data-spm-filecount hidden>&nbsp;</p>
                    </div>

                    <ul class="spm-included">
                        <li><span class="spm-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>{{ __('payment.included_item_1') }}</li>
                        <li><span class="spm-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>{{ __('payment.included_item_2') }}</li>
                        <li><span class="spm-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>{{ __('payment.included_item_3', ['days' => $trialDays]) }}</li>
                    </ul>

                    <div class="spm-badges">
                        <span class="spm-badge"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>{{ __('payment.ssl_badge') }}</span>
                        <span class="spm-badge"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>{{ __('payment.guarantee_badge') }}</span>
                    </div>
                </div>
            </div>

            {{-- ═════ RIGHT: PAYMENT FORM ═════ --}}
            <div class="spm-right">
                <div class="spm-price-header">
                    @if ($discountPct > 0)
                        <span class="spm-price-discount">-{{ $discountPct }}%</span>
                    @endif
                    <div class="spm-price-header-row">
                        <span class="spm-price-label">{{ __('payment.total_label') }}</span>
                        <span class="spm-price-values">
                            @if ($discountPct > 0)
                                <span class="spm-price-strike">{{ $subscriptionPriceFormatted }}</span>
                            @endif
                            <span class="spm-price-value">{{ $trialPriceFormatted }}</span>
                        </span>
                    </div>
                    <p class="spm-price-footnote">{{ __('payment.full_price_label', ['price' => $subscriptionPriceFormatted]) }} &middot; {{ __('payment.promo_label') }}</p>
                </div>

                <form id="spm-form" class="spm-form" novalidate>
                    @csrf

                    @guest
                        <div class="spm-field">
                            <label for="spm-name">{{ __('payment.form_full_name') }}</label>
                            <input type="text" id="spm-name" autocomplete="name" required>
                        </div>
                        <div class="spm-field">
                            <label for="spm-email">{{ __('payment.form_email') }}</label>
                            <input type="email" id="spm-email" autocomplete="email" required>
                        </div>
                    @endguest

                    <div class="spm-field">
                        <label for="spm-cardholder">{{ __('payment.form_cardholder') }}</label>
                        <input type="text" id="spm-cardholder" autocomplete="cc-name" required>
                    </div>

                    <div class="spm-field">
                        <label>
                            {{ __('payment.form_card_number') }}
                            <span class="spm-enc">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                {{ __('payment.form_encrypted') }}
                            </span>
                        </label>
                        <div id="spm-card-number" class="spm-stripe"></div>
                    </div>

                    <div class="spm-field-grid">
                        <div class="spm-field">
                            <label>{{ __('payment.form_expiry') }}</label>
                            <div id="spm-card-expiry" class="spm-stripe"></div>
                        </div>
                        <div class="spm-field">
                            <label>CVC</label>
                            <div id="spm-card-cvc" class="spm-stripe"></div>
                        </div>
                    </div>

                    <div id="spm-error" class="spm-error" hidden></div>

                    <button type="submit" id="spm-submit" class="spm-submit">
                        <svg id="spm-submit-lock" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span id="spm-submit-text">{{ __('payment.pay_button', ['price' => $trialPriceFormatted]) }}</span>
                        <svg id="spm-submit-spinner" hidden class="spm-spin" width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-opacity="0.25"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </button>

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

                    <p class="spm-bank-note">{{ __('payment.bank_statement', ['name' => 'sofortpdf.com']) }}</p>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
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
    .spm-panel {
        position: relative;
        width: 100%; max-width: 860px;
        max-height: 96vh;
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 24px 60px -12px rgba(15, 23, 42, 0.35);
        overflow: hidden;
        display: flex; flex-direction: column;
        transform: translateY(20px) scale(0.98);
        transition: transform 260ms cubic-bezier(0.23, 1, 0.32, 1);
        margin: 0 16px;
    }
    .spm-root.spm-open .spm-panel {
        transform: translateY(0) scale(1);
    }
    @media (max-width: 640px) {
        .spm-panel {
            max-height: 100vh;
            border-radius: 1rem 1rem 0 0;
            margin: 0;
            align-self: flex-end;
        }
        .spm-root { align-items: flex-end; }
    }
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

    /* Step progress */
    .spm-steps {
        display: flex; align-items: center; justify-content: center;
        gap: 10px;
        padding: 22px 28px 18px;
    }
    .spm-step {
        display: flex; align-items: center; gap: 8px;
        color: #94a3b8; font-size: 12px; font-weight: 500;
    }
    .spm-step.is-current { color: #0f172a; font-weight: 700; }
    .spm-step-dot {
        width: 26px; height: 26px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700;
        background: #f1f5f9; color: #94a3b8;
        border: 1.5px solid transparent;
    }
    .spm-step.is-current .spm-step-dot {
        background: #dcfce7; color: #047857;
        border-color: #86efac;
    }
    .spm-step-icon { color: currentColor; }
    .spm-step-bar {
        width: 28px; height: 1.5px;
        background: linear-gradient(to right, #cbd5e1 50%, transparent 50%);
        background-size: 8px 100%;
    }
    @media (max-width: 640px) {
        .spm-step-label { display: none; }
        .spm-step-bar { width: 18px; }
    }

    .spm-body {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        padding: 20px 28px 28px;
        overflow-y: auto;
    }
    @media (min-width: 720px) {
        .spm-body { grid-template-columns: 5fr 7fr; gap: 28px; }
    }

    /* Mobile preview toggle (hidden on desktop) */
    .spm-preview-toggle {
        display: none;
        width: calc(100% - 32px);
        margin: 0 16px;
        align-items: center; gap: 8px;
        padding: 10px 14px;
        background: #f1f5f9;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 10px;
        color: #334155;
        font-size: 13px; font-weight: 600;
        text-align: left;
        cursor: pointer;
        transition: background-color 160ms ease-out;
    }
    .spm-preview-toggle:hover { background: #e2e8f0; }
    .spm-toggle-chev {
        flex-shrink: 0;
        transition: transform 200ms var(--ease-out-expo);
    }
    .spm-preview-toggle[aria-expanded="true"] .spm-toggle-chev {
        transform: rotate(180deg);
    }
    .spm-toggle-filename {
        margin-left: auto;
        font-size: 11px; font-weight: 500;
        color: #64748b;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 40%;
    }
    /* Mobile-only left-column header with back button (desktop hides it).
       The header uses a back-arrow + label instead of an "×" so users
       don't confuse it with the main modal's close button (which kills
       the whole payment flow). */
    .spm-left-header {
        display: none;
        position: sticky; top: 0;
        padding: 12px 0 10px;
        background: #fff;
        margin-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        align-items: center; justify-content: space-between;
        gap: 12px;
        z-index: 2;
    }
    .spm-left-back {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 12px 8px 8px;
        border: 0; background: transparent;
        color: #3b6cf5;
        font-family: inherit; font-size: 13px; font-weight: 600;
        cursor: pointer;
        border-radius: 999px;
        transition: background-color 160ms ease-out;
    }
    .spm-left-back:hover { background: #eef4ff; }
    .spm-left-title {
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        font-size: 14px; font-weight: 700; color: #0f172a;
        padding-right: 4px;
    }

    @media (max-width: 719px) {
        .spm-preview-toggle { display: inline-flex; }
        .spm-body { padding-top: 12px; }
        .spm-left { display: none; }
        /* Expanded: overlay the whole panel so the form stays in place underneath */
        .spm-left.is-expanded {
            display: block;
            position: absolute;
            inset: 0;
            z-index: 5;
            background: #fff;
            padding: 0 20px 20px;
            overflow-y: auto;
            animation: spm-left-slide 260ms var(--ease-out-expo);
        }
        .spm-left.is-expanded .spm-left-header { display: flex; }
    }
    @keyframes spm-left-slide {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── LEFT COLUMN ── */
    .spm-preview-card {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 14px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .spm-preview {
        position: relative;
        aspect-ratio: 3 / 4;
        background: linear-gradient(180deg, #f8fafc 0%, #eef4ff 100%);
        border-radius: 10px;
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
    }
    .spm-preview-ribbon {
        position: absolute;
        top: 8px; right: 8px;
        padding: 3px 8px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 6px;
        font-size: 9px; font-weight: 800;
        letter-spacing: 0.08em;
        color: #334155;
        text-transform: uppercase;
        z-index: 2;
    }
    .spm-preview .spm-docx-preview {
        width: 100%; height: 100%;
        overflow: hidden;
        background: #fff;
    }
    /* docx-preview outputs pages; shrink to fit */
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
    .spm-preview-meta {
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }
    .spm-preview-name {
        font-size: 13px; font-weight: 600; color: #0f172a;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .spm-preview-size {
        font-size: 11px; color: #94a3b8; margin-top: 2px;
    }
    .spm-preview-count {
        font-size: 10px; color: #64748b; font-weight: 600;
        margin-top: 2px;
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

    .spm-included {
        list-style: none; padding: 10px 12px; margin: 0;
        background: #f8fafc;
        border-radius: 10px;
    }
    .spm-included li {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: #334155;
    }
    .spm-included li + li { margin-top: 7px; }
    .spm-check {
        flex: 0 0 16px; width: 16px; height: 16px;
        border-radius: 50%;
        background: #dcfce7; color: #059669;
        display: inline-flex; align-items: center; justify-content: center;
    }

    .spm-badges {
        display: flex; gap: 14px; justify-content: center;
        padding-top: 4px;
    }
    .spm-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10px; color: #94a3b8;
    }

    /* ── RIGHT COLUMN ── */
    .spm-price-header {
        position: relative;
        background: linear-gradient(135deg, #ecfdf5 0%, #dcfce7 100%);
        border: 1px solid rgba(16, 185, 129, 0.25);
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .spm-price-header-row {
        display: flex; justify-content: space-between; align-items: baseline;
    }
    .spm-price-label {
        font-size: 12px; color: #047857; font-weight: 600;
    }
    .spm-price-values {
        display: inline-flex; align-items: baseline; gap: 10px;
    }
    .spm-price-strike {
        font-size: 13px; color: #64748b; text-decoration: line-through;
        font-weight: 500;
    }
    .spm-price-value {
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        font-size: 24px; font-weight: 800; color: #059669;
    }
    .spm-price-discount {
        position: absolute;
        top: 0; right: 0;
        background: #dc2626;
        color: #fff;
        font-size: 10px; font-weight: 800;
        letter-spacing: 0.04em;
        padding: 3px 10px;
        border-bottom-left-radius: 10px;
        line-height: 1.3;
    }
    .spm-price-footnote {
        margin-top: 6px;
        font-size: 11px; color: #047857;
        opacity: 0.85;
    }

    .spm-form { display: flex; flex-direction: column; gap: 12px; }
    .spm-field label {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 12px; color: #334155; font-weight: 600;
        margin-bottom: 6px;
    }
    .spm-field input[type="text"],
    .spm-field input[type="email"],
    .spm-stripe {
        width: 100%; min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-family: inherit; font-size: 14px;
        background: #fff; color: #0f172a;
        transition: border-color 160ms ease-out, box-shadow 160ms ease-out;
        display: flex; align-items: center;
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

    .spm-enc {
        display: inline-flex; align-items: center; gap: 3px;
        font-size: 10px; color: #94a3b8; font-weight: 400;
    }
    .spm-field-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }

    .spm-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 12px; color: #b91c1c;
    }

    .spm-submit {
        width: 100%;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 14px 20px;
        background: #059669;
        color: #fff;
        border: 0;
        border-radius: 12px;
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        font-weight: 800;
        font-size: 15px;
        cursor: pointer;
        transition: transform 160ms cubic-bezier(0.23, 1, 0.32, 1),
                    background-color 180ms ease-out,
                    box-shadow 180ms ease-out;
        box-shadow: 0 6px 16px -4px rgba(5, 150, 105, 0.4);
    }
    .spm-submit:hover:not(:disabled) {
        background: #047857;
        transform: translateY(-1px);
        box-shadow: 0 10px 24px -6px rgba(5, 150, 105, 0.5);
    }
    .spm-submit:active:not(:disabled) { transform: scale(0.98); }
    .spm-submit:disabled { opacity: 0.6; cursor: not-allowed; }
    @keyframes spm-spin { to { transform: rotate(360deg); } }
    .spm-spin { animation: spm-spin 0.9s linear infinite; }

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

    .spm-bank-note {
        font-size: 10px; color: #94a3b8; text-align: center;
        margin-top: 4px;
    }

    body.spm-lock { overflow: hidden; }

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
    var __toggleLabels = {!! json_encode([
        'show' => __('payment.show_preview'),
        'hide' => __('payment.hide_preview'),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

    // --- DOM refs ----------------------------------------------------------
    var previewWrap    = root.querySelector('[data-spm-preview]');
    var previewExt     = root.querySelector('[data-spm-preview-ext]');
    var previewRibbon  = root.querySelector('[data-spm-preview-ribbon]');
    var leftCol        = root.querySelector('.spm-left');
    var toggleBtns     = root.querySelectorAll('[data-spm-preview-toggle]');
    var toggleBtn      = toggleBtns[0] || null; // the main trigger (above the body)
    var toggleLabel    = root.querySelector('[data-spm-toggle-label]');
    var toggleFilename = root.querySelector('[data-spm-toggle-filename]');
    var filenameEl     = root.querySelector('[data-spm-filename]');
    var filesizeEl     = root.querySelector('[data-spm-filesize]');
    var filecountEl    = root.querySelector('[data-spm-filecount]');
    var form           = root.querySelector('#spm-form');
    var errorEl        = root.querySelector('#spm-error');
    var submitBtn      = root.querySelector('#spm-submit');
    var submitText     = root.querySelector('#spm-submit-text');
    var submitLock     = root.querySelector('#spm-submit-lock');
    var submitSpinner  = root.querySelector('#spm-submit-spinner');
    var nameInput      = root.querySelector('#spm-name');        // guest-only field
    var emailInput     = root.querySelector('#spm-email');       // guest-only field
    var cardholder     = root.querySelector('#spm-cardholder');
    var tcCheckbox     = root.querySelector('#spm-tc');

    // --- State -------------------------------------------------------------
    var stripe = null;
    var elements = null;
    var cardNumberEl = null;
    var cardExpiryEl = null;
    var cardCvcEl = null;
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
        // Keep the ribbon across renders — buildSinglePreviewHtml replaces
        // the inner content only.
        var ribbonHtml = previewRibbon ? previewRibbon.outerHTML : '';

        // Single-file mode
        if (files.length === 1) {
            previewWrap.classList.remove('spm-preview-stack');
            previewWrap.innerHTML = ribbonHtml + await buildSinglePreviewHtml(files[0]);
            // Re-bind the ref since we just replaced the DOM node.
            previewRibbon = previewWrap.querySelector('[data-spm-preview-ribbon]');
            return;
        }

        // Multi-file: stacked grid (up to 5 visible, extra as "+N" tile)
        previewWrap.classList.add('spm-preview-stack');
        var maxVisible = 5;
        var visible = files.slice(0, maxVisible);
        var remaining = files.length - visible.length;

        previewWrap.innerHTML = ribbonHtml + visible.map(function(f, i) {
            return '<div class="spm-stack-item" data-i="' + i + '">' +
                        '<span class="spm-stack-badge">' + (i + 1) + '</span>' +
                        '<div class="spm-stack-body">' +
                            '<span class="spm-stack-ext">' + extensionFrom(f.name).toUpperCase() + '</span>' +
                        '</div>' +
                   '</div>';
        }).join('') + (remaining > 0
            ? '<div class="spm-stack-item spm-stack-more">+' + remaining + '</div>'
            : '');

        // Re-bind ribbon ref after innerHTML replacement
        previewRibbon = previewWrap.querySelector('[data-spm-preview-ribbon]');

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
        // IMPORTANT: canvas pixel data does not survive outerHTML/innerHTML
        // serialization. Export as a data URL and embed as an <img> so
        // whichever innerHTML consumer picks this up (single-file path or
        // multi-file stack) gets the actual rendered page, not an empty
        // canvas of the right dimensions.
        return '<img src="' + canvas.toDataURL('image/png') + '" alt="">';
    }

    // Lazy-load PDF.js if not already present (merge tool pre-loads it, but
    // other tool pages don't). Uses the same CDN build + worker URL.
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

    // Lazy-load docx-preview and render the first page of a .docx file
    // into a detached container, returning its outerHTML string (same
    // contract as renderPdfPage1).
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
        // Render into a detached host, then inline the result as the preview.
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
        // Keep only the first page — docx-preview renders all pages.
        var pages = host.querySelectorAll('.docx > section, .docx > .docx_page');
        if (pages.length > 1) {
            for (var i = 1; i < pages.length; i++) pages[i].remove();
        }
        // Auto-scale so the page fits the tile width.
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
    // Stripe rejects many characters; Greek, German, Romanian, Polish and
    // Arabic speakers hit this often. Simplified from contract-kit.
    function sanitizeStripeName(input) {
        if (!input) return '';
        // NFD decomposition + strip combining marks
        var s = String(input).normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        // German
        s = s.replace(/ß/g, 'ss')
             .replace(/Ä/g, 'Ae').replace(/ä/g, 'ae')
             .replace(/Ö/g, 'Oe').replace(/ö/g, 'oe')
             .replace(/Ü/g, 'Ue').replace(/ü/g, 'ue');
        // Romanian remnants (post-NFD) that NFD didn't strip
        s = s.replace(/[Șș]/g, 's').replace(/[Țț]/g, 't');
        // Polish
        s = s.replace(/[Łł]/g, function(c) { return c === 'Ł' ? 'L' : 'l'; });
        // Strip any remaining non-ASCII letters / numbers / space / . - '
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

    async function initStripe() {
        if (stripeReady || !__config.stripeKey) return;
        await ensureStripeLoaded();
        stripe = window.Stripe(__config.stripeKey);
        elements = stripe.elements({ locale: __m.stripeLocale });
        var style = {
            base: {
                fontFamily: '"DM Sans", system-ui, sans-serif',
                fontSize: '14px',
                color: '#0f172a',
                '::placeholder': { color: '#94a3b8' },
            },
            invalid: { color: '#ef4444' },
        };
        cardNumberEl = elements.create('cardNumber', { style: style, showIcon: true });
        cardExpiryEl = elements.create('cardExpiry', { style: style });
        cardCvcEl    = elements.create('cardCvc',    { style: style });
        cardNumberEl.mount(root.querySelector('#spm-card-number'));
        cardExpiryEl.mount(root.querySelector('#spm-card-expiry'));
        cardCvcEl.mount(root.querySelector('#spm-card-cvc'));
        [cardNumberEl, cardExpiryEl, cardCvcEl].forEach(function(el) {
            el.on('focus', function(e) { /* class handled by Stripe */ });
            el.on('change', function(event) {
                if (event.error) showError(event.error.message);
                else hideError();
            });
        });
        stripeReady = true;
    }

    // --- Submit handler ----------------------------------------------------
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        hideError();

        if (!tcCheckbox.checked) { showError(__m.tcRequired); return; }
        var cardName = cardholder.value.trim();
        if (!cardName) { showError(__m.errName); return; }

        var accountName = nameInput ? nameInput.value.trim() : '';
        var accountEmail = emailInput ? emailInput.value.trim() : '';

        if (nameInput && !accountName) { showError(__m.errName); return; }
        if (emailInput && (!accountEmail || accountEmail.indexOf('@') < 0)) { showError(__m.errEmail); return; }

        setLoading(true);
        try {
            var sanitizedCardName = sanitizeStripeName(cardName) || cardName;
            var sanitizedAccountName = sanitizeStripeName(accountName) || accountName;

            var pm = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumberEl,
                billing_details: { name: sanitizedCardName, email: accountEmail || undefined },
            });
            if (pm.error) { showError(pm.error.message); setLoading(false); return; }

            var payload = { payment_method_id: pm.paymentMethod.id };
            if (sanitizedAccountName) payload.name = sanitizedAccountName;
            if (accountEmail) payload.email = accountEmail;
            @auth
                // /checkout/create-subscription requires email/name even for
                // logged-in users (validation), so populate from the server.
                payload.email = @json(auth()->user()->email ?? '');
                payload.name  = @json(auth()->user()->name ?? '');
            @endauth

            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            var res = await fetch('/checkout/create-subscription', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(payload),
            });
            var data = await res.json();

            if (data.error) { showError(data.error); setLoading(false); return; }

            if (data.requires_action) {
                var confirmRes = await stripe.confirmCardPayment(data.client_secret, {
                    payment_method: pm.paymentMethod.id,
                });
                if (confirmRes.error) { showError(confirmRes.error.message); setLoading(false); return; }

                var confirm = await fetch('/checkout/confirm-payment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ subscription_id: data.subscription_id }),
                });
                var confirmData = await confirm.json();
                if (confirmData.error) { showError(confirmData.error); setLoading(false); return; }
            } else if (!data.success) {
                showError(__m.errGeneric);
                setLoading(false);
                return;
            }

            // Payment succeeded — mark the flag + close modal + hand control back.
            window.__sofortpdfTrialJustPaid = true;
            setLoading(false);
            var cb = onSuccessCb;
            close(true); // silent close (skip onClose callback)
            if (typeof cb === 'function') cb();
        } catch (err) {
            showError(__m.errGeneric);
            setLoading(false);
        }
    });

    // --- Open / close ------------------------------------------------------
    function open(options) {
        options = options || {};
        onSuccessCb = options.onSuccess || null;
        onCloseCb = options.onClose || null;

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
        if (cardholder) cardholder.value = '';
        if (nameInput)  nameInput.value  = options.defaultName  || '';
        if (emailInput) emailInput.value = options.defaultEmail || '';

        // Collapse the mobile preview by default — user taps the toggle to
        // reveal it. Desktop ignores this class (CSS always shows .spm-left).
        if (leftCol) leftCol.classList.remove('is-expanded');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        if (toggleLabel) toggleLabel.textContent = __toggleLabels.show;

        // Label: single file → name + size. Multi → "First-file.pdf + N more".
        if (files.length === 0) {
            filenameEl.textContent = options.filename || '';
            filesizeEl.textContent = options.fileSize ? formatSize(options.fileSize) : '';
            filecountEl.hidden = true;
            previewWrap.classList.remove('spm-preview-stack');
            previewWrap.innerHTML =
                '<div class="spm-preview-placeholder">' +
                    '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>' +
                    '<span class="spm-preview-ext">' + (extensionFrom(options.filename || '') || 'FILE').toUpperCase() + '</span>' +
                '</div>';
        } else if (files.length === 1) {
            filenameEl.textContent = files[0].name;
            filesizeEl.textContent = formatSize(files[0].size);
            filecountEl.hidden = true;
            if (previewRibbon) previewRibbon.textContent = (extensionFrom(files[0].name) || 'file').toUpperCase();
            if (toggleFilename) toggleFilename.textContent = files[0].name;
            renderPreview(files).catch(function() { /* ignore */ });
        } else {
            var totalSize = files.reduce(function(sum, f) { return sum + (f.size || 0); }, 0);
            filenameEl.textContent = files[0].name;
            filesizeEl.textContent = formatSize(totalSize);
            filecountEl.textContent = (__filesCountLabel || '{n} files').replace('{n}', files.length);
            filecountEl.hidden = false;
            if (previewRibbon) previewRibbon.textContent = files.length + '×';
            if (toggleFilename) toggleFilename.textContent = files[0].name + ' +' + (files.length - 1);
            renderPreview(files).catch(function() { /* ignore */ });
        }

        root.classList.add('spm-open');
        root.setAttribute('aria-hidden', 'false');
        document.body.classList.add('spm-lock');

        initStripe().catch(function(e) { showError(__m.errGeneric); });
    }

    function close(silent) {
        root.classList.remove('spm-open');
        root.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('spm-lock');
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

    // Mobile preview toggle — opens the preview as an overlay on top of the
    // payment form. Any element with data-spm-preview-toggle triggers it
    // (the main button above the body and the close button inside the
    // overlay header both share the attribute). Hidden on desktop via CSS.
    if (leftCol && toggleBtns.length) {
        toggleBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var expanded = leftCol.classList.toggle('is-expanded');
                if (toggleBtn) toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                if (toggleLabel) {
                    toggleLabel.textContent = expanded ? __toggleLabels.hide : __toggleLabels.show;
                }
                if (expanded) {
                    // Scroll the overlay to the top so the preview is the first thing the user sees.
                    leftCol.scrollTop = 0;
                }
            });
        });
    }

    // Public API
    window.SofortpdfPaymentModal = {
        open: open,
        close: close,
    };
})();
</script>
