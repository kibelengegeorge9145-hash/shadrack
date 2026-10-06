@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
    <x-page-header title="Parent Dashboard" subtitle="Welcome, {{ $parent->full_name }}" />

    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <x-stat-card label="My Children" value="{{ $children->count() }}" icon="users" color="navy" />
        <x-stat-card label="Results Available" value="{{ $resultsCount }}" icon="file-text" color="gold" />
        <x-stat-card label="Total Fee Balance" value="TZS {{ number_format($totalBalance, 0) }}" icon="credit-card" color="{{ $totalBalance > 0 ? 'red' : 'green' }}" />
    </div>

    <x-card title="My Children" icon="users">
        @if ($children->isEmpty())
            <x-empty-state icon="users" title="No Children" message="No children linked to your account." />
        @else
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-navy-100 dark:border-navy-700">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Student</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Class</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Admission No.</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @foreach ($children as $child)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-navy-800 text-gold-300 text-sm font-bold shrink-0">{{ $child->initials }}</span>
                                        <div>
                                            <p class="font-medium text-navy-900 dark:text-navy-100">{{ $child->full_name }}</p>
                                            <p class="text-xs text-navy-500">{{ $child->level }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $child->schoolClass?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-navy-600 dark:text-navy-400 font-mono text-xs">{{ $child->admission_number }}</td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button href="{{ route('parent.results.index') }}" variant="ghost" size="sm" icon="file-text">Results</x-button>
                                        <x-button href="{{ route('parent.fees.index') }}" variant="ghost" size="sm" icon="credit-card">Fees</x-button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
@endsection
