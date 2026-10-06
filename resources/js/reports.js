export function initReports() {
    const form = document.querySelector('[data-report-form]');
    if (!form) return;

    const yearSelect = form.querySelector('select[name="academic_year_id"]');
    const termSelect = form.querySelector('select[name="term_id"]');
    const reportTypeSelect = form.querySelector('select[name="report_type"]');
    const termsUrl = form.dataset.termsUrl;
    const selectedTerm = form.dataset.selectedTerm ? String(form.dataset.selectedTerm) : null;

    async function loadTerms(yearId) {
        if (!termsUrl) return;
        const url = new URL(termsUrl, window.location.origin);
        if (yearId) url.searchParams.set('year', yearId);

        try {
            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Failed');
            const data = await res.json();
            termSelect.innerHTML = '';
            termSelect.appendChild(new Option('First term', '', false, !selectedTerm));
            data.terms.forEach((t) => {
                const opt = new Option(t.name, String(t.id), false, String(t.id) === selectedTerm);
                termSelect.appendChild(opt);
            });
            if (data.terms.length > 0 && !selectedTerm) {
                termSelect.value = String(data.default_term);
            }
        } catch (e) {
            /* keep empty select */
        }
    }

    if (yearSelect) {
        yearSelect.addEventListener('change', () => loadTerms(yearSelect.value));
    }
    loadTerms(yearSelect ? yearSelect.value : null);

    const REQUIRED_FILTERS = {
        student: ['student_id'],
        class: ['school_class_id'],
        subject: ['school_class_id', 'subject_id'],
    };

    const LABELS = {
        student_id: 'Student',
        school_class_id: 'Class',
        subject_id: 'Subject',
    };

    function missingFields(type) {
        return (REQUIRED_FILTERS[type] || []).filter((name) => {
            const el = form.querySelector(`select[name="${name}"]`);
            return el && !el.value;
        });
    }

    form.addEventListener('submit', (e) => {
        const type = reportTypeSelect ? reportTypeSelect.value : '';
        const missing = missingFields(type);
        if (missing.length === 0) return;

        e.preventDefault();
        e.stopPropagation();

        const labels = missing.map((n) => LABELS[n] || n).join(' and ');
        const msg = `${labels} ${missing.length === 1 ? 'is' : 'are'} required for a ${type} report.`;
        if (typeof window.EbenezerToast === 'function') {
            window.EbenezerToast(msg, 'warning', 'Missing filter');
        } else {
            alert(msg);
        }
        form.querySelector(`select[name="${missing[0]}"]`)?.focus();
    });

    const quickLinks = document.querySelectorAll('[data-quick-report]');
    quickLinks.forEach((link) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            if (reportTypeSelect) reportTypeSelect.value = link.dataset.quickReport;
            form.requestSubmit();
        });
    });
}
