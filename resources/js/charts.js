export function initCharts() {
    document.querySelectorAll('[data-chart="bar"]').forEach((el) => {
        try {
            const data = JSON.parse(el.dataset.values || '[]');
            const labels = JSON.parse(el.dataset.labels || '[]');
            el.innerHTML = barChart(data, labels, el.dataset.color);
        } catch (e) {
            el.innerHTML = '';
        }
    });

    document.querySelectorAll('[data-chart="line"]').forEach((el) => {
        try {
            const data = JSON.parse(el.dataset.values || '[]');
            el.innerHTML = lineChart(data);
        } catch (e) {
            el.innerHTML = '';
        }
    });

    document.querySelectorAll('[data-chart="donut"]').forEach((el) => {
        try {
            const data = JSON.parse(el.dataset.values || '[]');
            const labels = JSON.parse(el.dataset.labels || '[]');
            el.innerHTML = donutChart(data, labels);
        } catch (e) {
            el.innerHTML = '';
        }
    });
}

function barChart(values, labels = [], color = '#2e4f79') {
    const max = Math.max(...values, 1);
    const w = 100 - labels.length * 2;
    const barW = w / values.length;
    const bars = values
        .map((v, i) => {
            const h = (v / max) * 100;
            const label = labels[i] ? `<div class="text-center text-[10px] text-navy-500 mt-1 truncate" style="width:${barW}%">${labels[i]}</div>` : '';
            return `<div class="flex flex-col items-center justify-end h-full" style="width:${barW}%">
                <div class="w-6 rounded-t bg-current transition-all" style="height:${h}%; background:${color}"></div>${label}
            </div>`;
        })
        .join('');
    return `<div class="flex items-end gap-2 h-full">${bars}</div>`;
}

function lineChart(values) {
    const min = Math.min(...values, 0);
    const max = Math.max(...values, 1);
    const range = max - min || 1;
    const pts = values
        .map((v, i) => {
            const x = (i / (values.length - 1)) * 100;
            const y = 100 - ((v - min) / range) * 90 - 5;
            return `${x},${y}`;
        })
        .join(' ');
    return `
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full">
            <polyline points="${pts}" fill="none" stroke="currentColor" stroke-width="1.5" vector-effect="non-scaling-stroke"/>
        </svg>`;
}

function donutChart(values, labels = []) {
    const total = values.reduce((a, b) => a + b, 0) || 1;
    const colors = ['#2e4f79', '#c09a3e', '#5f85b2', '#a37f2f', '#8a6a42', '#13233a'];
    let offset = 0;
    const segs = values
        .map((v, i) => {
            const pct = (v / total) * 100;
            const seg = `<circle r="16" cx="20" cy="20" fill="none" stroke="${colors[i % colors.length]}" stroke-width="6" stroke-dasharray="${pct} ${100 - pct}" stroke-dashoffset="${-offset}" />`;
            offset += pct;
            return seg;
        })
        .join('');
    const legend = labels.map((l, i) => `
        <span class="flex items-center gap-2 text-xs text-navy-600 dark:text-navy-300">
            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:${colors[i % colors.length]}"></span>${l}
        </span>`).join('');
    return `
        <div class="flex flex-col sm:flex-row items-center gap-6">
            <svg viewBox="0 0 40 40" class="w-32 h-32">
                <circle r="16" cx="20" cy="20" fill="none" stroke="currentColor" stroke-width="6" class="text-navy-100 dark:text-navy-800" />
                ${segs}
                <text x="50%" y="50%" text-anchor="middle" dy="0.3em" class="fill-navy-900 dark:fill-white font-bold" style="font-size:8px">${Math.round(total)}</text>
            </svg>
            <div class="flex flex-col gap-1.5">${legend}</div>
        </div>`;
}
