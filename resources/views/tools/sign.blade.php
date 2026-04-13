@extends('layouts.app')

@php
    $maxUploadMb = (int) env('MAX_UPLOAD_SIZE_MB', 50);
    // Build JS message payload in PHP to avoid Blade parser issues with
    // nested __() calls inside directives / inline arrays.
    $signJsMessages = [
        'errFileTooLarge'    => __('sign.err_file_too_large', ['size' => $maxUploadMb]),
        'errNotPdf'          => __('sign.err_not_pdf'),
        'errSignFailed'      => __('sign.err_sign_failed'),
        'errGeneric'         => __('sign.error_generic'),
        'submit'             => __('sign.submit'),
        'submitting'         => __('sign.submitting'),
        'pageIndicator'      => __('sign.page_indicator', ['current' => '__CURRENT__', 'total' => '__TOTAL__']),
    ];
@endphp

@section('content')
    <section class="relative overflow-hidden">
        {{-- Background --}}
        <div class="absolute inset-0 bg-gradient-to-b from-brand-50/40 to-white pointer-events-none"></div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">
            {{-- Headlines --}}
            <div class="text-center mb-8">
                <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-slate-900 tracking-tight">{{ $h1 }}</h1>
                <h2 class="mt-3 text-lg text-slate-500 max-w-xl mx-auto">{{ $h2 }}</h2>
            </div>

            {{-- Upload Zone --}}
            <div id="upload-zone"
                 class="relative bg-white rounded-2xl border-2 border-dashed border-slate-200 hover:border-brand-300 transition-colors p-8 sm:p-12 text-center cursor-pointer group">

                {{-- Icon --}}
                <div class="w-16 h-16 rounded-2xl bg-brand-50 group-hover:bg-brand-100 flex items-center justify-center mx-auto mb-4 transition-colors">
                    <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>

                <p class="font-display font-bold text-slate-700 mb-1">{{ __('sign.drop_or_click') }}</p>
                <p class="text-sm text-slate-400">
                    {{ __('sign.format_hint', ['size' => $maxUploadMb]) }}
                </p>

                <input type="file" id="file-input" class="hidden" accept=".pdf">
            </div>

            {{-- Signing workspace (hidden by default) --}}
            <div id="sign-workspace" class="hidden">

                {{-- Toolbar --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <button id="btn-create-sig"
                                class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-display font-bold px-4 py-2 rounded-lg shadow-sm text-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            {{ __('sign.create_signature') }}
                        </button>
                        <button id="btn-reset"
                                class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium px-4 py-2 rounded-lg text-sm transition-colors">
                            {{ __('sign.reset') }}
                        </button>
                    </div>

                    {{-- Page navigation --}}
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <button id="btn-prev-page" class="p-1.5 rounded-lg hover:bg-slate-100 disabled:opacity-30 transition-colors" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span id="page-indicator" class="font-medium">{{ __('sign.page_indicator', ['current' => 1, 'total' => 1]) }}</span>
                        <button id="btn-next-page" class="p-1.5 rounded-lg hover:bg-slate-100 disabled:opacity-30 transition-colors" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Instruction --}}
                <div id="placement-hint" class="hidden bg-brand-50 border border-brand-100 rounded-xl p-3 mb-4 text-center">
                    <p class="text-sm text-brand-700 font-medium">{{ __('sign.placement_hint') }}</p>
                </div>

                {{-- PDF viewer --}}
                <div id="pdf-viewer" class="relative bg-slate-100 rounded-xl border border-slate-200 overflow-hidden min-h-[400px] flex items-center justify-center">
                    <canvas id="pdf-canvas" class="max-w-full cursor-crosshair"></canvas>
                    {{-- Signature overlays will be appended here --}}
                    <div id="sig-overlays" class="absolute inset-0 pointer-events-none"></div>
                </div>

                {{-- Submit --}}
                <div class="mt-6 text-center">
                    <button id="btn-submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-display font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-brand-600/25 hover:shadow-brand-600/40 transition-all text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span id="btn-submit-text">{{ __('sign.submit') }}</span>
                        <svg id="btn-submit-spinner" class="hidden animate-spin-slow w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Processing state --}}
            <div id="processing-state" class="hidden mt-8 text-center">
                <div class="w-12 h-12 rounded-full border-4 border-brand-200 border-t-brand-600 animate-spin-slow mx-auto mb-4"></div>
                <p class="font-display font-bold text-slate-700">{{ __('sign.processing_heading') }}</p>
                <p class="text-sm text-slate-400 mt-1">{{ __('sign.processing_note') }}</p>
            </div>

            {{-- Download state --}}
            <div id="download-state" class="hidden mt-8 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="font-display font-bold text-slate-700">{{ __('sign.download_ready') }}</p>
                <a id="download-link" href="#" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-display font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all text-sm mt-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    {{ __('sign.download') }}
                </a>
            </div>

            {{-- Error state --}}
            <div id="error-state" class="hidden mt-6">
                <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center">
                    <p class="text-sm text-red-600" id="error-message">{{ __('sign.error_generic') }}</p>
                    <button onclick="window.signApp.reset()" class="text-sm text-red-500 underline mt-2">{{ __('sign.try_again') }}</button>
                </div>
            </div>

            {{-- Trust signals --}}
            @include('partials.trust-signals')
        </div>
    </section>

    {{-- Signature creation modal --}}
    <div id="sig-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div id="sig-modal-backdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        {{-- Modal content --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="font-display font-bold text-xl text-slate-900 mb-4">{{ __('sign.modal_heading') }}</h3>

            {{-- Signature canvas --}}
            <div class="bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 p-1">
                <canvas id="sig-pad-canvas" class="w-full rounded-lg" style="height: 200px; touch-action: none;"></canvas>
            </div>

            <p class="text-xs text-slate-400 mt-2 text-center">{{ __('sign.modal_hint') }}</p>

            {{-- Modal buttons --}}
            <div class="flex items-center justify-between mt-5">
                <button id="btn-sig-clear"
                        class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    {{ __('sign.clear') }}
                </button>
                <div class="flex items-center gap-2">
                    <button id="btn-sig-cancel"
                            class="px-4 py-2 text-sm text-slate-500 hover:text-slate-700 font-medium transition-colors">
                        {{ __('sign.cancel') }}
                    </button>
                    <button id="btn-sig-apply"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-display font-bold px-5 py-2 rounded-lg shadow-sm text-sm transition-colors disabled:opacity-50"
                            disabled>
                        {{ __('sign.apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- PDF.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
{{-- signature_pad --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>

<script>
(function() {
    'use strict';

    var __t = {!! json_encode($signJsMessages, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
    var __maxUploadMb = {{ $maxUploadMb }};

    // PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // DOM references
    const uploadZone      = document.getElementById('upload-zone');
    const fileInput        = document.getElementById('file-input');
    const signWorkspace    = document.getElementById('sign-workspace');
    const pdfCanvas        = document.getElementById('pdf-canvas');
    const sigOverlays      = document.getElementById('sig-overlays');
    const pageIndicator    = document.getElementById('page-indicator');
    const btnPrev          = document.getElementById('btn-prev-page');
    const btnNext          = document.getElementById('btn-next-page');
    const btnCreateSig     = document.getElementById('btn-create-sig');
    const btnReset         = document.getElementById('btn-reset');
    const btnSubmit        = document.getElementById('btn-submit');
    const btnSubmitText    = document.getElementById('btn-submit-text');
    const btnSubmitSpinner = document.getElementById('btn-submit-spinner');
    const placementHint    = document.getElementById('placement-hint');
    const processingState  = document.getElementById('processing-state');
    const downloadState    = document.getElementById('download-state');
    const errorState       = document.getElementById('error-state');

    // Modal
    const sigModal         = document.getElementById('sig-modal');
    const sigModalBackdrop = document.getElementById('sig-modal-backdrop');
    const sigPadCanvas     = document.getElementById('sig-pad-canvas');
    const btnSigClear      = document.getElementById('btn-sig-clear');
    const btnSigCancel     = document.getElementById('btn-sig-cancel');
    const btnSigApply      = document.getElementById('btn-sig-apply');

    // State
    let pdfFile = null;
    let pdfDoc = null;
    let pdfArrayBuffer = null;
    let currentPage = 1;
    let totalPages = 0;
    let signaturePad = null;
    let signatureDataUrl = null;  // PNG data URL of the drawn signature
    let placedSignatures = [];    // { id, page, xPct, yPct, widthPct, heightPct }
    let sigIdCounter = 0;
    let placementMode = false;
    const SCALE = 1.5;

    // Upload handling
    uploadZone.addEventListener('click', () => fileInput.click());
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dropzone-active', 'border-brand-400', 'bg-brand-50/50');
    });
    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dropzone-active', 'border-brand-400', 'bg-brand-50/50');
    });
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dropzone-active', 'border-brand-400', 'bg-brand-50/50');
        if (e.dataTransfer.files.length > 0) loadPdf(e.dataTransfer.files[0]);
    });
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) loadPdf(fileInput.files[0]);
    });

    async function loadPdf(file) {
        const maxSize = __maxUploadMb * 1024 * 1024;
        if (file.size > maxSize) {
            showError(__t.errFileTooLarge);
            return;
        }
        if (!file.name.toLowerCase().endsWith('.pdf')) {
            showError(__t.errNotPdf);
            return;
        }

        pdfFile = file;
        pdfArrayBuffer = await file.arrayBuffer();

        const loadingTask = pdfjsLib.getDocument({ data: pdfArrayBuffer.slice(0) });
        pdfDoc = await loadingTask.promise;
        totalPages = pdfDoc.numPages;
        currentPage = 1;

        uploadZone.classList.add('hidden');
        signWorkspace.classList.remove('hidden');
        errorState.classList.add('hidden');

        updatePageNav();
        renderPage(currentPage);
    }

    // PDF rendering
    async function renderPage(pageNum) {
        const page = await pdfDoc.getPage(pageNum);
        const viewport = page.getViewport({ scale: SCALE });

        pdfCanvas.width = viewport.width;
        pdfCanvas.height = viewport.height;

        const ctx = pdfCanvas.getContext('2d');
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;

        renderSignatureOverlays();
    }

    function updatePageNav() {
        pageIndicator.textContent = __t.pageIndicator
            .replace('__CURRENT__', currentPage)
            .replace('__TOTAL__', totalPages);
        btnPrev.disabled = currentPage <= 1;
        btnNext.disabled = currentPage >= totalPages;
    }

    btnPrev.addEventListener('click', () => {
        if (currentPage > 1) { currentPage--; updatePageNav(); renderPage(currentPage); }
    });
    btnNext.addEventListener('click', () => {
        if (currentPage < totalPages) { currentPage++; updatePageNav(); renderPage(currentPage); }
    });

    // Signature pad modal
    btnCreateSig.addEventListener('click', openSigModal);
    btnSigCancel.addEventListener('click', closeSigModal);
    sigModalBackdrop.addEventListener('click', closeSigModal);

    function openSigModal() {
        sigModal.classList.remove('hidden');

        // Initialize signature pad
        sigPadCanvas.width = sigPadCanvas.offsetWidth;
        sigPadCanvas.height = 200;

        if (!signaturePad) {
            signaturePad = new SignaturePad(sigPadCanvas, {
                backgroundColor: 'rgba(255,255,255,0)',
                penColor: '#1e293b',
                minWidth: 1.5,
                maxWidth: 3,
            });
        } else {
            signaturePad.clear();
        }

        btnSigApply.disabled = true;

        signaturePad.addEventListener('endStroke', () => {
            btnSigApply.disabled = signaturePad.isEmpty();
        });
    }

    function closeSigModal() {
        sigModal.classList.add('hidden');
    }

    btnSigClear.addEventListener('click', () => {
        if (signaturePad) {
            signaturePad.clear();
            btnSigApply.disabled = true;
        }
    });

    btnSigApply.addEventListener('click', () => {
        if (!signaturePad || signaturePad.isEmpty()) return;

        signatureDataUrl = signaturePad.toDataURL('image/png');
        closeSigModal();

        // Enter placement mode
        placementMode = true;
        placementHint.classList.remove('hidden');
        pdfCanvas.style.cursor = 'crosshair';
    });

    // Place signature on click
    pdfCanvas.addEventListener('click', (e) => {
        if (!placementMode || !signatureDataUrl) return;

        const rect = pdfCanvas.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const clickY = e.clientY - rect.top;

        // Convert to percentage of canvas
        const xPct = (clickX / rect.width) * 100;
        const yPct = (clickY / rect.height) * 100;

        // Signature size: ~20% width, proportional height
        const widthPct = 20;
        const heightPct = 8;

        // Center on click position
        const finalX = Math.max(0, Math.min(100 - widthPct, xPct - widthPct / 2));
        const finalY = Math.max(0, Math.min(100 - heightPct, yPct - heightPct / 2));

        const sig = {
            id: ++sigIdCounter,
            page: currentPage,
            xPct: finalX,
            yPct: finalY,
            widthPct: widthPct,
            heightPct: heightPct,
        };

        placedSignatures.push(sig);
        placementMode = false;
        placementHint.classList.add('hidden');
        pdfCanvas.style.cursor = 'default';

        renderSignatureOverlays();
        updateSubmitButton();
    });

    // Render signature overlays
    function renderSignatureOverlays() {
        sigOverlays.innerHTML = '';

        const pageSignatures = placedSignatures.filter(s => s.page === currentPage);

        pageSignatures.forEach(sig => {
            const overlay = document.createElement('div');
            overlay.className = 'absolute pointer-events-auto';
            overlay.style.left = sig.xPct + '%';
            overlay.style.top = sig.yPct + '%';
            overlay.style.width = sig.widthPct + '%';
            overlay.style.height = sig.heightPct + '%';
            overlay.dataset.sigId = sig.id;

            // Signature image
            const img = document.createElement('img');
            img.src = signatureDataUrl;
            img.className = 'w-full h-full object-contain opacity-80';
            img.draggable = false;
            overlay.appendChild(img);

            // Background highlight
            overlay.style.backgroundColor = 'rgba(59, 108, 245, 0.05)';
            overlay.style.border = '1px dashed rgba(59, 108, 245, 0.3)';
            overlay.style.borderRadius = '4px';

            // Remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs shadow-sm transition-colors';
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                placedSignatures = placedSignatures.filter(s => s.id !== sig.id);
                renderSignatureOverlays();
                updateSubmitButton();
            });
            overlay.appendChild(removeBtn);

            // Simple drag support
            let isDragging = false;
            let dragStartX, dragStartY, origLeft, origTop;

            overlay.addEventListener('mousedown', (e) => {
                if (e.target === removeBtn) return;
                isDragging = true;
                dragStartX = e.clientX;
                dragStartY = e.clientY;
                origLeft = sig.xPct;
                origTop = sig.yPct;
                overlay.style.cursor = 'grabbing';
                e.preventDefault();
            });

            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                const viewerRect = sigOverlays.getBoundingClientRect();
                const dx = ((e.clientX - dragStartX) / viewerRect.width) * 100;
                const dy = ((e.clientY - dragStartY) / viewerRect.height) * 100;

                sig.xPct = Math.max(0, Math.min(100 - sig.widthPct, origLeft + dx));
                sig.yPct = Math.max(0, Math.min(100 - sig.heightPct, origTop + dy));

                overlay.style.left = sig.xPct + '%';
                overlay.style.top = sig.yPct + '%';
            });

            document.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    overlay.style.cursor = 'grab';
                }
            });

            overlay.style.cursor = 'grab';
            sigOverlays.appendChild(overlay);
        });
    }

    function updateSubmitButton() {
        btnSubmit.disabled = placedSignatures.length === 0;
    }

    // Reset
    btnReset.addEventListener('click', () => {
        placedSignatures = [];
        signatureDataUrl = null;
        placementMode = false;
        placementHint.classList.add('hidden');
        pdfCanvas.style.cursor = 'default';
        renderSignatureOverlays();
        updateSubmitButton();
    });

    // Submit
    btnSubmit.addEventListener('click', async () => {
        if (placedSignatures.length === 0 || !signatureDataUrl) return;

        if (window.sofortpdfPaywall && window.sofortpdfPaywall.needsPayment()
            && window.SofortpdfPaymentModal) {
            window.SofortpdfPaymentModal.open({
                files: pdfFile ? [pdfFile] : [],
                onSuccess: function() { btnSubmit.click(); },
            });
            return;
        }

        btnSubmit.disabled = true;
        btnSubmitText.textContent = __t.submitting;
        btnSubmitSpinner.classList.remove('hidden');

        signWorkspace.classList.add('hidden');
        processingState.classList.remove('hidden');

        try {
            // Convert PDF file to base64
            const pdfBase64 = arrayBufferToBase64(pdfArrayBuffer);

            // Build positions array (percentage-based)
            const positions = placedSignatures.map(s => ({
                page: s.page,
                x: s.xPct,
                y: s.yPct,
                width: s.widthPct,
                height: s.heightPct,
            }));

            const response = await fetch('/api/sign', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    pdf_base64: pdfBase64,
                    signature_png_base64: signatureDataUrl,
                    positions: positions,
                }),
            });

            if (response.status === 403) {
                window.location.href = '/checkout/start?return_to=' + encodeURIComponent(window.location.pathname);
                return;
            }

            if (!response.ok) {
                const err = await response.json();
                throw new Error(err.message || __t.errSignFailed);
            }

            const result = await response.json();

            // Redirect to the unified confirmation page (same UX as every
            // other tool). Falls back to the inline download state if the
            // server didn't return a confirmation_url.
            if (result.confirmation_url) {
                var confUrl = result.confirmation_url;
                if (window.__sofortpdfTrialJustPaid) {
                    confUrl += (confUrl.indexOf('?') >= 0 ? '&' : '?') + 'cGF5bWVudFN1Y2Nlc3M=';
                    try { delete window.__sofortpdfTrialJustPaid; } catch (e) { window.__sofortpdfTrialJustPaid = false; }
                }
                window.location.href = confUrl;
                return;
            }

            processingState.classList.add('hidden');
            downloadState.classList.remove('hidden');
            document.getElementById('download-link').href = result.download_url;

        } catch (err) {
            showError(err.message || __t.errGeneric);
        }
    });

    // Helpers
    function arrayBufferToBase64(buffer) {
        let binary = '';
        const bytes = new Uint8Array(buffer);
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary);
    }

    function showError(message) {
        processingState.classList.add('hidden');
        signWorkspace.classList.add('hidden');
        errorState.classList.remove('hidden');
        document.getElementById('error-message').textContent = message;
        btnSubmit.disabled = false;
        btnSubmitText.textContent = __t.submit;
        btnSubmitSpinner.classList.add('hidden');
    }

    // Public API for reset from error state
    window.signApp = {
        reset: function() {
            pdfFile = null;
            pdfDoc = null;
            pdfArrayBuffer = null;
            currentPage = 1;
            totalPages = 0;
            signatureDataUrl = null;
            placedSignatures = [];
            placementMode = false;

            uploadZone.classList.remove('hidden');
            signWorkspace.classList.add('hidden');
            processingState.classList.add('hidden');
            downloadState.classList.add('hidden');
            errorState.classList.add('hidden');
            placementHint.classList.add('hidden');

            fileInput.value = '';
            sigOverlays.innerHTML = '';
            pdfCanvas.style.cursor = 'default';
            btnSubmit.disabled = true;
            btnSubmitText.textContent = __t.submit;
            btnSubmitSpinner.classList.add('hidden');
        }
    };

})();
</script>
@endpush
