@extends((isset($pdfMode) && $pdfMode) ? 'reports.pdf' : 'reports.layout')

@section('title', 'School Performance Report')

@section('report')
    @include('reports.academic._school-header', [
        'reportTitle' => 'WHOLE SCHOOL ACADEMIC PERFORMANCE REPORT',
        'reportSubtitle' => 'Academic Year ' . $year->name . ($term ? ' · ' . $term->name : ''),
        'reference' => $reference ?? null,
        'generatedAt' => $generated_at ?? null,
    ])

    <div class="stat-grid">
        <div class="stat-cell"><div class="k">Total Students</div><div class="v">{{ $overview['total_students'] }}</div></div>
        <div class="stat-cell"><div class="k">Total Subjects</div><div class="v">{{ $overview['total_subjects'] }}</div></div>
        <div class="stat-cell"><div class="k">Overall Average</div><div class="v">{{ number_format($overview['overall_average'], 2) }}%</div></div>
        <div class="stat-cell"><div class="k">Overall Pass Rate</div><div class="v gold">{{ $overview['overall_pass_rate'] }}%</div></div>
    </div>

    <h3 class="section-title">Highlights</h3>
    <div class="info-grid">
        @if (!empty($best['class']))
            <div class="info-cell"><div class="k">Best Performing Class</div><div class="v">{{ $best['class']['class']->name }} · {{ number_format($best['class']['average'], 2) }}%</div></div>
        @endif
        @if (!empty($best['student']))
            <div class="info-cell"><div class="k">Best Performing Student</div><div class="v">{{ $best['student']->student?->full_name }} · {{ number_format((float) $best['student']->average, 2) }}%</div></div>
        @endif
        @if (!empty($best['subject']))
            <div class="info-cell"><div class="k">Best Performing Subject</div><div class="v">{{ $best['subject']['subject']?->name }} · {{ number_format($best['subject']['average'], 2) }} avg</div></div>
        @endif
    </div>

    <h3 class="section-title">Performance by Class</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th>Form / Class</th>
                <th class="ctr">Level</th>
                <th class="ctr">Students</th>
                <th class="ctr">Average</th>
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
                    <td class="ctr">{{ $row['pass_rate'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="5" class="ctr">No results recorded for the school in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.academic._footer')
@endsection
