@extends((isset($pdfMode) && $pdfMode) ? 'reports.pdf' : 'reports.layout')

@section('title', 'Student Report Card')

@php
    $backRoute = match (auth()->user()->role) {
        'student' => route('student.reports.index'),
        'parent' => route('parent.reports.index'),
        'teacher' => route('teacher.reports.academic'),
        default => route('admin.reports.academic'),
    };
    $staff = auth()->user()->hasRole('admin', 'teacher');
    $isAdmin = auth()->user()->hasRole('admin');
@endphp

@push('toolbar-actions')
    @if (!empty($result) && $isAdmin)
        <a class="btn btn-ghost" href="{{ route('admin.results.report-card', [$student, $term]) }}">Dashboard Report Card</a>
    @endif
@endpush

@section('report')
    @include('reports.academic._school-header', [
        'reportTitle' => 'STUDENT ACADEMIC REPORT',
        'reportSubtitle' => 'Full academic performance for the term',
        'reference' => $reference ?? null,
        'generatedAt' => $generated_at ?? null,
    ])

    @if (empty($result))
        <div class="report-empty">
            <div class="big">No Results Recorded</div>
            <p>There is no {{ $term->name }} result for {{ $student->full_name }}. Results must be entered and published before a report can be generated.</p>
        </div>
    @else
        <div class="info-grid">
            <div class="info-cell"><div class="k">Student Name</div><div class="v">{{ $student->full_name }}</div></div>
            <div class="info-cell"><div class="k">Admission No.</div><div class="v">{{ $student->admission_number }}</div></div>
            <div class="info-cell"><div class="k">Class / Form</div><div class="v">{{ $result->schoolClass?->name ?? $student->schoolClass?->name }} ({{ $result->level }})</div></div>
            <div class="info-cell"><div class="k">Academic Year</div><div class="v">{{ $year->name }}</div></div>
            <div class="info-cell"><div class="k">Term</div><div class="v">{{ $result->term?->name ?? $term->name }}</div></div>
            <div class="info-cell"><div class="k">Gender</div><div class="v">{{ ucfirst((string) $student->gender) }}</div></div>
        </div>

        <h3 class="section-title">Subject Results</h3>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width:40px;">No.</th>
                    <th>Subject</th>
                    <th class="ctr" style="width:70px;">Marks</th>
                    <th class="ctr" style="width:60px;">Grade</th>
                    <th class="ctr" style="width:60px;">Points</th>
                    <th>Teacher Comment</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($result->items as $item)
                    <tr>
                        <td class="ctr">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $item->subject?->name ?? '—' }}</td>
                        <td class="ctr">{{ $item->marks ?? '—' }}</td>
                        <td class="ctr"><span class="grade-pill">{{ $item->grade ?? '—' }}</span></td>
                        <td class="ctr">{{ $item->points ?? '—' }}</td>
                        <td>{{ $item->teacher_comment ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="ctr">No subjects recorded for this term.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3 class="section-title">Student Summary</h3>
        <div class="stat-grid">
            <div class="stat-cell"><div class="k">Total Marks</div><div class="v">{{ $result->total_marks }}</div></div>
            <div class="stat-cell"><div class="k">Average</div><div class="v">{{ number_format((float) $result->average, 2) }}%</div></div>
            <div class="stat-cell"><div class="k">Position</div><div class="v">{{ $result->position ?? '—' }}</div></div>
            <div class="stat-cell"><div class="k">Division</div><div class="v gold">{{ str_replace('Division ', 'Div. ', (string) $result->division) }}</div></div>
            <div class="stat-cell"><div class="k">Passed Subjects</div><div class="v">{{ $result->passed_subjects }}</div></div>
            <div class="stat-cell"><div class="k">Failed Subjects</div><div class="v">{{ $result->failed_subjects }}</div></div>
            <div class="stat-cell"><div class="k">Subjects Taken</div><div class="v">{{ $result->items->count() }}</div></div>
            <div class="stat-cell"><div class="k">Status</div><div class="v gold">{{ $result->failed_subjects === 0 || $result->failed_subjects < $result->passed_subjects ? 'PASS' : 'FAIL' }}</div></div>
        </div>

        @if (!empty($attendance['has_data']))
            <h3 class="section-title">Attendance for {{ $term->name }}</h3>
            <div class="stat-grid">
                <div class="stat-cell"><div class="k">School Days</div><div class="v">{{ $attendance['days'] }}</div></div>
                <div class="stat-cell"><div class="k">Present</div><div class="v">{{ $attendance['present'] }}</div></div>
                <div class="stat-cell"><div class="k">Absent</div><div class="v">{{ $attendance['absent'] }}</div></div>
                <div class="stat-cell"><div class="k">Late</div><div class="v">{{ $attendance['late'] }}</div></div>
                <div class="stat-cell"><div class="k">Attendance %</div><div class="v gold">{{ $attendance['percentage'] }}%</div></div>
            </div>
        @endif

        <h3 class="section-title">Comments</h3>
        <div class="comments-box">
            <div class="cb-label">Class Teacher / Academic Comment</div>
            <p>{{ $result->teacher_comment ?? 'No comment recorded.' }}</p>
        </div>
        @if ($result->head_comment)
            <div class="comments-box">
                <div class="cb-label">Head of School Comment</div>
                <p>{{ $result->head_comment }}</p>
            </div>
        @endif

        @include('reports.academic._footer')
    @endif
@endsection
