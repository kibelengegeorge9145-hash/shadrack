<style>
    :root { color-scheme: light; }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: 'Source Sans 3', 'Helvetica Neue', Arial, sans-serif;
        color: #1a3049;
        background: #eceff3;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .no-print { display: block; }
    .report-document {
        background: #ffffff;
        max-width: 820px;
        margin: 0 auto;
        padding: 48px 52px;
    }
    .report-toolbar {
        max-width: 820px;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        background: #ffffff;
        border: 1px solid #dce6f1;
        border-radius: 12px;
        padding: 12px 16px;
        box-shadow: 0 1px 3px rgba(11,23,39,0.06);
    }
    .report-toolbar .tb-title { font-weight: 700; color: #1a3049; }
    .report-toolbar .tb-title small { display:block; font-weight:400; color:#3e6697; font-size:12px; }
    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        font-weight: 600; font-size: 13px; line-height: 1;
        padding: 9px 14px; border-radius: 8px; border: 1px solid transparent;
        cursor: pointer; text-decoration: none; transition: background .15s;
    }
    .btn-primary { background:#13233a; color:#fff; }
    .btn-primary:hover { background:#1a3049; }
    .btn-gold { background:#d4af55; color:#0b1727; }
    .btn-gold:hover { background:#c09a3e; }
    .btn-ghost { background:#fff; color:#1a3049; border-color:#b9cce2; }
    .btn-ghost:hover { background:#f0f4f9; }

    /* Typography */
    .report-document h1, .report-document h2, .report-document h3 { font-family: 'Playfair Display', Georgia, serif; }
    .text-navy-900 { color:#13233a; } .text-navy-600 { color:#2e4f79; } .text-navy-500 { color:#3e6697; }
    .text-navy-400 { color:#5f85b2; } .text-center { text-align:center; }

    .report-header { text-align:center; border-bottom:2px solid #13233a; padding-bottom:16px; margin-bottom:22px; }
    .report-header .flex { display:flex; align-items:center; justify-content:center; gap:16px; }
    .report-header .school-logo { display:flex; align-items:center; justify-content:center; width:60px; height:60px; border-radius:50%; background:#13233a; color:#e7cd8d; font-weight:800; font-size:22px; }
    .report-header h1 { font-size:26px; font-weight:800; text-transform:uppercase; letter-spacing:.02em; margin:0; }
    .report-header p { margin:.2em 0; font-size:11px; color:#3e6697; letter-spacing:.06em; text-transform:uppercase; }
    .report-header h2 { display:inline-block; border:2px solid #d4af55; padding:6px 26px; font-size:20px; margin-top:14px; letter-spacing:.04em; }
    .report-header .subtitle { margin-top:6px; font-weight:600; color:#2e4f79; font-size:14px; letter-spacing:0; text-transform:none; }
    .report-header .ref { margin-top:6px; font-size:11px; color:#5f85b2; letter-spacing:0; text-transform:none; }

    /* Info grid */
    .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:1px; background:#dce6f1; border:1px solid #dce6f1; border-radius:6px; overflow:hidden; margin-bottom:20px; }
    .info-cell { background:#fff; padding:8px 12px; }
    .info-cell .k { font-size:10px; text-transform:uppercase; letter-spacing:.06em; color:#5f85b2; font-weight:700; }
    .info-cell .v { font-size:14px; font-weight:600; color:#13233a; margin-top:1px; }

    /* Tables */
    table.report-table { width:100%; border-collapse:collapse; font-size:13px; margin-bottom:20px; }
    table.report-table thead th { background:#13233a; color:#fff; text-align:left; padding:8px 10px; font-size:11px; text-transform:uppercase; letter-spacing:.04em; }
    table.report-table thead th.ctr { text-align:center; }
    table.report-table tbody td { padding:7px 10px; border-bottom:1px solid #e4ebf3; color:#1a3049; }
    table.report-table tbody td.ctr { text-align:center; }
    table.report-table tbody tr:nth-child(even) { background:#f7fafc; }
    table.report-table tbody tr:last-child td { border-bottom:none; }

    .grade-pill { display:inline-block; min-width:26px; height:24px; line-height:24px; text-align:center; border-radius:50%; background:#13233a; color:#e7cd8d; font-weight:700; font-size:12px; }

    /* Summary stat cards */
    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1px; background:#dce6f1; border:1px solid #dce6f1; border-radius:6px; overflow:hidden; margin-bottom:20px; }
    .stat-cell { background:#fff; padding:10px 12px; text-align:center; }
    .stat-cell .k { font-size:10px; text-transform:uppercase; letter-spacing:.05em; color:#5f85b2; font-weight:700; }
    .stat-cell .v { font-family:'Playfair Display',serif; font-size:20px; font-weight:700; color:#13233a; margin-top:2px; }
    .stat-cell .v.gold { color:#a37f2f; }

    .section-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#13233a; border-bottom:1px solid #b9cce2; padding-bottom:4px; margin:18px 0 10px; }

    .comments-box { border:1px solid #dce6f1; border-left:4px solid #d4af55; padding:10px 14px; margin-bottom:10px; background:#fdfbf6; }
    .comments-box .cb-label { font-size:10px; text-transform:uppercase; letter-spacing:.05em; color:#a37f2f; font-weight:700; margin-bottom:2px; }
    .comments-box p { margin:0; font-size:13px; color:#1a3049; }

    .report-footer { margin-top:30px; border-top:1px solid #b9cce2; padding-top:16px; }
    .report-footer .grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
    .report-footer .signature-block { text-align:center; }
    .report-footer .signature-block .signature-line { height:38px; }
    .report-footer .signature-block p { margin:.2em 0; font-size:12px; color:#13233a; }
    .report-footer .signature-block .border-line { display:inline-block; width:150px; border-bottom:1px solid #b9cce2; }
    .report-footer .foot-note { text-align:center; font-size:10px; color:#5f85b2; margin-top:18px; }
    .report-empty { text-align:center; padding:40px 20px; color:#3e6697; }
    .report-empty .big { font-size:22px; font-weight:700; color:#13233a; margin-bottom:6px; }

    @page { size: A4 portrait; margin: 14mm 15mm; }
    @media print {
        body { background:#fff; }
        .no-print, .report-toolbar { display:none !important; }
        .report-document { max-width:100%; margin:0; padding:0; }
        .report-header, .report-footer { page-break-inside: avoid; }
        table.report-table { page-break-inside: auto; }
        table.report-table tr { page-break-inside: avoid; }
        .report-document { break-inside: auto; }
    }
</style>
