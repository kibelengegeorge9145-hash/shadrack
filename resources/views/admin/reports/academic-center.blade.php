@extends('layouts.app')

@section('title', 'Academic Report Center')

@section('content')
    <x-page-header title="Academic Report Center" subtitle="Generate, preview, print and export academic performance reports." />

    <x-alert type="success" flash class="mb-6" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route($staffPrefix . '.reports.academic.preview') }}" class="grid md:grid-cols-4 gap-4 items-end" data-report-form
            data-terms-url="{{ route($staffPrefix . '.reports.academic.terms') }}"
            data-selected-term="{{ request('term_id', request('term')) }}">
            <div>
                <x-label for="report_type">Report Type</x-label>
                <select name="report_type" id="report_type" data-report-type class="mt-1.5 w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400 transition">
                    @foreach (\App\Http\Requests\AcademicReportRequest::TYPES as $value => $label)
                        <option value="{{ $value }}" @selected((string) old('report_type', request('report_type', 'student')) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <x-select name="academic_year_id" label="Academic Year"
                :options="$years->pluck('name', 'id')"
                placeholder="Current year"
                :selected="request('academic_year_id')" />

            <x-select name="term_id" label="Term" :options="[]" placeholder="First term" data-term-select />

            <x-select name="status" label="Result Status" :options="['all' => 'All statuses', 'submitted' => 'Submitted', 'reviewed' => 'Reviewed', 'published' => 'Published']"
                placeholder="All statuses" :selected="request('status', 'all')" />

            <x-select name="level" label="Level" :options="['O-Level' => 'O-Level', 'A-Level' => 'A-Level']"
                placeholder="All levels" :selected="request('level')" />

            <x-select name="school_class_id" label="Class / Form"
                :options="$classes->pluck('name', 'id')"
                placeholder="All classes"
                :selected="request('school_class_id')" />

            <x-select name="subject_id" label="Subject"
                :options="$subjects->mapWithKeys(fn ($s) => [$s->id => $s->name . ' (' . $s->code . ')'])->all()"
                placeholder="Select subject"
                :selected="request('subject_id')" />

            <x-select name="student_id" label="Student"
                :options="$students->mapWithKeys(fn ($s) => [$s->id => $s->full_name . ($s->schoolClass ? ' — ' . $s->schoolClass->name : '')])->all()"
                placeholder="Select student"
                :selected="request('student_id')" />

            <div class="flex items-center gap-2">
                <x-button type="submit" icon="file-text">Generate Report</x-button>
                <x-button href="{{ route($staffPrefix . '.reports.academic') }}" variant="secondary" icon="refresh">Reset</x-button>
            </div>
        </form>
        <p class="mt-4 text-xs text-navy-500">Each report opens in a clean, print-ready view. Use <strong>Print</strong> or <strong>Download PDF</strong> (print to PDF from the browser). Class and Subject reports also offer <strong>Export Excel</strong>.</p>
    </x-card>

    <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ([
            ['type' => 'student', 'icon' => 'user', 'title' => 'Student Card', 'desc' => 'Per-student subject results, attendance, comments.'],
            ['type' => 'class', 'icon' => 'users', 'title' => 'Class Results', 'desc' => 'Whole-class ranking, averages and divisions.'],
            ['type' => 'subject', 'icon' => 'book-open', 'title' => 'Subject Performance', 'desc' => 'Subject pass/fail rates and mark distribution.'],
            ['type' => 'term', 'icon' => 'calendar', 'title' => 'Term Performance', 'desc' => 'All classes compared for a single term.'],
            ['type' => 'year', 'icon' => 'layers', 'title' => 'Academic Year', 'desc' => 'Term-over-term comparison for the whole year.'],
            ['type' => 'school', 'icon' => 'school', 'title' => 'Whole School', 'desc' => 'School-wide averages, highlights and class table.'],
            ['type' => 'olevel', 'icon' => 'graduation-cap', 'title' => 'O-Level', 'desc' => 'Ordinary level classes and subjects.'],
            ['type' => 'alevel', 'icon' => 'award', 'title' => 'A-Level', 'desc' => 'Advanced level classes and subjects.'],
        ] as $card)
            <x-card class="hover:shadow-lg hover:shadow-navy-900/5 transition-shadow">
                <div class="flex items-start gap-4">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-navy-800 text-gold-300 shrink-0">
                        <x-icon :name="$card['icon']" class="w-5 h-5" />
                    </span>
                    <div class="min-w-0">
                        <h3 class="font-display font-bold text-navy-900 dark:text-navy-100">{{ $card['title'] }}</h3>
                        <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">{{ $card['desc'] }}</p>
                        <a href="javascript:void(0)" class="inline-flex items-center gap-1 text-sm font-semibold text-gold-600 hover:text-gold-500 mt-3" data-quick-report="{{ $card['type'] }}">
                            Generate <x-icon name="arrow-up" class="w-3.5 h-3.5 rotate-90" />
                        </a>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>

    <p class="text-xs text-navy-400 mt-6">Refer to <em>Student Report Cards</em> and the results module for entering and publishing results before generating reports.</p>
@endsection
