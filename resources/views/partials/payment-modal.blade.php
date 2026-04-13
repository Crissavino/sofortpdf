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
@endphp

<div id="sofortpdf-payment-modal" class="spm-root" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="spm-backdrop" data-spm-close></div>

    <div class="spm-panel" role="document">
        <button type="button" class="spm-close" data-spm-close aria-label="{{ __('payment.close_button') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
        </button>

        <div class="spm-header">
            <h2 class="spm-title">{{ __('payment.heading') }}</h2>
            <p class="spm-sub">{{ __('payment.subheading', ['days' => $trialDays, 'price' => $trialPriceFormatted]) }}</p>
        </div>

        <div class="spm-body">
            {{-- ═════ LEFT: FILE PREVIEW + SUMMARY ═════ --}}
            <div class="spm-left">
                <div class="spm-preview-card">
                    <div class="spm-preview" data-spm-preview>
                        {{-- Placeholder shown until JS injects real preview --}}
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
                    </div>
                </div>

                <div class="spm-summary">
                    <div class="spm-summary-row">
                        <span class="spm-summary-label">{{ __('payment.summary_title') }}</span>
                        <span class="spm-summary-value">{{ $trialPriceFormatted }}</span>
                    </div>
                    <div class="spm-summary-row spm-summary-total">
                        <span>{{ __('payment.total_label') }}</span>
                        <span class="spm-summary-price">{{ $trialPriceFormatted }}</span>
                    </div>
                    <p class="spm-summary-footnote">{{ __('payment.full_price_label', ['price' => $subscriptionPriceFormatted]) }}</p>
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

            {{-- ═════ RIGHT: PAYMENT FORM ═════ --}}
            <div class="spm-right">
                <div class="spm-price-header">
                    <span class="spm-price-label">{{ __('payment.total_label') }}</span>
                    <span class="spm-price-value">{{ $trialPriceFormatted }}</span>
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

    .spm-header {
        padding: 24px 28px 8px;
        text-align: center;
    }
    .spm-title {
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;
    }
    .spm-sub {
        margin-top: 4px;
        font-size: 13px; color: #64748b;
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

    /* ── LEFT COLUMN ── */
    .spm-preview-card {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 14px;
        padding: 14px;
    }
    .spm-preview {
        position: relative;
        aspect-ratio: 3 / 4;
        background: linear-gradient(180deg, #f8fafc 0%, #eef4ff 100%);
        border-radius: 10px;
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
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

    .spm-summary {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 14px;
        padding: 14px;
        margin-top: 12px;
    }
    .spm-summary-row {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 13px; color: #475569;
    }
    .spm-summary-row + .spm-summary-row {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }
    .spm-summary-total { font-size: 15px; font-weight: 700; color: #0f172a; }
    .spm-summary-price { color: #059669; font-weight: 800; }
    .spm-summary-footnote {
        margin-top: 8px;
        font-size: 11px; color: #94a3b8;
        text-align: right;
    }

    .spm-included {
        list-style: none; padding: 12px 14px; margin: 12px 0 0;
        background: #f8fafc;
        border-radius: 14px;
    }
    .spm-included li {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: #334155;
    }
    .spm-included li + li { margin-top: 8px; }
    .spm-check {
        flex: 0 0 16px; width: 16px; height: 16px;
        border-radius: 50%;
        background: #dcfce7; color: #059669;
        display: inline-flex; align-items: center; justify-content: center;
    }

    .spm-badges {
        display: flex; gap: 14px; justify-content: center;
        margin-top: 12px;
    }
    .spm-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10px; color: #94a3b8;
    }

    /* ── RIGHT COLUMN ── */
    .spm-price-header {
        background: linear-gradient(135deg, #ecfdf5 0%, #dcfce7 100%);
        border: 1px solid rgba(16, 185, 129, 0.25);
        border-radius: 14px;
        padding: 12px 16px;
        display: flex; justify-content: space-between; align-items: baseline;
        margin-bottom: 16px;
    }
    .spm-price-label {
        font-size: 12px; color: #047857; font-weight: 600;
    }
    .spm-price-value {
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        font-size: 24px; font-weight: 800; color: #059669;
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
    var __m = @json([
        'stripeLocale'    => $loc,
        'processing'      => __('payment.processing'),
        'payButton'       => __('payment.pay_button', ['price' => $trialPriceFormatted]),
        'errName'         => __('payment.err_name'),
        'errEmail'        => __('payment.err_email'),
        'errGeneric'      => __('payment.err_generic'),
        'tcRequired'      => __('payment.tc_required'),
    ]);
    var __config = @json([
        'stripeKey'        => (string) config('services.stripe.key', ''),
    ]);

    // --- DOM refs ----------------------------------------------------------
    var previewWrap    = root.querySelector('[data-spm-preview]');
    var previewExt     = root.querySelector('[data-spm-preview-ext]');
    var filenameEl     = root.querySelector('[data-spm-filename]');
    var filesizeEl     = root.querySelector('[data-spm-filesize]');
    var form           = root.querySelector('#spm-form');
    var errorEl        = root.querySelector('#spm-error');
    var submitBtn      = root.querySelector('#spm-submit');
    var submitText     = root.querySelector('#spm-submit-text');
    var submitLock     = root.querySelector('#spm-submit-lock');
    var submitSpinner  = root.querySelector('#spm-submit-spinner');
    var nameInput      = root.querySelector('#spm-name');        // @guest only
    var emailInput     = root.querySelector('#spm-email');       // @guest only
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

    // --- Preview builders (mirrored from merge grid) -----------------------
    async function renderPreview(file, filename) {
        if (!file) {
            previewWrap.innerHTML = placeholderHtml(extensionFrom(filename));
            return;
        }
        var ext = extensionFrom(file.name || filename);

        if (['jpg','jpeg','png','gif','webp','bmp'].indexOf(ext) !== -1) {
            var url = URL.createObjectURL(file);
            previewWrap.innerHTML = '<img src="' + url + '" alt="">';
            return;
        }
        if (ext === 'pdf' && window['pdfjs-dist/build/pdf']) {
            try {
                var dataHtml = await renderPdfPage1(file);
                previewWrap.innerHTML = dataHtml;
                return;
            } catch (e) { /* fall through */ }
        }
        previewWrap.innerHTML = placeholderHtml(ext);
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
        var pdfjs = window['pdfjs-dist/build/pdf'];
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
        canvas.style.width = '100%'; canvas.style.height = 'auto';
        await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
        return canvas.outerHTML;
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
            var pm = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumberEl,
                billing_details: { name: cardName, email: accountEmail || undefined },
            });
            if (pm.error) { showError(pm.error.message); setLoading(false); return; }

            var payload = { payment_method_id: pm.paymentMethod.id };
            if (accountName) payload.name = accountName;
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

        // Reset UI
        hideError();
        setLoading(false);
        tcCheckbox.checked = false;
        if (cardholder) cardholder.value = '';
        if (nameInput)  nameInput.value  = options.defaultName  || '';
        if (emailInput) emailInput.value = options.defaultEmail || '';

        // Preview + filename
        filenameEl.textContent = options.filename || '';
        filesizeEl.textContent = options.fileSize ? formatSize(options.fileSize) : '';
        previewExt.textContent = (extensionFrom(options.filename || '') || 'file').toUpperCase();
        renderPreview(options.file || null, options.filename || '').catch(function() { /* ignore */ });

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

    // Public API
    window.SofortpdfPaymentModal = {
        open: open,
        close: close,
    };
})();
</script>
