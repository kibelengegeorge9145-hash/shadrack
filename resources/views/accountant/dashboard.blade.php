@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-page-header title="Finance Dashboard" subtitle="{{ $term ? $year->name . ' · ' . $term->name : 'No active academic year/term set' }}" />

    @if (session('success') || session('error') || session('warning') || session('info'))
        <x-alert flash />
    @endif

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <x-stat-card label="Collected This Term" :value="'TZS ' . number_format((float) $collected, 2)" icon="banknote" color="green" :sub="$term ? $term->name : 'All time'" />
        <x-stat-card label="Expected Revenue" :value="'TZS ' . number_format($summary->expected, 2)" icon="target" color="gold" :sub="'Required fees' " />
        <x-stat-card label="Outstanding Balance" :value="'TZS ' . number_format($summary->outstanding, 2)" icon="alert" color="red" :sub="'Still to be collected'" />
        <x-stat-card label="Active Students" :value="$totalStudents" icon="users" color="navy" :sub="'Enrolled students'" />
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <x-card title="Payments by Month" icon="bar-chart" class="lg:col-span-2">
            <div class="h-56" data-chart="bar" data-values='{{ json_encode($chartMonths['values']) }}' data-labels='{{ json_encode($chartMonths['labels']) }}' data-color="#2e4f79"></div>
        </x-card>

        <x-card title="Recent Payments" icon="receipt" class="lg:col-span-1">
            <ul class="divide-y divide-navy-100 dark:divide-navy-700/60">
                @forelse ($recentPayments as $p)
                    <li class="py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-medium text-sm text-navy-900 dark:text-navy-100 truncate">{{ $p->student?->full_name ?? 'Unknown' }}</p>
                            <span class="text-sm font-semibold text-navy-900 dark:text-navy-100 shrink-0">TZS {{ number_format((float) $p->amount, 2) }}</span>
                        </div>
                        <div class="mt-0.5 flex items-center justify-between gap-2 text-xs text-navy-500 dark:text-navy-400">
                            <span>{{ $p->feeStructure?->name ?? '—' }} · {{ $p->method_label }}</span>
                            <span>{{ $p->payment_date?->format('d M') }}</span>
                        </div>
                    </li>
                @empty
                    <li class="py-8 text-center text-sm text-navy-400">No payments recorded yet.</li>
                @endforelse
            </ul>
        </x-card>
    </div>
@endsection
