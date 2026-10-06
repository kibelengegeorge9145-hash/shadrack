<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Academic Report') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    @include('reports.academic._report-styles')
    @stack('report-styles')
</head>
<body>
    <div class="no-print report-toolbar">
        <div class="tb-title">
            Report Preview
            <small>{{ $report_type_label ?? 'Academic Report' }} · {{ config('app.name') }}</small>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            @stack('toolbar-actions')
            <a class="btn btn-gold" href="javascript:window.print()">
                Print
            </a>
            @if (!empty($pdfUrl))
                <a class="btn btn-primary" href="{{ $pdfUrl }}">
                    Download PDF
                </a>
            @else
                <a class="btn btn-primary" href="javascript:window.print()">
                    Download PDF
                </a>
            @endif
            @if (!empty($excelUrl))
                <a class="btn btn-ghost" href="{{ $excelUrl }}">
                    Export Excel
                </a>
            @endif
            @if (!empty($backRoute))
                <a class="btn btn-ghost" href="{{ $backRoute }}">Back</a>
            @endif
        </div>
    </div>

    <div class="report-document">
        @yield('report')
    </div>

    @stack('report-scripts')
</body>
</html>
