export function initModals() {
    document.body.addEventListener('click', (e) => {
        const opener = e.target.closest('[data-modal-open]');
        if (opener) {
            const id = opener.getAttribute('data-modal-open');
            openModal(id);
            return;
        }
        const closer = e.target.closest('[data-modal-close]');
        if (closer) {
            const modal = closer.closest('[data-modal]');
            if (modal) closeModal(modal.id);
            return;
        }
        if (e.target.closest('[data-modal-backdrop]')) {
            const modal = e.target.closest('[data-modal]');
            if (modal) closeModal(modal.id);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-modal]:not(.hidden)').forEach((m) => closeModal(m.id));
        }
    });
}

export function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('hidden', 'pointer-events-none');
}

export function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}
