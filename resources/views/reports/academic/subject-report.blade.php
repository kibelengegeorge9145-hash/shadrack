@extends((isset($pdfMode) && $pdfMode) ? 'reports.pdf' : 'reports.layout')

@section('title', 'Subject Performance Report')

@push('toolbar-actions')
    @php
        $exportUrl = route(($staffPrefix ?? 'admin') . '.reports.academic.export', [
            'type' => 'subject',
            'year' => $year->id,
            'term' => $term->id,
            'class' => $class->id,
            'subject' => $subject->id,
            ...(request('status') ? ['status' => request('status')] : []),
        ]);
    @endphp
    <a class="btn btn-ghost" href="{{ $exportUrl }}">Export Excel</a>
@endpush

@section('report')
    @include('reports.academic._school-header', [
        'reportTitle' => 'SUBJECT PERFORMANCE REPORT',
        'reportSubtitle' => $subject->name . ' (' . $subject->code . ') · ' . $class->name . ' · ' . $term->name . ' · ' . $year->name,
        'reference' => $reference ?? null,
        'generatedAt' => $generated_at ?? null,
    ])

    <div class="stat-grid">
        <div class="stat-cell"><div class="k">Students</div><div class="v">{{ $summary['count'] }}</div></div>
        <div class="stat-cell"><div class="k">Average Mark</div><div class="v">{{ number_format($summary['average_mark'], 2) }}</div></div>
        <div class="stat-cell"><div class="k">Highest Mark</div><div class="v">{{ $summary['highest_mark'] }}</div></div>
        <div class="stat-cell"><div class="k">Lowest Mark</div><div class="v">{{ $summary['lowest_mark'] }}</div></div>
        <div class="stat-cell"><div class="k">Pass Rate</div><div class="v gold">{{ $summary['pass_rate'] }}%</div></div>
        <div class="stat-cell"><div class="k">Failure Rate</div><div class="v">{{ $summary['failure_rate'] }}%</div></div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width:50px;">Position</th>
                <th>Student</th>
                <th>Admission No.</th>
                <th class="ctr" style="width:70px;">Marks</th>
                <th class="ctr" style="width:60px;">Grade</th>
                <th class="ctr" style="width:60px;">Points</th>
                <th class="ctr" style="width:60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rankMap = collect();
                $marksSorted = $rows->pluck('marks')->map(fn ($m) => (float) $m)->sortDesc()->values();
                $marksSorted->each(function ($m, $i) use (&$rankMap) {
                    if (!$rankMap->has((string) $m)) { $rankMap[(string) $m] = $i + 1; }
                });
            @endphp
            @forelse ($rows as $i => $row)
                <tr>
                    <td class="ctr">{{ $rankMap[(string) $row['marks']] ?? $i + 1 }}</td>
                    <td>{{ $row['student']->full_name }}</td>
                    <td>{{ $row['student']->admission_number }}</td>
                    <td class="ctr">{{ $row['marks'] }}</td>
                    <td class="ctr"><span class="grade-pill">{{ $row['grade'] }}</span></td>
                    <td class="ctr">{{ $row['points'] }}</td>
                    <td class="ctr">{{ $row['grade'] !== 'F' ? 'PASS' : 'FAIL' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="ctr">No results recorded for this subject in the selected class, term and year.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.academic._footer')
@endsection
