/**
 * Tasteful public-site motion: scroll reveals, header state, stat counters.
 * Only runs when <body data-motion="public"> is present.
 */
export function initPublicMotion() {
    if (document.body?.dataset?.motion !== 'public') {
        return;
    }

    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    initHeaderScroll();

    if (reduce) {
        document.documentElement.classList.add('motion-reduce');
        document.querySelectorAll('.js-reveal, .reveal, .reveal-delay, .reveal-delay-2').forEach((el) => {
            el.classList.add('is-inview');
        });
        return;
    }

    prepareRevealTargets();
    initScrollReveal();
    initStatCounters();
}

function initHeaderScroll() {
    const header = document.querySelector('.site-header');
    if (!header) {
        return;
    }

    const onScroll = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 12);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

function prepareRevealTargets() {
    const main = document.querySelector('main');
    if (!main) {
        return;
    }

    const autoSelectors = [
        'main section:not(:first-of-type) > .site-container',
        'main .program-card',
        'main article',
        'main blockquote',
        'main .media-frame',
        'main form',
        'main table',
        'main .page-hero > .site-container > *',
    ];

    document.querySelectorAll(autoSelectors.join(',')).forEach((el) => {
        if (!el.classList.contains('js-reveal') && !el.classList.contains('reveal') && !el.classList.contains('reveal-delay') && !el.classList.contains('reveal-delay-2')) {
            el.classList.add('js-reveal');
        }
    });

    // Stagger siblings inside common grids
    document.querySelectorAll('main .grid, main .divide-y').forEach((grid) => {
        const children = [...grid.children].filter((child) => child.classList.contains('js-reveal') || child.classList.contains('program-card') || child.matches('article, a, blockquote, figure, div'));
        children.forEach((child, index) => {
            if (!child.classList.contains('js-reveal')) {
                child.classList.add('js-reveal');
            }
            child.style.setProperty('--reveal-delay', `${Math.min(index, 8) * 70}ms`);
        });
    });

    // Hero / first section: play immediately as entrance
    const first = main.querySelector(':scope > section');
    if (first) {
        first.querySelectorAll('.js-reveal, .reveal, .reveal-delay, .reveal-delay-2').forEach((el, index) => {
            el.style.setProperty('--reveal-delay', `${index * 90}ms`);
            requestAnimationFrame(() => el.classList.add('is-inview'));
        });
    }
}

function initScrollReveal() {
    const nodes = document.querySelectorAll('.js-reveal, .reveal, .reveal-delay, .reveal-delay-2');
    if (!nodes.length || !('IntersectionObserver' in window)) {
        nodes.forEach((el) => el.classList.add('is-inview'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-inview');
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.12,
            rootMargin: '0px 0px -6% 0px',
        }
    );

    nodes.forEach((el) => {
        if (!el.classList.contains('is-inview')) {
            observer.observe(el);
        }
    });

    // Safety: never leave content invisible if observer misses
    window.setTimeout(() => {
        nodes.forEach((el) => el.classList.add('is-inview'));
    }, 2500);
}

function initStatCounters() {
    const stats = document.querySelectorAll('[data-count]');
    if (!stats.length || !('IntersectionObserver' in window)) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }
                animateCount(entry.target);
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.4 }
    );

    stats.forEach((el) => observer.observe(el));
}

function animateCount(el) {
    const raw = el.getAttribute('data-count') || '0';
    const suffix = el.getAttribute('data-count-suffix') || '';
    const target = parseInt(raw, 10);
    if (Number.isNaN(target)) {
        return;
    }

    const duration = 1100;
    const start = performance.now();

    const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(target * eased) + suffix;
        if (progress < 1) {
            requestAnimationFrame(tick);
        }
    };

    requestAnimationFrame(tick);
}
