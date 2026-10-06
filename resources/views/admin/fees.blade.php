@extends('layouts.app')

@section('title', 'Fee Structures')

@section('content')
    <x-page-header title="Fee Structures" subtitle="Define tuition and other fee structures. Structures can optionally re-sync student ledgers." />

    @if (session('success') || session('error') || session('warning') || session('info'))
        <x-alert flash />
    @endif

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        <x-card title="Create Fee Structure" icon="credit-card">
            <form method="POST" action="{{ route('admin.fees.store') }}" class="space-y-4">
                @csrf
                <x-input name="name" label="Structure Name" placeholder="e.g. Tuition Fee" required leadingIcon="file-text" />
                <x-select name="category" label="Category" :options="array_combine($categories, $categories)" placeholder="Select a category" required />
                <x-input type="number" step="0.01" min="0" name="amount" label="Amount (TZS)" leadingIcon="dollar" placeholder="0.00" required />
                <x-select name="level" label="Applies To Level" :options="$levels" placeholder="All levels (None)" />
                <x-select name="school_class_id" label="Specific Class (Optional)" :options="$classes" placeholder="All classes (None)" />
                <x-select name="academic_year_id" label="Academic Year (Optional)" :options="$academicYears" placeholder="All years (None)" />
                <x-textarea name="description" label="Description" placeholder="Brief description of this fee structure." />
                <label class="flex items-center gap-3 text-sm text-navy-700 dark:text-navy-200">
                    <input type="checkbox" name="sync_students" value="1" class="rounded border-navy-300 text-navy-800 focus:ring-gold-400" />
                    Sync student fees for matching students now
                </label>
                <div class="flex justify-end pt-1">
                    <x-button type="submit" variant="gold" icon="plus">Create Structure</x-button>
                </div>
            </form>
        </x-card>

        <x-card title="Existing Structures" icon="list" class="lg:col-span-2">
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-navy-100 dark:border-navy-700">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Name</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Category</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Amount</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Scope</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @forelse ($feeStructures as $structure)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                <td class="px-4 py-3.5 align-middle">
                                    <p class="font-medium text-navy-900 dark:text-navy-100">{{ $structure->name }}</p>
                                    @if ($structure->description)
                                        <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">{{ $structure->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    <x-badge color="gold">{{ $structure->category }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5 text-right align-middle font-semibold text-navy-900 dark:text-navy-100">TZS {{ number_format((float) $structure->amount, 2) }}</td>
                                <td class="px-4 py-3.5 align-middle">
                                    <div class="text-xs text-navy-600 dark:text-navy-300">
                                        @if ($structure->level)
                                            <p>{{ $structure->level }}</p>
                                        @endif
                                        @if ($structure->schoolClass)
                                            <p>{{ $structure->schoolClass->name }}</p>
                                        @endif
                                        @if ($structure->academicYear)
                                            <p>{{ $structure->academicYear->name }}</p>
                                        @endif
                                        @if (!$structure->level && !$structure->schoolClass && !$structure->academicYear)
                                            <p class="text-navy-400">All students / years</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 align-middle">
                                    @if ($structure->is_active)
                                        <x-badge color="green">Active</x-badge>
                                    @else
                                        <x-badge color="gray">Inactive</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-navy-500 dark:text-navy-400">No fee structures yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
@endsection
