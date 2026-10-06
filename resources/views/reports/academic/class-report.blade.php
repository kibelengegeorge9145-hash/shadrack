@extends((isset($pdfMode) && $pdfMode) ? 'reports.pdf' : 'reports.layout')

@section('title', 'Class Results Report')

@push('toolbar-actions')
    @php
        $exportUrl = route(($staffPrefix ?? 'admin') . '.reports.academic.export', [
            'type' => 'class',
            'year' => $year->id,
            'term' => $term->id,
            'class' => $class->id,
            ...(request('status') ? ['status' => request('status')] : []),
        ]);
    @endphp
    <a class="btn btn-ghost" href="{{ $exportUrl }}">Export Excel</a>
@endpush

@section('report')
    @include('reports.academic._school-header', [
        'reportTitle' => 'CLASS ACADEMIC PERFORMANCE REPORT',
        'reportSubtitle' => $class->name . ' · ' . $term->name . ' · ' . $year->name,
        'reference' => $reference ?? null,
        'generatedAt' => $generated_at ?? null,
    ])

    <div class="info-grid">
        <div class="info-cell"><div class="k">Class / Form</div><div class="v">{{ $class->name }}</div></div>
        <div class="info-cell"><div class="k">Level</div><div class="v">{{ $class->level }}</div></div>
        <div class="info-cell"><div class="k">Academic Year</div><div class="v">{{ $year->name }}</div></div>
        <div class="info-cell"><div class="k">Term</div><div class="v">{{ $term->name }}</div></div>
    </div>

    <h3 class="section-title">Class Report Summary</h3>
    <div class="stat-grid">
        <div class="stat-cell"><div class="k">Students</div><div class="v">{{ $summary['count'] }}</div></div>
        <div class="stat-cell"><div class="k">Class Average</div><div class="v">{{ number_format($summary['class_average'], 2) }}%</div></div>
        <div class="stat-cell"><div class="k">Highest Avg</div><div class="v">{{ number_format($summary['highest_average'], 2) }}%</div></div>
        <div class="stat-cell"><div class="k">Lowest Avg</div><div class="v">{{ number_format($summary['lowest_average'], 2) }}%</div></div>
        <div class="stat-cell"><div class="k">Pass Rate</div><div class="v gold">{{ $summary['pass_rate'] }}%</div></div>
        <div class="stat-cell"><div class="k">Division I</div><div class="v">{{ $summary['division_counts']['Division I'] }}</div></div>
        <div class="stat-cell"><div class="k">Division II</div><div class="v">{{ $summary['division_counts']['Division II'] }}</div></div>
        <div class="stat-cell"><div class="k">Division III</div><div class="v">{{ $summary['division_counts']['Division III'] }}</div></div>
        <div class="stat-cell"><div class="k">Division IV</div><div class="v">{{ $summary['division_counts']['Division IV'] }}</div></div>
        <div class="stat-cell"><div class="k">Division 0</div><div class="v">{{ $summary['division_counts']['Division 0'] }}</div></div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width:50px;">Position</th>
                <th>Admission No.</th>
                <th>Student Name</th>
                <th class="ctr" style="width:70px;">Total</th>
                <th class="ctr" style="width:70px;">Average</th>
                <th class="ctr" style="width:90px;">Division</th>
                <th class="ctr" style="width:60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                @php $r = $entry['result']; $s = $entry['student']; @endphp
                <tr>
                    <td class="ctr">{{ $r->position ?? '—' }}</td>
                    <td>{{ $s->admission_number }}</td>
                    <td>{{ $s->full_name }}</td>
                    <td class="ctr">{{ $r->total_marks }}</td>
                    <td class="ctr">{{ number_format((float) $r->average, 2) }}</td>
                    <td class="ctr">{{ $r->division ?? '—' }}</td>
                    <td class="ctr">{{ $r->failed_subjects === 0 || $r->failed_subjects < $r->passed_subjects ? 'PASS' : 'FAIL' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="ctr">No published results for this class, term and year.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.academic._footer')
@endsection
