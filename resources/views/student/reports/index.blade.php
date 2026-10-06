@extends('layouts.app')

@section('title', 'My Reports')

@section('content')
    <x-page-header title="My Academic Reports" subtitle="View and print your published report cards for each term." />

    <x-alert flash />

    @if ($results->isEmpty())
        <x-card>
            <x-empty-state icon="file-text" title="No Published Reports" message="Your report cards will appear here once results have been published for a term." />
        </x-card>
    @else
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($results as $result)
                <x-card>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-display font-bold text-navy-900 dark:text-navy-100">{{ $result->term?->name }}</h3>
                            <p class="text-xs text-navy-500 mt-0.5">{{ $result->academicYear?->name }} · {{ $result->schoolClass?->name }}</p>
                        </div>
                        <x-badge color="green">Published</x-badge>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg bg-cream-50 dark:bg-navy-800/40 p-3">
                            <p class="text-xs text-navy-400 uppercase tracking-wider">Average</p>
                            <p class="font-semibold text-navy-900 dark:text-navy-100">{{ number_format($result->average, 2) }}%</p>
                        </div>
                        <div class="rounded-lg bg-cream-50 dark:bg-navy-800/40 p-3">
                            <p class="text-xs text-navy-400 uppercase tracking-wider">Division</p>
                            <p class="font-semibold text-navy-900 dark:text-navy-100">{{ $result->division }}</p>
                        </div>
                        <div class="rounded-lg bg-cream-50 dark:bg-navy-800/40 p-3">
                            <p class="text-xs text-navy-400 uppercase tracking-wider">Subjects</p>
                            <p class="font-semibold text-navy-900 dark:text-navy-100">{{ $result->items->count() }}</p>
                        </div>
                        <div class="rounded-lg bg-cream-50 dark:bg-navy-800/40 p-3">
                            <p class="text-xs text-navy-400 uppercase tracking-wider">Position</p>
                            <p class="font-semibold text-navy-900 dark:text-navy-100">{{ $result->position ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-button href="{{ route('student.reports.show', [$student, $result->term_id, $result->academic_year_id]) }}" variant="primary" icon="file-text" class="w-full justify-center">View &amp; Print Report</x-button>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
@endsection
