@extends('layouts.app')

@section('title', 'Payments')

@section('content')
    <x-page-header title="Payments" subtitle="Record new payments and browse the payment history." />

    @if (session('success') || session('error') || session('warning') || session('info'))
        <x-alert flash />
    @endif

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        <x-card title="Record Payment" icon="coins" class="lg:col-span-1">
            @if ($term)
                <p class="mb-4 text-sm text-navy-500 dark:text-navy-400">Recording for {{ $year->name }} · {{ $term->name }}.</p>
            @else
                <p class="mb-4 text-sm text-amber-600 dark:text-amber-400">No active academic year/term set. Payments will still be recorded without a term scope.</p>
            @endif
            <form method="POST" action="{{ route('accountant.payments.store') }}" class="space-y-4">
                @csrf
                <x-select name="student_id" label="Student" :options="$students" :selected="$currentStudent" placeholder="Select a student" required />
                <x-select name="fee_structure_id" label="Fee Structure" :options="$structures" placeholder="Select a fee structure" required />
                <x-input type="number" step="0.01" min="0.01" name="amount" label="Amount (TZS)" leadingIcon="dollar" placeholder="0.00" required />
                <x-select name="method" label="Payment Method" :options="$methods" placeholder="Select a method" required />
                <x-input type="date" name="payment_date" label="Payment Date" value="{{ now()->toDateString() }}" required />
                <x-input name="reference" label="Reference (Optional)" placeholder="e.g. cheque no / transaction id" />
                <x-textarea name="notes" label="Notes (Optional)" placeholder="Any remarks" />
                <div class="flex justify-end pt-1">
                    <x-button type="submit" variant="gold" icon="check-circle">Record Payment</x-button>
                </div>
            </form>
        </x-card>

        <x-card title="Payment History" icon="banknote" class="lg:col-span-2">
            <form method="GET" action="{{ route('accountant.payments.index') }}" class="mb-4 flex flex-col gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-navy-400">
                        <x-icon name="search" class="w-4 h-4" />
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by student or receipt number..."
                        class="w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 pl-10 py-2.5 focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400" />
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <select name="method" class="flex-1 rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 px-3 py-2.5">
                        <option value="">All methods</option>
                        @foreach ($methods as $value => $label)
                            <option value="{{ $value }}" @if (request('method') === $value) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="flex-1 rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 px-3 py-2.5">
                        <option value="">All statuses</option>
                        @foreach (['recorded' => 'Recorded', 'reversed' => 'Reversed', 'pending' => 'Pending'] as $value => $label)
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
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Receipt</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Student</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Fee</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Method</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Amount</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                <td class="px-4 py-3.5 align-middle font-mono text-xs text-navy-700 dark:text-navy-200">{{ $payment->receipt_number }}</td>
                                <td class="px-4 py-3.5 align-middle font-medium text-navy-900 dark:text-navy-100">{{ $payment->student?->full_name ?? '—' }}</td>
                                <td class="px-4 py-3.5 align-middle">{{ $payment->feeStructure?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 align-middle"><x-badge color="navy">{{ $payment->method_label }}</x-badge></td>
                                <td class="px-4 py-3.5 text-right align-middle font-semibold">TZS {{ number_format((float) $payment->amount, 2) }}</td>
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
                                <td colspan="6" class="px-4 py-10 text-center text-navy-500 dark:text-navy-400">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </x-card>
    </div>
@endsection
