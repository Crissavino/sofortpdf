{{-- =====================================================================
     Fake conversion loading modal — vanilla JS, single instance per page.

     Rendered once via layouts/app.blade.php. Hidden by default. Driven
     imperatively via window.SofortpdfLoadingModal.run({ duration, onDone })
     and window.SofortpdfLoadingModal.hide().

     Cosmetic only — runs a 3.5s progress animation revealing 3 step
     labels at 25% / 55% / 100%, then calls onDone. Used to give paywall
     users the perception that their document is being processed before
     the payment modal appears (mirrors the conversie-pdf pattern).

     Paid users do NOT see this — their flow uses the real `processing-state`
     in tools/show.blade.php (3-dots animation while /api/convert runs).
     ===================================================================== --}}

<div id="sofortpdf-loading-modal" class="slm-root" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="slm-backdrop"></div>
    <div class="slm-panel" role="document">
        <p class="slm-title">{{ __('tool.fake_loading_title') }}</p>

        <div class="slm-percent" data-slm-percent>0 %</div>

        <div class="slm-progress-wrap">
            <div class="slm-progress-bar" data-slm-bar></div>
        </div>

        <div class="slm-body">
            <div class="slm-icon">
                <div class="slm-doc-frame">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="14" y="6" width="36" height="48" rx="4" stroke="currentColor" stroke-width="3"/>
                        <line x1="22" y1="20" x2="42" y2="20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="22" y1="28" x2="42" y2="28" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="22" y1="36" x2="36" y2="36" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="slm-scan-line"></div>
            </div>

            <ul class="slm-steps">
                <li class="slm-step" data-slm-step="1">
                    <span class="slm-step-check">
                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="10" cy="10" r="9" fill="currentColor"/>
                            <path d="M6 10.5l2.5 2.5L14 7.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="slm-step-label">{{ __('tool.fake_loading_step_1') }}</span>
                </li>
                <li class="slm-step" data-slm-step="2">
                    <span class="slm-step-check">
                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="10" cy="10" r="9" fill="currentColor"/>
                            <path d="M6 10.5l2.5 2.5L14 7.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="slm-step-label">{{ __('tool.fake_loading_step_2') }}</span>
                </li>
                <li class="slm-step" data-slm-step="3">
                    <span class="slm-step-check">
                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="10" cy="10" r="9" fill="currentColor"/>
                            <path d="M6 10.5l2.5 2.5L14 7.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="slm-step-label">{{ __('tool.fake_loading_step_3') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
    .slm-root {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .slm-root.slm-open { display: flex; }

    .slm-backdrop {
        position: absolute;
        inset: 0;
        background-color: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .slm-panel {
        position: relative;
        background: #ffffff;
        border-radius: 24px;
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
        opacity: 0;
        transform: translateY(8px) scale(0.98);
        transition: opacity 200ms ease-out, transform 200ms ease-out;
    }
    .slm-root.slm-open .slm-panel {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .slm-title {
        text-align: center;
        font-size: 0.95rem;
        color: #475569;
        margin: 0 0 1.25rem;
        font-weight: 500;
    }

    .slm-percent {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--brand-600, #4f46e5);
        line-height: 1;
        margin-bottom: 1rem;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }

    .slm-progress-wrap {
        height: 12px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .slm-progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, var(--brand-500, #6366f1), var(--brand-600, #4f46e5));
        border-radius: 999px;
        transition: width 80ms linear;
    }

    .slm-body {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .slm-icon {
        position: relative;
        flex-shrink: 0;
        width: 88px;
        height: 88px;
        color: #334155;
    }

    .slm-doc-frame {
        width: 100%;
        height: 100%;
    }

    .slm-doc-frame svg { width: 100%; height: 100%; }

    .slm-scan-line {
        position: absolute;
        left: 8%;
        right: 8%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #10b981 30%, #10b981 70%, transparent);
        border-radius: 999px;
        top: 20%;
        animation: slm-scan 1.6s ease-in-out infinite;
    }

    @keyframes slm-scan {
        0%   { top: 20%; opacity: 0.3; }
        50%  { top: 70%; opacity: 1; }
        100% { top: 20%; opacity: 0.3; }
    }

    .slm-steps {
        list-style: none;
        margin: 0;
        padding: 0;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
    }

    .slm-step {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        opacity: 0.35;
        transition: opacity 400ms ease-out;
    }

    .slm-step.slm-step-active { opacity: 1; }

    .slm-step-check {
        flex-shrink: 0;
        width: 22px;
        height: 22px;
        color: #cbd5e1;
        transition: color 400ms ease-out;
    }

    .slm-step.slm-step-active .slm-step-check { color: #10b981; }

    .slm-step-check svg { width: 100%; height: 100%; display: block; }

    .slm-step-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #334155;
    }

    @media (max-width: 480px) {
        .slm-panel { padding: 2rem 1.25rem; }
        .slm-percent { font-size: 2rem; }
        .slm-body { flex-direction: column; gap: 1.25rem; align-items: flex-start; }
        .slm-icon { align-self: center; }
    }
</style>

<script>
(function() {
    if (window.SofortpdfLoadingModal) return; // idempotent

    var root = document.getElementById('sofortpdf-loading-modal');
    if (!root) return;

    var percentEl = root.querySelector('[data-slm-percent]');
    var barEl     = root.querySelector('[data-slm-bar]');
    var stepEls   = Array.from(root.querySelectorAll('[data-slm-step]'));

    var rafId = null;

    function reset() {
        percentEl.textContent = '0 %';
        barEl.style.width = '0%';
        stepEls.forEach(function(el) { el.classList.remove('slm-step-active'); });
    }

    function show() {
        reset();
        root.setAttribute('aria-hidden', 'false');
        root.classList.add('slm-open');
    }

    function hide() {
        root.classList.remove('slm-open');
        root.setAttribute('aria-hidden', 'true');
        if (rafId) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    function run(opts) {
        opts = opts || {};
        var duration = typeof opts.duration === 'number' ? opts.duration : 3500;
        var onDone   = typeof opts.onDone === 'function' ? opts.onDone : function() {};

        show();

        var start = performance.now();

        function tick(now) {
            var elapsed = now - start;
            var pct = Math.min(100, (elapsed / duration) * 100);

            percentEl.textContent = Math.round(pct) + ' %';
            barEl.style.width = pct + '%';

            if (pct >= 0)  stepEls[0].classList.add('slm-step-active');
            if (pct >= 25) stepEls[1].classList.add('slm-step-active');
            if (pct >= 55) stepEls[2].classList.add('slm-step-active');

            if (pct < 100) {
                rafId = requestAnimationFrame(tick);
            } else {
                rafId = null;
                onDone();
            }
        }

        rafId = requestAnimationFrame(tick);
    }

    window.SofortpdfLoadingModal = { run: run, hide: hide };
})();
</script>
