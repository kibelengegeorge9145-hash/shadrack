@extends((isset($pdfMode) && $pdfMode) ? 'reports.pdf' : 'reports.layout')

@section('title', 'Academic Year Report')

@section('report')
    @include('reports.academic._school-header', [
        'reportTitle' => 'ACADEMIC YEAR PERFORMANCE REPORT',
        'reportSubtitle' => 'Academic Year ' . $year->name,
        'reference' => $reference ?? null,
        'generatedAt' => $generated_at ?? null,
    ])

    <h3 class="section-title">Term Comparison</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th>Term</th>
                <th class="ctr">Students</th>
                <th class="ctr">Average</th>
                <th class="ctr">Highest Avg</th>
                <th class="ctr">Lowest Avg</th>
                <th class="ctr">Pass Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($terms as $row)
                <tr>
                    <td>{{ $row['term']->name }}</td>
                    <td class="ctr">{{ $row['students'] }}</td>
                    <td class="ctr">{{ number_format($row['average'], 2) }}%</td>
                    <td class="ctr">{{ number_format($row['highest_average'], 2) }}%</td>
                    <td class="ctr">{{ number_format($row['lowest_average'], 2) }}%</td>
                    <td class="ctr">{{ $row['pass_rate'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="6" class="ctr">No results recorded for this academic year.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Subject Performance Across the Year</h3>
    <table class="report-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th class="ctr">Code</th>
                <th class="ctr">Level</th>
                <th class="ctr">Entries</th>
                <th class="ctr">Average Mark</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subject_rows as $row)
                <tr>
                    <td>{{ $row['subject']->name }}</td>
                    <td class="ctr">{{ $row['subject']->code }}</td>
                    <td class="ctr">{{ $row['subject']->level }}</td>
                    <td class="ctr">{{ $row['count'] }}</td>
                    <td class="ctr">{{ number_format($row['average_mark'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="ctr">No subject data available for this academic year.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="stat-cell"><div class="k">Terms Compared</div><div class="v">{{ $summary['terms'] }}</div></div>
        <div class="stat-cell"><div class="k">Average Across Terms</div><div class="v">{{ number_format($summary['term_average'], 2) }}%</div></div>
        <div class="stat-cell"><div class="k">Academic Year</div><div class="v">{{ $year->name }}</div></div>
    </div>

    @include('reports.academic._footer')
@endsection
