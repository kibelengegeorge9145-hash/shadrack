@extends('layouts.app')

@section('title', 'Report Card')

@push('styles')
    <style>
        @media print {
            body * { visibility: hidden; }
            #report-card, #report-card * { visibility: visible; }
            #report-card { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
@endpush

@section('content')
    <div class="no-print mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-navy-900 dark:text-navy-50 tracking-tight">Report Card</h1>
            <p class="mt-1 text-sm text-navy-500 dark:text-navy-300">{{ $student->full_name }} · {{ $term->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-button href="{{ route('admin.results.index') }}" variant="secondary" icon="chevron-left">Back to Results</x-button>
            <x-button type="button" icon="printer" onclick="window.print()">Print</x-button>
        </div>
    </div>

    @if (!$result)
        <x-card>
            <x-empty-state
                icon="file-text"
                title="No result recorded"
                message="There is no saved result for this student in {{ $term->name }}. Marks must be entered in the teacher area first."
            />
        </x-card>
    @else
        <div id="report-card" class="max-w-3xl mx-auto bg-white dark:bg-navy-900 border border-navy-100 dark:border-navy-700 rounded-xl shadow-card overflow-hidden">
            <div class="bg-navy-950 text-center px-6 py-8 print:bg-navy-950">
                <h2 class="font-display text-2xl font-extrabold text-white tracking-tight">{{ config('app.name') }}</h2>
                <p class="mt-1 text-xs uppercase tracking-[0.22em] text-gold-300">Terminal Report Card</p>
            </div>

            <div class="p-6 sm:p-8">
                <div class="grid sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-400">Student Name</p>
                        <p class="mt-0.5 font-semibold text-navy-900 dark:text-navy-50">{{ $student->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-400">Admission No.</p>
                        <p class="mt-0.5 font-semibold text-navy-900 dark:text-navy-50">{{ $student->admission_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-400">Class</p>
                        <p class="mt-0.5 font-medium text-navy-900 dark:text-navy-50">{{ $result->schoolClass?->name }} ({{ $result->level }})</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-400">Term / Year</p>
                        <p class="mt-0.5 font-medium text-navy-900 dark:text-navy-50">{{ $term->name }} · {{ $result->academicYear?->name }}</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-navy-100 dark:border-navy-700">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-cream-100 dark:bg-navy-800 text-left text-navy-800 dark:text-navy-100">
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider">#</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider">Subject</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold uppercase tracking-wider">Marks</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold uppercase tracking-wider">Grade</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold uppercase tracking-wider">Points</th>
                                <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider">Comment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                            @forelse ($result->items as $item)
                                <tr>
                                    <td class="px-4 py-2.5 text-navy-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2.5 font-medium text-navy-900 dark:text-navy-100">{{ $item->subject?->name ?? '—' }}</td>
                                    <td class="px-4 py-2.5 text-center font-semibold text-navy-900 dark:text-navy-100">{{ $item->marks ?? '—' }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-navy-800 text-gold-300 text-xs font-bold">{{ $item->grade }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center text-navy-700 dark:text-navy-200">{{ $item->points }}</td>
                                    <td class="px-4 py-2.5 text-navy-500 dark:text-navy-300">{{ $item->teacher_comment ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-navy-400">No subjects for this record.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 grid sm:grid-cols-4 gap-px overflow-hidden rounded-lg border border-navy-100 dark:border-navy-700 bg-navy-100 dark:bg-navy-700">
                    <div class="bg-white dark:bg-navy-900 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-400">Average</p>
                        <p class="mt-0.5 font-display text-xl font-bold text-navy-900 dark:text-navy-50">{{ number_format($result->average, 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-navy-900 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-400">Total Points</p>
                        <p class="mt-0.5 font-display text-xl font-bold text-navy-900 dark:text-navy-50">{{ $result->total_points }}</p>
                    </div>
                    <div class="bg-white dark:bg-navy-900 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-400">Division</p>
                        <p class="mt-0.5 font-display text-lg font-bold text-gold-600 dark:text-gold-300">{{ $result->division ?? '—' }}</p>
                    </div>
                    <div class="bg-white dark:bg-navy-900 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-400">Position</p>
                        <p class="mt-0.5 font-display text-lg font-bold text-navy-900 dark:text-navy-50">{{ $result->position ?? '—' }}</p>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    <x-badge color="green">{{ $result->passed_subjects }} passed</x-badge>
                    <x-badge color="red">{{ $result->failed_subjects }} failed</x-badge>
                    <x-badge color="gray">{{ $result->items->count() }} subjects</x-badge>
                </div>

                @if ($result->teacher_comment)
                    <div class="mt-6 rounded-lg bg-cream-100 dark:bg-navy-800 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Teacher's Comment</p>
                        <p class="mt-1 text-sm text-navy-800 dark:text-navy-100">{{ $result->teacher_comment }}</p>
                    </div>
                @endif

                <div class="mt-10 grid grid-cols-2 gap-8">
                    <div class="border-t-2 border-navy-200 dark:border-navy-600 pt-2 text-center">
                        <p class="text-xs font-medium text-navy-500 dark:text-navy-400">Class Teacher</p>
                    </div>
                    <div class="border-t-2 border-navy-200 dark:border-navy-600 pt-2 text-center">
                        <p class="text-xs font-medium text-navy-500 dark:text-navy-400">Head of School</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection