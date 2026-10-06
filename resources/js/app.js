import { initTheme } from './theme';
import { initSidebar } from './sidebar';
import { initDropdowns } from './dropdown';
import { initModals } from './modals';
import { toast } from './toast';
import { initCharts } from './charts';
import { initConfirm } from './confirm';
import { initReports } from './reports';

window.EbenezerIconMap = {
    'check-circle': '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
    'x-circle': '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>',
    alert: '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
    info: '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
    x: '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
};

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initDropdowns();
    initModals();
    initCharts();
    initConfirm();
    initReports();
});

window.EbenezerToast = toast;
