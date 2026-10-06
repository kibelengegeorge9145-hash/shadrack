export function initConfirm() {
    document.body.addEventListener('submit', (e) => {
        const form = e.target;
        const confirmMsg = form.getAttribute('data-confirm');
        if (confirmMsg) {
            if (!window.confirm(confirmMsg)) {
                e.preventDefault();
            }
        }
    });
}
