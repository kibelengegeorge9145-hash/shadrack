@extends('layouts.app')

@section('title', 'Children Fees')

@section('content')
    <x-page-header title="Children's Fees" subtitle="Fee statements for your children" />

    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <x-stat-card label="Total Required" value="TZS {{ number_format($totalRequired, 0) }}" icon="wallet" color="navy" />
        <x-stat-card label="Total Paid" value="TZS {{ number_format($totalPaid, 0) }}" icon="check-circle" color="green" />
        <x-stat-card label="Total Balance" value="TZS {{ number_format($totalBalance, 0) }}" icon="credit-card" color="{{ $totalBalance > 0 ? 'red' : 'green' }}" />
    </div>

    <div class="space-y-6">
        @foreach ($children as $child)
            @php
                $childFees = $studentFees->where('student_id', $child->id);
                $childPayments = $payments->where('student_id', $child->id);
                $childBalance = (float) $childFees->sum('balance');
            @endphp

            <x-card>
                <x-slot:action>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-navy-800 text-gold-300 text-sm font-bold shrink-0">{{ $child->initials }}</span>
                        <div>
                            <p class="font-display font-bold text-navy-900 dark:text-navy-100">{{ $child->full_name }}</p>
                            <p class="text-xs text-navy-500">{{ $child->schoolClass?->name }} · Balance: <span class="{{ $childBalance > 0 ? 'text-red-600' : 'text-emerald-600' }}">TZS {{ number_format($childBalance, 0) }}</span></p>
                        </div>
                    </div>
                </x-slot:action>

                @if ($childFees->isEmpty())
                    <x-empty-state icon="receipt" title="No Fees" message="No fee records for this student." />
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
                                @foreach ($childFees as $fee)
                                    <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-navy-900 dark:text-navy-100">{{ $fee->feeStructure?->name ?? '—' }}</p>
                                            <p class="text-xs text-navy-500">{{ $fee->academicYear?->name }}</p>
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
        @endforeach
    </div>
@endsection
