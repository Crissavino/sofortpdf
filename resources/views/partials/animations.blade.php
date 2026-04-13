{{-- ============================================================
     Shared animations — included once from layouts/app.blade.php
     ============================================================ --}}
<style>
    /* ═══════════ Shared easing + tokens ═══════════ */
    :root {
        --ease-out-expo: cubic-bezier(0.23, 1, 0.32, 1);
        --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* ═══════════ observe-animate (scroll-triggered) ═══════════
       Used across home, tool page and anywhere else. JS toggles
       .is-visible when the element scrolls into view. */
    .observe-animate {
        opacity: 0;
        transform: translateY(16px) scale(0.98);
        transition: opacity 0.45s var(--ease-out-expo),
                    transform 0.45s var(--ease-out-expo);
    }
    .observe-animate.is-visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    /* Variant: slide-in from left/right for testimonials */
    .observe-slide-left {
        opacity: 0;
        transform: translateX(-20px);
        transition: opacity 0.5s var(--ease-out-expo),
                    transform 0.5s var(--ease-out-expo);
    }
    .observe-slide-right {
        opacity: 0;
        transform: translateX(20px);
        transition: opacity 0.5s var(--ease-out-expo),
                    transform 0.5s var(--ease-out-expo);
    }
    .observe-slide-left.is-visible,
    .observe-slide-right.is-visible {
        opacity: 1;
        transform: translateX(0);
    }

    /* ═══════════ Nav link underline grow ═══════════ */
    .nav-link {
        position: relative;
    }
    .nav-link::after {
        content: '';
        position: absolute;
        left: 12px;
        right: 12px;
        bottom: 4px;
        height: 2px;
        background: currentColor;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 220ms var(--ease-out-expo);
        border-radius: 1px;
        opacity: 0.6;
    }
    @media (hover: hover) and (pointer: fine) {
        .nav-link:hover::after {
            transform: scaleX(1);
        }
    }

    /* ═══════════ Logo shimmer ═══════════
       Overlay approach: the logo text stays normal; a highlight
       gradient sweeps across it on hover. Avoids the half-transparent
       bug that background-clip: text causes on elements with nested
       colored spans (like sofort<span>pdf</span>). */
    .logo-shimmer {
        position: relative;
        display: inline-block;
        overflow: hidden;
    }
    .logo-shimmer::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(110deg,
            transparent 0%,
            transparent 40%,
            rgba(255, 255, 255, 0.85) 50%,
            transparent 60%,
            transparent 100%);
        pointer-events: none;
    }
    @media (hover: hover) and (pointer: fine) {
        a:hover .logo-shimmer::after {
            animation: logo-sweep 900ms var(--ease-out-expo);
        }
    }
    @keyframes logo-sweep {
        from { left: -100%; }
        to   { left: 100%; }
    }

    /* ═══════════ Flash messages: slide-down + auto-fade ═══════════ */
    .flash-message {
        animation: flash-in 400ms var(--ease-out-expo) forwards;
        transform-origin: top center;
    }
    @keyframes flash-in {
        from { opacity: 0; transform: translateY(-12px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0)   scale(1); }
    }
    .flash-message.flash-dismissing {
        animation: flash-out 300ms var(--ease-out-expo) forwards;
    }
    @keyframes flash-out {
        from { opacity: 1; transform: translateY(0)   scale(1); }
        to   { opacity: 0; transform: translateY(-8px) scale(0.98); }
    }

    /* ═══════════ Reduced motion: kill everything ═══════════ */
    @media (prefers-reduced-motion: reduce) {
        .observe-animate,
        .observe-slide-left,
        .observe-slide-right {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
        .nav-link::after { transition: none; }
        .logo-shimmer::after { display: none; }
        .flash-message {
            animation: none;
        }
    }
</style>

<script>
    // All DOM-dependent setup must wait until the body is parsed — this
    // partial is included inside <head>, so querying now returns nothing.
    (function() {
        function init() {
            // IntersectionObserver: adds .is-visible when element scrolls into view.
            // Used by .observe-animate, .observe-slide-left, .observe-slide-right.
            var animEls = document.querySelectorAll('.observe-animate, .observe-slide-left, .observe-slide-right');
            if (animEls.length) {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    animEls.forEach(function(el) { el.classList.add('is-visible'); });
                } else {
                    var observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                var delay = parseInt(entry.target.getAttribute('data-delay') || '0', 10);
                                setTimeout(function() {
                                    entry.target.classList.add('is-visible');
                                }, delay);
                                observer.unobserve(entry.target);
                            }
                        });
                    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
                    animEls.forEach(function(el) { observer.observe(el); });
                }
            }

            // Flash messages: auto-dismiss after 5s
            document.querySelectorAll('.flash-message').forEach(function(el) {
                setTimeout(function() {
                    el.classList.add('flash-dismissing');
                    setTimeout(function() { el.remove(); }, 300);
                }, 5000);
            });

            // Count-up animation: <span data-countup="1.50" data-decimals="2">1,50</span>
            // Fires once when the element enters the viewport.
            var countEls = document.querySelectorAll('[data-countup]');
            if (countEls.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                function formatNumber(n, decimals, locale) {
                    return n.toLocaleString(locale, {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals,
                    });
                }
                var countObs = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (!entry.isIntersecting) return;
                        var el = entry.target;
                        var target = parseFloat(el.dataset.countup);
                        var decimals = parseInt(el.dataset.decimals || '0', 10);
                        var locale = el.dataset.locale || document.documentElement.lang || 'de';
                        var duration = 900;
                        var start = performance.now();
                        function tick(now) {
                            var t = Math.min(1, (now - start) / duration);
                            var eased = t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
                            var val = target * eased;
                            el.textContent = formatNumber(val, decimals, locale);
                            if (t < 1) requestAnimationFrame(tick);
                        }
                        requestAnimationFrame(tick);
                        countObs.unobserve(el);
                    });
                }, { threshold: 0.5 });
                countEls.forEach(function(el) { countObs.observe(el); });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
