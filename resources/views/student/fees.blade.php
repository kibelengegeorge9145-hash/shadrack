@extends('layouts.app')

@section('title', 'Fee Statement')

@section('content')
    <x-page-header title="Fee Statement" subtitle="View your fees and payment history" />

    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <x-stat-card label="Total Required" value="TZS {{ number_format($totalRequired, 0) }}" icon="wallet" color="navy" />
        <x-stat-card label="Total Paid" value="TZS {{ number_format($totalPaid, 0) }}" icon="check-circle" color="green" />
        <x-stat-card label="Balance" value="TZS {{ number_format($totalBalance, 0) }}" icon="credit-card" color="{{ $totalBalance > 0 ? 'red' : 'green' }}" />
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <x-card title="Fee Ledger" icon="receipt">
            @if ($studentFees->isEmpty())
                <x-empty-state icon="receipt" title="No Fees" message="No fee records found." />
            @else
                <div class="overflow-x-auto -mx-5 px-5">
                    <table class="w-full min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-navy-100 dark:border-navy-700">
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Fee</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Required</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Paid</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                            @foreach ($studentFees as $fee)
                                <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-navy-900 dark:text-navy-100">{{ $fee->feeStructure?->name ?? '—' }}</p>
                                        <p class="text-xs text-navy-500">{{ $fee->academicYear?->name }} {{ $fee->term?->name ? '· ' . $fee->term->name : '' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-right text-navy-700 dark:text-navy-300">TZS {{ number_format((float) $fee->required_amount, 0) }}</td>
                                    <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400">TZS {{ number_format((float) $fee->paid_amount, 0) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold {{ (float) $fee->balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">TZS {{ number_format((float) $fee->balance, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>

        <x-card title="Payment History" icon="banknote">
            @if ($payments->isEmpty())
                <x-empty-state icon="banknote" title="No Payments" message="No payment records found." />
            @else
                <div class="overflow-x-auto -mx-5 px-5">
                    <table class="w-full min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-navy-100 dark:border-navy-700">
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Receipt</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Fee</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Amount</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                                    <td class="px-4 py-3 text-navy-700 dark:text-navy-300 font-mono text-xs">{{ $payment->receipt_number }}</td>
                                    <td class="px-4 py-3 text-navy-700 dark:text-navy-300">{{ $payment->feeStructure?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">TZS {{ number_format((float) $payment->amount, 0) }}</td>
                                    <td class="px-4 py-3 text-navy-500">{{ $payment->payment_date?->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
@endsection
