'use strict';
// Native links, GET filters and <details> remain useful without JavaScript.
const menu = document.querySelector('#main-menu');
const toggle = document.querySelector('.menu-toggle');
const closer = document.querySelector('.menu-close');
function closeMenu() {
    menu?.classList.remove('is-open');
    toggle?.setAttribute('aria-expanded', 'false');
    toggle?.setAttribute('aria-label', 'Open navigation');
    document.body.style.overflow = '';
}
function openMenu() {
    menu?.classList.add('is-open');
    toggle?.setAttribute('aria-expanded', 'true');
    toggle?.setAttribute('aria-label', 'Close navigation');
    document.body.style.overflow = 'hidden';
    closer?.focus();
}
toggle?.addEventListener('click', () =>
    menu.classList.contains('is-open') ? closeMenu() : openMenu(),
);
closer?.addEventListener('click', () => {
    closeMenu();
    toggle.focus();
});
document.addEventListener('keydown', (event) => {
    if (!menu?.classList.contains('is-open')) return;
    if (event.key === 'Escape') {
        closeMenu();
        toggle.focus();
    }
    if (event.key === 'Tab') {
        const items = [...menu.querySelectorAll('a,button')];
        const first = items[0],
            last = items.at(-1);
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }
});
document.addEventListener('click', (event) => {
    if (
        menu?.classList.contains('is-open') &&
        !menu.contains(event.target) &&
        !toggle.contains(event.target)
    )
        closeMenu();
});
menu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
window.matchMedia('(min-width: 1001px)').addEventListener('change', (event) => {
    if (event.matches) closeMenu();
});
// Fail-open reveal: only hide below-the-fold content after the observer exists.
if ('IntersectionObserver' in window && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const observer = new IntersectionObserver(
        (entries) =>
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('is-pending');
                    observer.unobserve(entry.target);
                }
            }),
        { threshold: 0.08 },
    );
    document.querySelectorAll('.reveal').forEach((element) => {
        element.classList.add('is-ready');
        if (element.getBoundingClientRect().top > innerHeight) element.classList.add('is-pending');
        observer.observe(element);
    });
}
// Gallery buttons update the image and truthful rendering/photo caption together.
document.querySelectorAll('[data-gallery-src]').forEach((button) =>
    button.addEventListener('click', () => {
        const main = document.querySelector('#gallery-main');
        if (!main) return;
        main.src = button.dataset.gallerySrc;
        main.alt = button.dataset.galleryAlt;
        if (!matchMedia('(prefers-reduced-motion: reduce)').matches && main.animate) {
            main.animate([{ opacity: 0.3 }, { opacity: 1 }], { duration: 380, easing: 'ease-out' });
        }
        const caption = document.querySelector('#gallery-caption');
        if (caption) caption.textContent = button.dataset.galleryKind;
        document
            .querySelectorAll('[data-gallery-src]')
            .forEach((item) => item.setAttribute('aria-pressed', String(item === button)));
    }),
);
// Changing estate or plot reloads the PHP form without discarding typed form fields.
// A separate GET selector is used for the prototype discovery journey.
document
    .querySelectorAll('[data-autosubmit]')
    .forEach((select) => select.addEventListener('change', () => select.form.requestSubmit()));
const typeFilter = document.querySelector('#type-filter');
const bedroomField = document.querySelector('#bedroom-filter-field');
function updateBedrooms() {
    if (!typeFilter || !bedroomField) return;
    const relevant = typeFilter.value === 'home';
    bedroomField.hidden = !relevant;
    const field = bedroomField.querySelector('select');
    field.disabled = !relevant;
    if (!relevant) field.value = '';
}
typeFilter?.addEventListener('change', updateBedrooms);
updateBedrooms();
document.querySelectorAll('[data-confirm]').forEach((form) =>
    form.addEventListener('submit', (event) => {
        if (!confirm(form.dataset.confirm)) event.preventDefault();
    }),
);
document.querySelectorAll('form[data-submit-once]').forEach((form) =>
    form.addEventListener('submit', () => {
        if (!form.checkValidity()) return;
        const button = form.querySelector('button[type=submit]');
        if (button) {
            button.disabled = true;
            button.textContent = 'Opening WhatsApp…';
        }
    }),
);
// Keep the inspection selections consistent without rebuilding the interface.
const estateSelect = document.querySelector('#request-estate');
const optionSelect = document.querySelector('#request-option');
const prototypeSelect = document.querySelector('#request-prototype');
function filterRequestSelections() {
    if (!optionSelect) return;
    const optionValue = optionSelect.value;
    [...optionSelect.options].forEach((option) => {
        const allowed = !option.value || option.dataset.estate === estateSelect.value;
        option.hidden = !allowed;
        option.disabled = !allowed;
    });
    optionSelect.selectedIndex = Math.max(
        0,
        [...optionSelect.options].findIndex((o) => o.value === optionValue && !o.disabled),
    );
    if (!prototypeSelect) return;
    const prototypeValue = prototypeSelect.value;
    [...prototypeSelect.options].forEach((option) => {
        const allowed = !option.value || option.dataset.option === optionSelect.value;
        option.hidden = !allowed;
        option.disabled = !allowed;
    });
    prototypeSelect.selectedIndex = Math.max(
        0,
        [...prototypeSelect.options].findIndex((o) => o.value === prototypeValue && !o.disabled),
    );
}
estateSelect?.addEventListener('change', filterRequestSelections);
optionSelect?.addEventListener('change', filterRequestSelections);
filterRequestSelections();
// Animate native FAQ disclosure while preserving a usable no-script fallback.
document.querySelectorAll('.faq-item').forEach((details) => {
    const summary = details.querySelector('summary');
    let animation;
    summary.setAttribute('aria-expanded', String(details.open));
    summary.addEventListener('click', (event) => {
        if (matchMedia('(prefers-reduced-motion: reduce)').matches || !details.animate) return;
        event.preventDefault();
        if (animation) return;
        const willOpen = !details.open;
        const start = details.offsetHeight;
        if (willOpen) details.open = true;
        const end = willOpen ? details.offsetHeight : summary.offsetHeight + 1;
        details.style.overflow = 'hidden';
        summary.setAttribute('aria-expanded', String(willOpen));
        animation = details.animate([{ height: `${start}px` }, { height: `${end}px` }], {
            duration: 240,
            easing: 'ease-out',
        });
        animation.onfinish = () => {
            details.open = willOpen;
            details.style.overflow = '';
            animation = null;
        };
    });
    details.addEventListener('toggle', () =>
        summary.setAttribute('aria-expanded', String(details.open)),
    );
});
// Keep fixed contact controls clear of the on-screen keyboard and active fields.
function updateFormFocus() {
    document.body.classList.toggle(
        'editing-form',
        Boolean(document.activeElement?.matches('input, select, textarea')),
    );
}
document.addEventListener('focusin', updateFormFocus);
document.addEventListener('focusout', () => setTimeout(updateFormFocus, 0));

// Restore the form after returning from WhatsApp via the browser Back button.
window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-submit-once] button[type=submit]').forEach((button) => {
        if (!button.disabled) return;
        button.disabled = false;
        button.textContent = button.form.action.includes('inspection.php')
            ? 'Request an inspection'
            : 'Send your enquiry';
    });
});
