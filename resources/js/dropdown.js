export function initDropdowns() {
    const scope = document.body;

    function closeAll(except) {
        document.querySelectorAll('[data-dropdown]').forEach((dd) => {
            if (dd === except) return;
            const menu = dd.querySelector('[data-dropdown-menu]');
            if (menu) menu.classList.add('hidden');
        });
    }

    scope.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-dropdown-trigger]');
        if (trigger) {
            const dd = trigger.closest('[data-dropdown]');
            const menu = dd?.querySelector('[data-dropdown-menu]');
            const isHidden = menu?.classList.contains('hidden');
            closeAll(dd);
            if (menu && isHidden) {
                menu.classList.remove('hidden');
            }
            e.stopPropagation();
            return;
        }
        const insideMenu = e.target.closest('[data-dropdown-menu]');
        if (!insideMenu) {
            closeAll(null);
        }
    });

    const gg = scope.querySelector('[data-nav]');
    if (gg) {
        const toggle = scope.querySelector('[data-nav-toggle]');
        toggle?.addEventListener('click', () => {
            gg.classList.toggle('hidden');
        });
    }
}
