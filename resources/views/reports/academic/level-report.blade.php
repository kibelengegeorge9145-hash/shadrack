@extends((isset($pdfMode) && $pdfMode) ? 'reports.pdf' : 'reports.layout')

@section('title', $level . ' Performance Report')

@section('report')
    @include('reports.academic._school-header', [
        'reportTitle' => strtoupper($level) . ' PERFORMANCE REPORT',
        'reportSubtitle' => $term->name . ' · ' . $year->name,
        'reference' => $reference ?? null,
        'generatedAt' => $generated_at ?? null,
    ])

    <div class="stat-grid">
        <div class="stat-cell"><div class="k">Level</div><div class="v">{{ $level }}</div></div>
        <div class="stat-cell"><div class="k">Students</div><div class="v">{{ $total_students }}</div></div>
        <div class="stat-cell"><div class="k">Overall Average</div><div class="v">{{ number_format($overall_average, 2) }}%</div></div>
        <div class="stat-cell"><div class="k">Pass Rate</div><div class="v gold">{{ $pass_rate }}%</div></div>
    </div>

    <h3 class="section-title">Class Performance — {{ $level }}</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th>Form / Class</th>
                <th class="ctr">Students</th>
                <th class="ctr">Average</th>
                <th class="ctr">Pass Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($classes as $row)
                <tr>
                    <td>{{ $row['class']->name }}</td>
                    <td class="ctr">{{ $row['students'] }}</td>
                    <td class="ctr">{{ number_format($row['average'], 2) }}%</td>
                    <td class="ctr">{{ $row['pass_rate'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="4" class="ctr">No results recorded for {{ $level }} classes.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Subject Performance — {{ $level }}</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th class="ctr">Code</th>
                <th class="ctr">Students</th>
                <th class="ctr">Average Mark</th>
                <th class="ctr">Pass Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subjects as $row)
                <tr>
                    <td>{{ $row['subject']->name }}</td>
                    <td class="ctr">{{ $row['subject']->code }}</td>
                    <td class="ctr">{{ $row['count'] }}</td>
                    <td class="ctr">{{ number_format($row['average_mark'], 2) }}</td>
                    <td class="ctr">{{ $row['pass_rate'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="5" class="ctr">No subject data available for {{ $level }}.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Division Distribution — {{ $level }}</h3>
    <div class="stat-grid" style="grid-template-columns:repeat(5,1fr);">
        @foreach (['Division I','Division II','Division III','Division IV','Division 0'] as $div)
            <div class="stat-cell"><div class="k">{{ $div }}</div><div class="v">{{ $division_distribution->get($div, 0) }}</div></div>
        @endforeach
    </div>

    @include('reports.academic._footer')
@endsection
