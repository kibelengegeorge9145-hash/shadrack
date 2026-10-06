<style>
    /* Applied only inside the dompdf pipeline (reports.pdf layout).          */
    /* Dompdf has no CSS grid/flayout support, so grid-based report blocks    */
    /* are flattened to table-cell layouts that paginate reliably.            */
    .info-grid,
    .stat-grid,
    .report-footer .grid {
        display: table;
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .info-grid .info-cell,
    .stat-grid .stat-cell,
    .report-footer .signature-block {
        display: table-cell;
        vertical-align: top;
        border: 1px solid #dce6f1;
        border-collapse: collapse;
        padding: 7px 10px;
    }

    .stat-grid .stat-cell {
        width: 20%;
    }

    .info-grid .info-cell {
        width: 50%;
    }

    .report-footer .signature-block {
        width: 50%;
        text-align: center;
    }

    .report-header .flex {
        display: table;
        width: 100%;
        margin: 0 auto;
    }

    .report-header .school-logo {
        display: table-cell;
        vertical-align: middle;
    }

    .report-header .flex .text-left {
        display: table-cell;
        vertical-align: middle;
        width: 78%;
        text-align: left;
    }

    .report-header .inline-block {
        display: inline-block;
    }

    .report-header .mt-3,
    .report-header .mt-1 {
        margin-top: 12px;
    }

    .report-header .border-2 {
        border: 2px solid #d4af55;
    }

    .gold {
        color: #a37f2f;
    }
</style>