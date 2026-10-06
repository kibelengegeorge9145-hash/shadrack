@extends((isset($pdfMode) && $pdfMode) ? 'reports.pdf' : 'reports.layout')

@section('title', 'Term Performance Report')

@section('report')
    @include('reports.academic._school-header', [
        'reportTitle' => 'TERM ACADEMIC PERFORMANCE REPORT',
        'reportSubtitle' => $term->name . ' · ' . $year->name,
        'reference' => $reference ?? null,
        'generatedAt' => $generated_at ?? null,
    ])

    <div class="info-grid">
        <div class="info-cell"><div class="k">School</div><div class="v">{{ config('app.name') }}</div></div>
        <div class="info-cell"><div class="k">Academic Year</div><div class="v">{{ $year->name }}</div></div>
        <div class="info-cell"><div class="k">Term</div><div class="v">{{ $term->name }}</div></div>
        <div class="info-cell"><div class="k">Classes Reported</div><div class="v">{{ $classes->count() }}</div></div>
    </div>

    <h3 class="section-title">Performance by Class</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th>Form / Class</th>
                <th class="ctr">Level</th>
                <th class="ctr">Students</th>
                <th class="ctr">Average</th>
                <th class="ctr">Highest Avg</th>
                <th class="ctr">Lowest Avg</th>
                <th class="ctr">Pass Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($classes as $row)
                <tr>
                    <td>{{ $row['class']->name }}</td>
                    <td class="ctr">{{ $row['class']->level }}</td>
                    <td class="ctr">{{ $row['students'] }}</td>
                    <td class="ctr">{{ number_format($row['average'], 2) }}%</td>
                    <td class="ctr">{{ number_format($row['highest_average'], 2) }}%</td>
                    <td class="ctr">{{ number_format($row['lowest_average'], 2) }}%</td>
                    <td class="ctr">{{ $row['pass_rate'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="7" class="ctr">No class results recorded for this term.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.academic._footer')
@endsection
