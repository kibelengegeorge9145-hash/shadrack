let container = null;

export function toast(message, type = 'info', title = null) {
    if (!container) {
        container = document.createElement('div');
        container.className = 'fixed top-4 right-4 z-[200] flex flex-col gap-2 w-80 max-w-[calc(100vw-2rem)]';
        document.body.appendChild(container);
    }

    const icons = {
        success: 'check-circle',
        error: 'x-circle',
        warning: 'alert',
        info: 'info',
    };
    const colors = {
        success: 'border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200',
        error: 'border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
        warning: 'border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200',
        info: 'border-navy-200 dark:border-navy-700 text-navy-800 dark:text-navy-200',
    };

    const el = document.createElement('div');
    el.className = `flex items-start gap-3 rounded-xl border bg-white dark:bg-navy-900 px-4 py-3 shadow-card-hover ${colors[type] || colors.info} animate-toast-in`;
    el.innerHTML = `
        <span class="mt-0.5 w-5 h-5 shrink-0" data-icon="${icons[type] || 'info'}"></span>
        <div class="flex-1 min-w-0">
            ${title ? `<p class="font-semibold">${title}</p>` : ''}
            <p class="text-sm">${message}</p>
        </div>
        <button type="button" class="shrink-0 opacity-60 hover:opacity-100" data-toast-dismiss>
            <span class="block w-4 h-4" data-icon="x"></span>
        </button>
    `;
    container.appendChild(el);
    hydrateIcons(el);

    const dismiss = () => el.remove();
    el.querySelector('[data-toast-dismiss]').addEventListener('click', dismiss);
    setTimeout(dismiss, 4500);
}

function hydrateIcons(root) {
    const icons = window.EbenezerIconMap || {};
    root.querySelectorAll('[data-icon]').forEach((el) => {
        const paths = icons[el.dataset.icon];
        if (paths) el.innerHTML = paths;
    });
}
