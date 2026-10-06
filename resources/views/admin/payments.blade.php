@extends('layouts.app')

@section('title', 'Payments')

@section('content')
    <x-page-header title="Payments" subtitle="View all payments recorded across the school." />

    @if (session('success') || session('error') || session('warning') || session('info'))
        <x-alert flash />
    @endif

    <x-card title="All Payments" icon="banknote">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-navy-400">
                    <x-icon name="search" class="w-4 h-4" />
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by student or receipt number..."
                    class="w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 pl-10 py-2.5 focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400" />
            </div>
            <select name="status" class="rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 px-3 py-2.5">
                <option value="">All statuses</option>
                @foreach (['recorded' => 'Recorded', 'reversed' => 'Reversed', 'pending' => 'Pending'] as $value => $label)
                    <option value="{{ $value }}" @if (request('status') === $value) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
            <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
        </form>

        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Receipt</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Student</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Fee</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Method</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Amount</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                            <td class="px-4 py-3.5 align-middle font-mono text-xs text-navy-700 dark:text-navy-200">{{ $payment->receipt_number }}</td>
                            <td class="px-4 py-3.5 align-middle">
                                <p class="font-medium text-navy-900 dark:text-navy-100">{{ $payment->student?->full_name ?? '—' }}</p>
                                @if ($payment->student?->admission_number)
                                    <p class="text-xs text-navy-500 dark:text-navy-400">{{ $payment->student->admission_number }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 align-middle">{{ $payment->feeStructure?->name ?? '—' }}</td>
                            <td class="px-4 py-3.5 align-middle"><x-badge color="navy">{{ $payment->method_label }}</x-badge></td>
                            <td class="px-4 py-3.5 text-right align-middle font-semibold text-navy-900 dark:text-navy-100">TZS {{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-4 py-3.5 align-middle">{{ $payment->payment_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3.5 align-middle">
                                @php
                                    $statusColor = match ($payment->status) {
                                        'recorded' => 'green',
                                        'reversed' => 'red',
                                        'pending' => 'amber',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-badge :color="$statusColor">{{ ucfirst($payment->status) }}</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-navy-500 dark:text-navy-400">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    </x-card>
@endsection
