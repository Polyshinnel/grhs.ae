const menu = document.querySelector('[data-mobile-menu]');
const menuToggle = document.querySelector('[data-menu-toggle]');

function setMenuOpen(isOpen) {
    if (!menu || !menuToggle) {
        return;
    }

    menu.hidden = !isOpen;
    menuToggle.setAttribute('aria-expanded', String(isOpen));
    menuToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
    menuToggle.querySelector('img')?.setAttribute('src', isOpen ? menuToggle.dataset.closeIcon : menuToggle.dataset.icon);

    if (isOpen) {
        menu.querySelector('a')?.focus();
    }
}

menuToggle?.addEventListener('click', () => {
    setMenuOpen(menuToggle.getAttribute('aria-expanded') !== 'true');
});

document.addEventListener('click', (event) => {
    if (menu && menuToggle && !menu.hidden && !menu.contains(event.target) && !menuToggle.contains(event.target)) {
        setMenuOpen(false);
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && menu && !menu.hidden) {
        setMenuOpen(false);
        menuToggle?.focus();
    }
});

menu?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setMenuOpen(false));
});

const floatingActions = document.querySelector('[data-floating-actions]');

function setFloatingPanelOpen(toggle, isOpen) {
    const panel = document.getElementById(toggle.getAttribute('aria-controls'));

    if (!panel) {
        return;
    }

    toggle.setAttribute('aria-expanded', String(isOpen));
    panel.dataset.open = String(isOpen);
    panel.setAttribute('aria-hidden', String(!isOpen));
    panel.inert = !isOpen;
}

function resetActionToggle(toggle) {
    const icon = toggle.querySelector('img');

    icon?.setAttribute('src', toggle.dataset.icon);
    icon?.classList.remove('h-5', 'w-5');
    icon?.classList.add('h-7', 'w-7');
}

floatingActions?.querySelectorAll('[data-action-toggle]').forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const panel = document.getElementById(toggle.getAttribute('aria-controls'));
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';

        floatingActions.querySelectorAll('[data-action-toggle]').forEach((otherToggle) => {
            resetActionToggle(otherToggle);
            setFloatingPanelOpen(otherToggle, false);
        });

        if (!isOpen && panel) {
            const icon = toggle.querySelector('img');
            icon?.setAttribute('src', floatingActions.dataset.crossIcon);
            icon?.classList.remove('h-7', 'w-7');
            icon?.classList.add('h-5', 'w-5');
            setFloatingPanelOpen(toggle, true);
            panel.querySelector('a, input')?.focus();
        }
    });
});

document.querySelectorAll('[data-contact-form]').forEach((form) => {
    const status = form.querySelector('[data-form-status]');
    const button = form.querySelector('[data-submit-button]');
    let isSubmitting = false;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (isSubmitting || !form.reportValidity()) {
            return;
        }

        isSubmitting = true;
        button.disabled = true;
        button.textContent = 'Sending…';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            const result = await response.json().catch(() => ({}));

            if (response.ok) {
                form.reset();
                status.textContent = result.message || 'Thank you! Your message has been sent successfully.';
                status.dataset.state = 'success';
            } else if (response.status === 422 && result.errors) {
                status.textContent = Object.values(result.errors).flat().join(' ');
                status.dataset.state = 'error';
            } else {
                status.textContent = result.message || "We couldn't send your message right now. Please try again or contact us directly.";
                status.dataset.state = 'error';
            }
        } catch {
            status.textContent = "We couldn't send your message right now. Please try again or contact us directly.";
            status.dataset.state = 'error';
        } finally {
            isSubmitting = false;
            button.disabled = false;
            button.textContent = button.dataset.idleLabel;
        }
    });
});

document.addEventListener('click', (event) => {
    if (floatingActions && !floatingActions.contains(event.target)) {
        floatingActions.querySelectorAll('[data-action-toggle]').forEach((toggle) => {
            resetActionToggle(toggle);
            setFloatingPanelOpen(toggle, false);
        });
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && floatingActions) {
        floatingActions.querySelectorAll('[data-action-toggle]').forEach((toggle) => {
            resetActionToggle(toggle);
            setFloatingPanelOpen(toggle, false);
        });
    }
});
