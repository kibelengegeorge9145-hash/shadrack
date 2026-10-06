@extends('layouts.app')

@section('title', 'My Results')

@section('content')
    <x-page-header title="My Results" subtitle="View your published examination results" />

    <x-alert flash />

    @if ($results->isEmpty())
        <x-card>
            <x-empty-state icon="file-text" title="No Results Found" message="Your published results will appear here." />
        </x-card>
    @else
        <div class="space-y-6">
            @foreach ($results as $result)
                <x-card title="{{ $result->term?->name ?? 'Term' }} · {{ $result->academicYear?->name ?? 'Year' }}" icon="file-text">
                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        <x-badge color="green">Published</x-badge>
                        <span class="text-sm text-navy-500">{{ $result->schoolClass?->name }}</span>
                        @if ($result->published_at)
                            <span class="text-xs text-navy-400">Published {{ $result->published_at->format('d M Y') }}</span>
                        @endif
                    </div>
                    <div class="overflow-x-auto -mx-5 px-5">
                        <table class="w-full min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-navy-100 dark:border-navy-700">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Subject</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Marks</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Grade</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Points</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                                @foreach ($result->items as $item)
                                    <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                                        <td class="px-4 py-3 font-medium text-navy-900 dark:text-navy-100">{{ $item->subject?->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right text-navy-700 dark:text-navy-300">{{ $item->marks }}</td>
                                        <td class="px-4 py-3 text-center"><x-badge color="gold">{{ $item->grade }}</x-badge></td>
                                        <td class="px-4 py-3 text-right font-semibold text-navy-900 dark:text-navy-100">{{ $item->points }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-4 text-sm border-t border-navy-100 dark:border-navy-700 pt-4">
                        <span class="text-navy-600 dark:text-navy-300">Total Points: <strong class="text-navy-900 dark:text-navy-100">{{ $result->total_points }}</strong></span>
                        <span class="text-navy-600 dark:text-navy-300">Average: <strong class="text-navy-900 dark:text-navy-100">{{ number_format($result->average, 1) }}</strong></span>
                        <span class="text-navy-600 dark:text-navy-300">Division: <x-badge color="navy">{{ $result->division }}</x-badge></span>
                        @if ($result->position)
                            <span class="text-navy-600 dark:text-navy-300">Position: <strong class="text-navy-900 dark:text-navy-100">{{ $result->position }}</strong></span>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
@endsection
