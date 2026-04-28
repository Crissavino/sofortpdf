{{-- =====================================================================
     Fake conversion loading modal — vanilla JS, single instance per page.

     Rendered once via layouts/app.blade.php. Hidden by default. Driven
     imperatively via:
         window.SofortpdfLoadingModal.run({ files, duration, onDone })
         window.SofortpdfLoadingModal.hide()

     Cosmetic only — runs a 3.5s progress animation revealing 3 step
     labels at 25% / 55% / 100%, then calls onDone. Used to give paywall
     users the perception that their document is being processed before
     the payment modal appears (mirrors the conversie-pdf pattern).

     Paid users do NOT see this — their flow uses the real `processing-state`
     in tools/show.blade.php (3-dots animation while /api/convert runs).

     File preview: `files` is the array of File objects the user uploaded.
     Renders a pill (extension badge + filename + size, "+N" suffix for
     multi-file) and a body thumbnail (real first-page render via pdf.js
     for PDFs, <img> blob for images, large extension badge for others).
     Thumbnail render is async and non-blocking — the modal animates the
     progress bar regardless of whether the thumbnail finished.
     ===================================================================== --}}

<div id="sofortpdf-loading-modal" class="slm-root" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="slm-backdrop"></div>
    <div class="slm-panel" role="document">
        <p class="slm-title">{{ __('tool.fake_loading_title') }}</p>

        <div class="slm-pill" data-slm-pill hidden>
            <div class="slm-pill-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span class="slm-pill-ext" data-slm-pill-ext>FILE</span>
            </div>
            <div class="slm-pill-meta">
                <p class="slm-pill-name" data-slm-pill-name>—</p>
                <p class="slm-pill-size" data-slm-pill-size>—</p>
            </div>
            <span class="slm-pill-badge" data-slm-pill-badge hidden>+0</span>
        </div>

        <div class="slm-percent" data-slm-percent>0 %</div>

        <div class="slm-progress-wrap">
            <div class="slm-progress-bar" data-slm-bar></div>
        </div>

        <div class="slm-body">
            <div class="slm-thumb" data-slm-thumb>
                {{-- Default: generic doc placeholder with scan line.
                     Replaced at run() with image / pdf canvas / ext badge. --}}
                <div class="slm-thumb-placeholder" data-slm-thumb-placeholder>
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

    /* ── File pill (filename + size + extension badge) ── */
    .slm-pill {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.625rem 0.875rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        margin-bottom: 1.25rem;
    }
    .slm-pill[hidden] { display: none; }

    .slm-pill-icon {
        position: relative;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slm-pill-icon svg { width: 100%; height: 100%; }

    .slm-pill-ext {
        position: absolute;
        bottom: 2px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.55rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        color: #ffffff;
        background: #64748b;
        padding: 1px 4px;
        border-radius: 3px;
        text-transform: uppercase;
        line-height: 1;
        max-width: 32px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .slm-pill-ext.ext-pdf { background: #ef4444; }
    .slm-pill-ext.ext-doc, .slm-pill-ext.ext-docx { background: #2563eb; }
    .slm-pill-ext.ext-xls, .slm-pill-ext.ext-xlsx { background: #16a34a; }
    .slm-pill-ext.ext-ppt, .slm-pill-ext.ext-pptx { background: #ea580c; }
    .slm-pill-ext.ext-jpg, .slm-pill-ext.ext-jpeg, .slm-pill-ext.ext-png,
    .slm-pill-ext.ext-gif, .slm-pill-ext.ext-webp, .slm-pill-ext.ext-bmp { background: #8b5cf6; }

    .slm-pill-meta {
        flex-grow: 1;
        min-width: 0;
    }

    .slm-pill-name {
        margin: 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .slm-pill-size {
        margin: 2px 0 0;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .slm-pill-badge {
        flex-shrink: 0;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        background: #e2e8f0;
        padding: 0.25rem 0.5rem;
        border-radius: 999px;
    }
    .slm-pill-badge[hidden] { display: none; }

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

    /* ── Thumb container (PDF canvas / img / extension placeholder) ── */
    .slm-thumb {
        position: relative;
        flex-shrink: 0;
        width: 96px;
        height: 124px;
        border-radius: 8px;
        overflow: hidden;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
    }

    .slm-thumb canvas,
    .slm-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        opacity: 0;
        animation: slm-fade-in 240ms ease-out forwards;
    }

    @keyframes slm-fade-in {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .slm-thumb-placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slm-thumb-placeholder svg {
        width: 60%;
        height: 60%;
    }

    .slm-thumb-ext-large {
        position: absolute;
        bottom: 6px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        color: #ffffff;
        background: #64748b;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        line-height: 1;
        max-width: 80%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .slm-thumb-ext-large.ext-pdf { background: #ef4444; }
    .slm-thumb-ext-large.ext-doc, .slm-thumb-ext-large.ext-docx { background: #2563eb; }
    .slm-thumb-ext-large.ext-xls, .slm-thumb-ext-large.ext-xlsx { background: #16a34a; }
    .slm-thumb-ext-large.ext-ppt, .slm-thumb-ext-large.ext-pptx { background: #ea580c; }

    .slm-scan-line {
        position: absolute;
        left: 8%;
        right: 8%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #10b981 30%, #10b981 70%, transparent);
        border-radius: 999px;
        top: 20%;
        animation: slm-scan 1.6s ease-in-out infinite;
        pointer-events: none;
        z-index: 2;
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
        .slm-thumb { align-self: center; }
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

    var pillEl       = root.querySelector('[data-slm-pill]');
    var pillExtEl    = root.querySelector('[data-slm-pill-ext]');
    var pillNameEl   = root.querySelector('[data-slm-pill-name]');
    var pillSizeEl   = root.querySelector('[data-slm-pill-size]');
    var pillBadgeEl  = root.querySelector('[data-slm-pill-badge]');

    var thumbEl            = root.querySelector('[data-slm-thumb]');
    var thumbPlaceholderEl = root.querySelector('[data-slm-thumb-placeholder]');

    var rafId = null;
    var thumbToken = 0; // Cancels stale async thumbnail renders.

    function reset() {
        percentEl.textContent = '0 %';
        barEl.style.width = '0%';
        stepEls.forEach(function(el) { el.classList.remove('slm-step-active'); });
        pillEl.setAttribute('hidden', '');
        pillBadgeEl.setAttribute('hidden', '');
        pillExtEl.className = 'slm-pill-ext';
        clearThumb();
    }

    function clearThumb() {
        // Drop any rendered <canvas>, <img>, or extension badge — leave
        // only the placeholder + scan line.
        var nodes = thumbEl.querySelectorAll('canvas, img, .slm-thumb-ext-large');
        nodes.forEach(function(n) { n.remove(); });
        if (thumbPlaceholderEl) thumbPlaceholderEl.style.display = '';
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
        thumbToken++; // Invalidate any in-flight render.
    }

    function formatSize(bytes) {
        if (typeof bytes !== 'number' || !isFinite(bytes)) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    function getExt(name) {
        var s = String(name || '');
        var i = s.lastIndexOf('.');
        if (i < 0 || i === s.length - 1) return '';
        return s.slice(i + 1).toLowerCase();
    }

    function populatePill(files) {
        if (!files || !files.length) return;

        var first = files[0];
        var ext = getExt(first.name);

        pillExtEl.className = 'slm-pill-ext' + (ext ? ' ext-' + ext : '');
        pillExtEl.textContent = ext ? ext.toUpperCase().slice(0, 4) : 'FILE';

        pillNameEl.textContent = first.name || '';
        pillSizeEl.textContent = formatSize(first.size);

        if (files.length > 1) {
            pillBadgeEl.textContent = '+' + (files.length - 1);
            pillBadgeEl.removeAttribute('hidden');
        }

        pillEl.removeAttribute('hidden');
    }

    function appendThumbNode(node, token) {
        if (token !== thumbToken) return; // Stale render — discarded.
        if (thumbPlaceholderEl) thumbPlaceholderEl.style.display = 'none';
        thumbEl.appendChild(node);
    }

    function appendExtBadge(ext) {
        var badge = document.createElement('span');
        badge.className = 'slm-thumb-ext-large' + (ext ? ' ext-' + ext : '');
        badge.textContent = ext ? ext.toUpperCase() : 'FILE';
        thumbEl.appendChild(badge);
    }

    async function renderThumb(file) {
        var token = ++thumbToken;
        var ext = getExt(file && file.name);

        // Always show the extension badge as a fallback overlay until
        // the better preview lands. Lives next to the placeholder under
        // the scan line.
        appendExtBadge(ext);

        // Image files: show the blob directly.
        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].indexOf(ext) >= 0) {
            try {
                var img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = '';
                appendThumbNode(img, token);
                // Once the image is in, hide the redundant ext badge.
                var badge = thumbEl.querySelector('.slm-thumb-ext-large');
                if (badge) badge.remove();
            } catch (e) { /* keep ext badge */ }
            return;
        }

        // PDFs: render first page via pdf.js.
        if (ext === 'pdf') {
            var pdfjs = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
            if (!pdfjs || !pdfjs.getDocument) return; // pdf.js not loaded → keep ext badge

            try {
                var ab = await file.arrayBuffer();
                if (token !== thumbToken) return;
                var pdf = await pdfjs.getDocument({ data: ab }).promise;
                if (token !== thumbToken) return;
                var page = await pdf.getPage(1);
                if (token !== thumbToken) return;

                var unscaled = page.getViewport({ scale: 1 });
                var targetH = 240;
                var scale = targetH / unscaled.height;
                var dpr = window.devicePixelRatio || 1;
                var viewport = page.getViewport({ scale: scale * dpr });

                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                var ctx = canvas.getContext('2d');
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                if (token !== thumbToken) return;

                appendThumbNode(canvas, token);
                var badge = thumbEl.querySelector('.slm-thumb-ext-large');
                if (badge) badge.remove();
            } catch (e) {
                // Render failed — keep the ext badge as fallback.
                console.warn('[SofortpdfLoadingModal] PDF thumbnail failed', e);
            }
            return;
        }

        // Other extensions (docx, xlsx, etc.): just keep the ext badge.
    }

    function run(opts) {
        opts = opts || {};
        var duration = typeof opts.duration === 'number' ? opts.duration : 3500;
        var onDone   = typeof opts.onDone === 'function' ? opts.onDone : function() {};
        var files    = Array.isArray(opts.files) ? opts.files : null;

        show();

        if (files && files.length) {
            populatePill(files);
            // Async render, non-blocking.
            renderThumb(files[0]);
        }

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
