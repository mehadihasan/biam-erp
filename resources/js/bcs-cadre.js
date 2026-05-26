const isSafeNavigateLink = (link) => {
    if (!(link instanceof HTMLAnchorElement)) {
        return false;
    }

    if (link.target === '_blank' || link.hasAttribute('download')) {
        return false;
    }

    if (link.hasAttribute('data-no-navigate') || link.closest('[data-no-navigate]')) {
        return false;
    }

    if (link.hasAttribute('wire:navigate')) {
        return false;
    }

    const href = link.getAttribute('href');

    if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
        return false;
    }

    if (link.closest('form[method]:not([method="get"]):not([method="GET"])')) {
        return false;
    }

    try {
        const url = new URL(href, window.location.href);

        if (url.origin !== window.location.origin) {
            return false;
        }

        if (/\/logout(?:\/|$)/i.test(url.pathname)) {
            return false;
        }

        return !/\.(?:zip|pdf|png|jpe?g|gif|webp|csv|xlsx?|docx?)$/i.test(url.pathname);
    } catch {
        return false;
    }
};

const initBcsSidebar = () => {
    const sidebar = document.querySelector('[data-bcs-sidebar]');
    const sidebarToggle = document.querySelector('[data-bcs-sidebar-toggle]');
    const sidebarNav = sidebar?.querySelector('.bcs-sidebar__nav');
    const activeSidebarItem = sidebar?.querySelector('.bcs-sidebar__link--active');
    const sidebarStorageKey = 'bcsPortalSidebarCollapsed';

    const setSidebarCollapsed = (isCollapsed) => {
        if (!sidebar || !sidebarToggle) {
            return;
        }

        sidebar.classList.toggle('bcs-sidebar--collapsed', isCollapsed);
        sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));

        try {
            localStorage.setItem(sidebarStorageKey, isCollapsed ? '1' : '0');
        } catch {
            // Storage can be blocked in private browsing modes.
        }
    };

    if (sidebar && sidebarToggle && sidebarToggle.dataset.bcsSidebarToggleBound !== 'true') {
        sidebarToggle.dataset.bcsSidebarToggleBound = 'true';

        let storedCollapsed = false;

        try {
            storedCollapsed = localStorage.getItem(sidebarStorageKey) === '1';
        } catch {
            storedCollapsed = false;
        }

        setSidebarCollapsed(storedCollapsed);

        sidebarToggle.addEventListener('click', () => {
            setSidebarCollapsed(!sidebar.classList.contains('bcs-sidebar--collapsed'));
        });
    }

    if (activeSidebarItem && sidebarNav) {
        requestAnimationFrame(() => {
            activeSidebarItem.scrollIntoView({
                block: 'nearest',
                inline: 'nearest',
            });
        });
    }
};

const bindPortalNavigate = () => {
    if (document.documentElement.dataset.bcsPortalNavigateBound === 'true') {
        return;
    }

    document.documentElement.dataset.bcsPortalNavigateBound = 'true';

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const link = event.target.closest('.bcs-shell__main a[href]');

        if (!isSafeNavigateLink(link)) {
            return;
        }

        const navigate = window.Livewire?.navigate;

        if (typeof navigate !== 'function') {
            return;
        }

        event.preventDefault();
        navigate(link.href);
    });
};

const initBcsFormHelpers = () => {
    const autofillBtn = document.querySelector('[data-bcs-autofill]');
    const cadreInput = document.querySelector('#cadre_reference');

    if (autofillBtn && cadreInput) {
        const demoCadre = autofillBtn.getAttribute('data-demo-cadre') || '';
        autofillBtn.addEventListener('click', (e) => {
            e.preventDefault();
            cadreInput.value = demoCadre;
            cadreInput.dispatchEvent(new Event('input', { bubbles: true }));
            cadreInput.focus();
        });
    }

    document.querySelectorAll('[data-bcs-date-field]').forEach((field) => {
        const input = field.querySelector('[data-bcs-date-input]');

        if (!input) {
            return;
        }

        const openPicker = () => {
            input.focus();

            if (typeof input.showPicker === 'function') {
                try {
                    input.showPicker();
                } catch {
                    // Some browsers throw if the native picker is already opening.
                }

                return;
            }
        };

        field.addEventListener('click', () => {
            openPicker();
        });

        field.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            openPicker();
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initBcsSidebar();
    bindPortalNavigate();
    initBcsFormHelpers();
});

document.addEventListener('livewire:navigated', initBcsSidebar);
