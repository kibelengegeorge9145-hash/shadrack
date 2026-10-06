@extends('layouts.app')

@section('title', 'Grading & Division Rules')

@php
    $levels = ['O-Level', 'A-Level'];
    $gradeSets = ['O-Level' => $olevelGrades, 'A-Level' => $alevelGrades];
    $divisionSets = ['O-Level' => $olevelDivisions, 'A-Level' => $alevelDivisions];
@endphp

@section('content')
    <x-page-header
        title="Grading & Division Rules"
        subtitle="Configure grade boundaries, points and division classifications for O-Level and A-Level."
    />

    <x-alert type="success" flash class="mb-6" />

    <div class="grid xl:grid-cols-2 gap-6">
        @foreach ($levels as $level)
            <x-card title="{{ $level }}" :icon="$level === 'O-Level' ? 'target' : 'award'">
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h3 class="font-display text-lg font-bold text-navy-900 dark:text-navy-50">Grading Boundaries</h3>
                                <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">Marks are matched against these ranges to derive grades and points.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.grading.store') }}">
                            @csrf
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-navy-100 dark:border-navy-700 text-left">
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Min</th>
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Max</th>
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Grade</th>
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Points</th>
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Pass</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                                        @foreach ($gradeSets[$level] as $rule)
                                            <tr>
                                                @php $inputClass = 'w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 px-2.5 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400'; @endphp
                                                <td class="px-2 py-1.5">
                                                    <input type="hidden" name="rules[{{ $rule->id }}][id]" value="{{ $rule->id }}">
                                                    <input type="number" name="rules[{{ $rule->id }}][min_mark]" value="{{ $rule->min_mark }}" min="0" max="100" class="{{ $inputClass }}">
                                                    @error('rules.' . $rule->id . '.min_mark') <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="number" name="rules[{{ $rule->id }}][max_mark]" value="{{ $rule->max_mark }}" min="0" max="100" class="{{ $inputClass }}">
                                                    @error('rules.' . $rule->id . '.max_mark') <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="text" name="rules[{{ $rule->id }}][grade]" value="{{ $rule->grade }}" maxlength="5" class="{{ $inputClass }}">
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="number" name="rules[{{ $rule->id }}][points]" value="{{ $rule->points }}" min="0" class="{{ $inputClass }}">
                                                </td>
                                                <td class="px-2 py-1.5 text-center">
                                                    <label class="inline-flex items-center justify-center cursor-pointer">
                                                        <input type="checkbox" name="rules[{{ $rule->id }}][is_pass]" value="1" @checked($rule->is_pass) class="h-4 w-4 rounded border-navy-300 text-gold-500 focus:ring-gold-400/40">
                                                    </label>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-end mt-4">
                                <x-button type="submit" icon="save">Save {{ $level }} Grades</x-button>
                            </div>
                        </form>
                    </div>

                    <hr class="border-navy-100 dark:border-navy-700/60">

                    <div>
                        <h3 class="font-display text-lg font-bold text-navy-900 dark:text-navy-50 mb-1">{{ $level }} Division Rules</h3>
                        <p class="text-xs text-navy-500 dark:text-navy-400 mb-3">Divisions are derived from the total points accumulated across all subjects.</p>

                        <form method="POST" action="{{ route('admin.grading.store') }}">
                            @csrf
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-navy-100 dark:border-navy-700 text-left">
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Division</th>
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Min Pts</th>
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Max Pts</th>
                                            <th class="px-2 py-2 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Order</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                                        @foreach ($divisionSets[$level] as $division)
                                            <tr>
                                                @php $inputClass = 'w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 px-2.5 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400'; @endphp
                                                <td class="px-2 py-1.5">
                                                    <input type="hidden" name="divisions[{{ $division->id }}][id]" value="{{ $division->id }}">
                                                    <input type="text" name="divisions[{{ $division->id }}][division]" value="{{ $division->division }}" maxlength="20" class="{{ $inputClass }}">
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="number" name="divisions[{{ $division->id }}][min_points]" value="{{ $division->min_points }}" min="0" class="{{ $inputClass }}">
                                                    @error('divisions.' . $division->id . '.min_points') <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="number" name="divisions[{{ $division->id }}][max_points]" value="{{ $division->max_points }}" min="0" class="{{ $inputClass }}">
                                                    @error('divisions.' . $division->id . '.max_points') <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="number" name="divisions[{{ $division->id }}][sort_order]" value="{{ $division->sort_order }}" min="0" class="{{ $inputClass }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-end mt-4">
                                <x-button type="submit" icon="save" variant="gold">Save {{ $level }} Divisions</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
@endsection