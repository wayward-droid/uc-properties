'use strict';
const motionPreference = matchMedia('(prefers-reduced-motion: reduce)');
const hero = document.querySelector('[data-hero]');
if (hero) {
    const slides = [...hero.querySelectorAll('[data-slide]')];
    const captions = [...hero.querySelectorAll('[data-slide-caption]')];
    const controls = hero.querySelector('.hero-controls');
    const play = hero.querySelector('[data-slide-play]');
    let current = 0;
    let timer;
    let playing = false;
    function showSlide(index) {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, i) => {
            slide.classList.toggle('is-active', i === current);
            slide.setAttribute('aria-hidden', String(i !== current));
            if (i === current) slide.querySelector('img').loading = 'eager';
        });
        captions.forEach((caption, i) => {
            caption.hidden = i !== current;
        });
        const count = hero.querySelector('[data-slide-number]');
        if (count) count.textContent = String(current + 1).padStart(2, '0');
    }
    function stop() {
        clearInterval(timer);
        playing = false;
        if (!play) return;
        play.textContent = 'Play';
        play.setAttribute('aria-pressed', 'false');
        play.setAttribute('aria-label', 'Play estate slideshow');
    }
    function start() {
        clearInterval(timer);
        playing = true;
        play.textContent = 'Pause';
        play.setAttribute('aria-pressed', 'true');
        play.setAttribute('aria-label', 'Pause estate slideshow');
        timer = setInterval(() => showSlide(current + 1), 6500);
    }
    if (controls) {
        controls.hidden = false;
        hero.querySelector('[data-slide-prev]').addEventListener('click', () => {
            stop();
            showSlide(current - 1);
        });
        hero.querySelector('[data-slide-next]').addEventListener('click', () => {
            stop();
            showSlide(current + 1);
        });
        play.addEventListener('click', () => (playing ? stop() : start()));
        // Start only when requested, avoiding movement while visitors are reading.
        hero.addEventListener('keydown', (event) => {
            if (
                event.target.closest('.hero-controls') &&
                ['ArrowLeft', 'ArrowRight'].includes(event.key)
            ) {
                event.preventDefault();
                stop();
                showSlide(current + (event.key === 'ArrowRight' ? 1 : -1));
            }
        });
        hero.addEventListener('focusin', (event) => {
            if (event.target !== play) stop();
        });
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) stop();
        });
        motionPreference.addEventListener('change', stop);
        if ('IntersectionObserver' in window)
            new IntersectionObserver((entries) => {
                if (!entries[0].isIntersecting) stop();
            }).observe(hero);
    }
}
const budget = document.querySelector('#home-budget');
function budgetNote() {
    const note = document.querySelector('[data-budget-note]');
    if (note) note.hidden = !budget.value;
}
budget?.addEventListener('change', budgetNote);
if (budget) budgetNote();

// Same-document grid/map switch: original filters and ordinary PHP links stay intact.
document.querySelectorAll('[data-view-switch]').forEach((switcher) => {
    switcher.hidden = false;
    const panels = [...document.querySelectorAll('[data-results-panel]')];
    switcher.querySelectorAll('button').forEach((button) =>
        button.addEventListener('click', () => {
            switcher
                .querySelectorAll('button')
                .forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
            panels.forEach((panel) => {
                panel.hidden = panel.dataset.resultsPanel !== button.dataset.view;
                if (!panel.hidden)
                    panel
                        .querySelector('[data-estate-map]')
                        ?.dispatchEvent(new Event('map:visible'));
            });
        }),
    );
});
// A light header response; no scroll hijacking or delayed navigation.
const siteHeader = document.querySelector('.site-header');
if (siteHeader && 'IntersectionObserver' in window) {
    const sentinel = document.createElement('div');
    sentinel.className = 'header-sentinel';
    sentinel.setAttribute('aria-hidden', 'true');
    document.body.prepend(sentinel);
    new IntersectionObserver(([entry]) =>
        siteHeader.classList.toggle('is-scrolled', !entry.isIntersecting),
    ).observe(sentinel);
}
