@extends('layouts.app')

@section('title', 'Receipt ' . $payment->receipt_number)

@section('content')
    <div class="mb-4 flex justify-end print:hidden">
        <x-button variant="gold" icon="printer" onclick="window.print()">Print Receipt</x-button>
    </div>

    <x-card title="Payment Receipt" icon="receipt">
        <div id="receipt-print" class="bg-white text-navy-900">
            <div class="border-b border-navy-200 pb-6 mb-6 text-center">
                <h1 class="font-display text-2xl font-extrabold text-navy-950">{{ config('app.name') }}</h1>
                <p class="text-sm text-navy-500">Ebenezer Secondary School</p>
                <p class="mt-1 text-xs text-navy-400">Official Payment Receipt</p>
            </div>

            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-navy-500">Receipt Number</p>
                    <p class="mt-1 font-mono text-lg font-bold text-navy-950">{{ $payment->receipt_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-navy-500">Date</p>
                    <p class="mt-1 text-sm">{{ $payment->payment_date?->format('d M Y') }}</p>
                    @if ($payment->created_at)
                        <p class="mt-1 text-xs text-navy-400">Issued {{ $payment->created_at->format('d M Y, H:i') }}</p>
                    @endif
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4 mb-6 p-4 rounded-lg bg-cream-50 dark:bg-navy-800/40">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-navy-500">Student</p>
                    <p class="mt-1 font-medium text-navy-900">{{ $payment->student?->full_name ?? '—' }}</p>
                    @if ($payment->student?->admission_number)
                        <p class="text-xs text-navy-500">Adm. No: {{ $payment->student->admission_number }}</p>
                    @endif
                    @if ($payment->student?->schoolClass)
                        <p class="text-xs text-navy-500">{{ $payment->student->schoolClass->name }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-navy-500">Recorded By</p>
                    <p class="mt-1 font-medium text-navy-900">{{ $payment->recorder?->name ?? '—' }}</p>
                    @if ($payment->method)
                        <p class="text-xs text-navy-500">Method: {{ $payment->method_label }}</p>
                    @endif
                    @if ($payment->reference)
                        <p class="text-xs text-navy-500">Reference: {{ $payment->reference }}</p>
                    @endif
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-navy-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-navy-800 text-white">
                            <th class="text-left px-4 py-2.5 text-xs font-semibold uppercase tracking-wider">Description</th>
                            <th class="text-right px-4 py-2.5 text-xs font-semibold uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-navy-100">
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $payment->feeStructure?->name ?? 'Fee Payment' }}</p>
                                @if ($payment->notes)
                                    <p class="text-xs text-navy-500 mt-0.5">{{ $payment->notes }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format((float) $payment->amount, 2) }}</td>
                        </tr>
                        <tr class="border-t border-navy-100 bg-cream-50 dark:bg-navy-800/40">
                            <td class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Total Paid</td>
                            <td class="px-4 py-3 text-right font-bold text-navy-950">TZS {{ number_format((float) $payment->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($payment->notes)
                <p class="mt-4 text-sm text-navy-500"><span class="font-semibold">Notes:</span> {{ $payment->notes }}</p>
            @endif

            <div class="mt-10 pt-6 border-t border-dashed border-navy-200 flex justify-between">
                <div class="text-center">
                    <div class="h-10"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-navy-500">Student / Guardian Signature</p>
                </div>
                <div class="text-center">
                    <div class="h-10"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-navy-500">Authorized Signature</p>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-navy-400">Thank you for your payment. This receipt is computer generated and valid without a physical signature.</p>
        </div>
    </x-card>

    @push('scripts')
        <script>
            window.addEventListener('beforeprint', () => {
                // Keep print styles minimal; the layout is print-friendly already.
            });
        </script>
    @endpush
@endsection
