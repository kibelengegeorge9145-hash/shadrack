@extends('layouts.app')

@section('title', 'Results')

@section('content')
    <x-page-header title="Results" subtitle="Filter and review term results across all classes." />

    <x-alert type="success" flash class="mb-6" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.results.index') }}" class="grid md:grid-cols-4 gap-4 items-end">
            <x-select name="term_id" label="Term" :options="$termOptions" placeholder="All terms" :selected="request('term_id')" />
            <x-select name="school_class_id" label="Class" :options="$classes->pluck('name', 'id')" placeholder="All classes" :selected="request('school_class_id')" />
            <x-select name="status" label="Status" :options="$statusOptions" placeholder="All statuses" :selected="request('status')" />
            <div class="flex items-center gap-2">
                <x-button type="submit" icon="filter">Filter</x-button>
                @if (request('term_id') || request('school_class_id') || request('status'))
                    <x-button href="{{ route('admin.results.index') }}" variant="secondary" icon="x">Clear</x-button>
                @endif
            </div>
        </form>
    </x-card>

    <x-card padding="false">
        <div class="px-5 pt-5 pb-3 flex items-center justify-between gap-3">
            <div>
                <h3 class="font-display text-lg font-bold text-navy-900 dark:text-navy-50">Registered Results</h3>
                <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">{{ $results->total() }} result(s) found</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700 text-left">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Student</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Class</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Term</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 text-center">Avg</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 text-center">Points</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Division</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 text-center">Position</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($results as $result)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-navy-800 text-gold-300 text-xs font-bold shrink-0">
                                        {{ $result->student?->initials }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="font-medium text-navy-900 dark:text-navy-100 truncate">{{ $result->student?->full_name ?? '—' }}</p>
                                        <p class="text-xs text-navy-400">{{ $result->student?->admission_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">{{ $result->schoolClass?->name ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">{{ $result->term?->name ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-center font-semibold text-navy-900 dark:text-navy-100">{{ number_format($result->average, 2) }}</td>
                            <td class="px-4 py-3.5 text-center font-semibold text-navy-900 dark:text-navy-100">{{ $result->total_points }}</td>
                            <td class="px-4 py-3.5">
                                <x-badge color="navy">{{ $result->division ?? '—' }}</x-badge>
                            </td>
                            <td class="px-4 py-3.5 text-center font-semibold text-navy-900 dark:text-navy-100">{{ $result->position ?? '—' }}</td>
                            <td class="px-4 py-3.5">
                                <x-badge :color="match ($result->status) {
                                    'published' => 'green',
                                    'reviewed' => 'blue',
                                    'submitted' => 'gold',
                                    default => 'gray',
                                }">{{ ucfirst($result->status) }}</x-badge>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <x-button href="{{ route('admin.results.report-card', [$result->student_id, $result->term_id]) }}" size="sm" variant="secondary" icon="eye">Report Card</x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <x-empty-state icon="file-text" title="No results found" message="Adjust the filters above or save marks from the teacher area first." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($results->hasPages())
            <div class="px-5 py-4 border-t border-navy-100 dark:border-navy-700/60">
                {{ $results->links() }}
            </div>
        @endif
    </x-card>
@endsection