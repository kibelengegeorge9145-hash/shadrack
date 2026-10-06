@extends('layouts.app')

@section('title', 'My Children Reports')

@section('content')
    <x-page-header title="My Children's Academic Reports" subtitle="View and print published report cards for your children." />

    <x-alert flash />

    @forelse ($children as $child)
        <x-card class="mb-6">
            <div class="flex items-center justify-between gap-3 border-b border-navy-100 dark:border-navy-700 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-navy-800 text-gold-300 text-xs font-bold shrink-0">
                        {{ $child->initials }}
                    </span>
                    <div>
                        <h3 class="font-display font-bold text-navy-900 dark:text-navy-100">{{ $child->full_name }}</h3>
                        <p class="text-xs text-navy-500">{{ $child->schoolClass?->name ?? 'No class' }} · {{ $child->admission_number }}</p>
                    </div>
                </div>
                <x-badge color="green">{{ $child->results->count() }} published</x-badge>
            </div>

            @if ($child->results->isEmpty())
                <p class="text-sm text-navy-500">No published reports available for this child yet.</p>
            @else
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($child->results as $result)
                        <div class="rounded-xl border border-navy-100 dark:border-navy-700 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-navy-900 dark:text-navy-100">{{ $result->term?->name }}</p>
                                    <p class="text-xs text-navy-500">{{ $result->academicYear?->name }}</p>
                                </div>
                                <x-badge color="green">Published</x-badge>
                            </div>
                            <p class="text-sm text-navy-600 dark:text-navy-300 mt-3">
                                Average <strong class="text-navy-900">{{ number_format($result->average, 2) }}%</strong> ·
                                Division <strong class="text-navy-900">{{ $result->division }}</strong>
                            </p>
                            <x-button href="{{ route('parent.reports.show', [$child, $result->term_id, $result->academic_year_id]) }}" variant="primary" icon="file-text" size="sm" class="mt-3 w-full justify-center">View &amp; Print</x-button>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    @empty
        <x-card>
            <x-empty-state icon="users" title="No Children" message="No student accounts are linked to this parent." />
        </x-card>
    @endforelse
@endsection
