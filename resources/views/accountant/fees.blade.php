@extends('layouts.app')

@section('title', 'Fees & Ledger')

@section('content')
    <x-page-header title="Fees & Student Ledger" subtitle="{{ $term ? $year->name . ' · ' . $term->name : 'No active term set' }}" />

    @if (session('success') || session('error') || session('warning') || session('info'))
        <x-alert flash />
    @endif

    <x-card title="Fee Structures" icon="credit-card">
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Name</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Category</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Amount</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Scope</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($structures as $structure)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                            <td class="px-4 py-3 align-middle font-medium text-navy-900 dark:text-navy-100">{{ $structure->name }}</td>
                            <td class="px-4 py-3 align-middle"><x-badge color="gold">{{ $structure->category }}</x-badge></td>
                            <td class="px-4 py-3 text-right align-middle font-semibold">TZS {{ number_format((float) $structure->amount, 2) }}</td>
                            <td class="px-4 py-3 align-middle text-xs text-navy-600 dark:text-navy-300">
                                {{ $structure->level ?? 'All levels' }}{{ $structure->schoolClass ? ' · ' . $structure->schoolClass->name : '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-navy-500 dark:text-navy-400">No active fee structures.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-card title="Student Ledger" icon="file-text" class="mt-6">
        <form method="GET" action="{{ route('accountant.fees.index') }}" class="mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative sm:col-span-2">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-navy-400">
                    <x-icon name="search" class="w-4 h-4" />
                </span>
                <input type="text" name="student" value="{{ request('student') }}" placeholder="Search by student name or admission number..."
                    class="w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 pl-10 py-2.5 focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400" />
            </div>
            <div class="flex gap-3">
                <select name="status" class="flex-1 rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 px-3 py-2.5">
                    <option value="">All statuses</option>
                    @foreach (['unpaid' => 'Unpaid', 'partial' => 'Partially Paid', 'paid' => 'Fully Paid'] as $value => $label)
                        <option value="{{ $value }}" @if (request('status') === $value) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="secondary" icon="filter">Filter</x-button>
            </div>
        </form>

        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Student</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Class</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Fee</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Required</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Paid</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Balance</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($ledger as $entry)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                            <td class="px-4 py-3.5 align-middle">
                                <p class="font-medium text-navy-900 dark:text-navy-100">{{ $entry->student?->full_name ?? '—' }}</p>
                                @if ($entry->student?->admission_number)
                                    <p class="text-xs text-navy-500 dark:text-navy-400">{{ $entry->student->admission_number }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 align-middle">{{ $entry->student?->schoolClass?->name ?? '—' }}</td>
                            <td class="px-4 py-3.5 align-middle">{{ $entry->feeStructure?->name ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-right align-middle">TZS {{ number_format((float) $entry->required_amount, 2) }}</td>
                            <td class="px-4 py-3.5 text-right align-middle text-emerald-600 dark:text-emerald-300">TZS {{ number_format((float) $entry->paid_amount, 2) }}</td>
                            <td class="px-4 py-3.5 text-right align-middle font-semibold {{ (float) $entry->balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-navy-900 dark:text-navy-100' }}">TZS {{ number_format((float) $entry->balance, 2) }}</td>
                            <td class="px-4 py-3.5 align-middle">
                                @if ((float) $entry->balance <= 0 && (float) $entry->paid_amount > 0)
                                    <x-badge color="green">Paid</x-badge>
                                @elseif ((float) $entry->paid_amount > 0)
                                    <x-badge color="amber">Partial</x-badge>
                                @elseif ($entry->is_waived)
                                    <x-badge color="gray">Waived</x-badge>
                                @else
                                    <x-badge color="red">Unpaid</x-badge>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-navy-500 dark:text-navy-400">No ledger entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $ledger->links() }}
        </div>
    </x-card>
@endsection
