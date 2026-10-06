export function initSidebar() {
    const sidebar = document.getElementById('app-sidebar');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    if (!sidebar) return;

    const open = () => {
        sidebar.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
    };
    const close = () => {
        sidebar.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
    };

    document.querySelectorAll('[data-sidebar-open]').forEach((b) => b.addEventListener('click', open));
    document.querySelectorAll('[data-sidebar-close]').forEach((b) => b.addEventListener('click', close));
    overlay?.addEventListener('click', close);
}
