<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Academic Report') · {{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    @include('reports.academic._report-styles')
    @include('reports.academic._pdf-styles')
    @stack('report-styles')
</head>
<body data-report-pdf="1">
    <div class="report-document">
        @yield('report')
    </div>
</body>
</html>