export function initTheme() {
    const key = 'ebenezer-theme';
    const stored = localStorage.getItem(key);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initial = stored || (prefersDark ? 'dark' : 'light');
    applyTheme(initial);

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem(key, next);
        });
    });
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
}
