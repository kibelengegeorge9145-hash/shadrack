@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <x-page-header title="Finance Reports" subtitle="{{ $term ? $year->name . ' · ' . $term->name : 'No active term set' }}">
        <x-slot:actions>
            <x-button variant="gold" icon="printer" onclick="window.print()">Export / Print</x-button>
        </x-slot:actions>
    </x-page-header>

    @if (session('success') || session('error') || session('warning') || session('info'))
        <x-alert flash />
    @endif

    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-6">
        <x-stat-card label="Total Collected" :value="'TZS ' . number_format($collected, 2)" icon="banknote" color="green" />
        <x-stat-card label="Expected Revenue" :value="'TZS ' . number_format($expected, 2)" icon="target" color="gold" />
        <x-stat-card label="Outstanding Balance" :value="'TZS ' . number_format($outstanding, 2)" icon="alert" color="red" />
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <x-card title="Payments by Method" icon="pie-chart">
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-navy-100 dark:border-navy-700">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Method</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Count</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @forelse ($byMethod as $row)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                <td class="px-4 py-3 align-middle"><x-badge color="navy">{{ ucfirst($row->method) }}</x-badge></td>
                                <td class="px-4 py-3 text-right align-middle">{{ $row->count }}</td>
                                <td class="px-4 py-3 text-right align-middle font-semibold">TZS {{ number_format((float) $row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-navy-500 dark:text-navy-400">No payments recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="Top Fee Structures by Revenue" icon="bar-chart">
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-navy-100 dark:border-navy-700">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Fee Structure</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Payments</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @forelse ($topStructures as $row)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                <td class="px-4 py-3 align-middle font-medium text-navy-900 dark:text-navy-100">{{ $row['name'] }}</td>
                                <td class="px-4 py-3 text-right align-middle">{{ $row['payments'] }}</td>
                                <td class="px-4 py-3 text-right align-middle font-semibold">TZS {{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-navy-500 dark:text-navy-400">No revenue data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
@endsection
