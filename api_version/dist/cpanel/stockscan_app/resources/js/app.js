document.addEventListener('DOMContentLoaded', () => {
    const autofocus = document.querySelector('[data-autofocus]');
    autofocus?.focus();

    const networkStatus = document.getElementById('scan-network-status');
    const toggleNetworkStatus = () => {
        if (!networkStatus) {
            return;
        }

        networkStatus.classList.toggle('hidden', navigator.onLine);
    };

    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
    const openSidebarButton = document.querySelector('[data-sidebar-open]');
    const closeSidebarButton = document.querySelector('[data-sidebar-close]');

    const setSidebarState = (open) => {
        if (!sidebar || !sidebarOverlay) {
            return;
        }

        sidebar.classList.toggle('is-open', open);
        sidebarOverlay.classList.toggle('is-open', open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    openSidebarButton?.addEventListener('click', () => setSidebarState(true));
    closeSidebarButton?.addEventListener('click', () => setSidebarState(false));
    sidebarOverlay?.addEventListener('click', () => setSidebarState(false));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            setSidebarState(false);
        }
    });

    window.addEventListener('online', toggleNetworkStatus);
    window.addEventListener('offline', toggleNetworkStatus);
    toggleNetworkStatus();

    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            if (!window.confirm(element.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-prevent-double-submit]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('button[type="submit"], button:not([type])');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = submitButton.dataset.submitLabel || 'Processing...';
            }
        });
    });

    document.querySelectorAll('[data-auto-submit="1"]').forEach((input) => {
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                const form = input.closest('form');

                if (form && !form.dataset.submitting) {
                    form.dataset.submitting = '1';
                    form.requestSubmit();
                }
            }
        });
    });
});
