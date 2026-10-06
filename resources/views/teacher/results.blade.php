@extends('layouts.app')

@section('title', 'Enter Results')

@section('content')
    <x-page-header title="Enter Results" subtitle="Record marks for the subjects you teach." />

    <x-alert type="success" flash class="mb-6" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route('teacher.results.index') }}" class="grid md:grid-cols-4 gap-4 items-end">
            <x-select name="school_class_id" label="Class" :options="$classes->pluck('name', 'id')" placeholder="Select class" :selected="request('school_class_id')" required />
            <x-select name="subject_id" label="Subject" :options="$subjects->pluck('name', 'id')" placeholder="Select subject" :selected="request('subject_id')" />
            <x-select name="term_id" label="Term" :options="$termOptions" placeholder="Select term" :selected="request('term_id')" />
            <div class="flex items-center gap-2">
                <x-button type="submit" icon="filter">Load</x-button>
                @if (request('school_class_id') || request('subject_id') || request('term_id'))
                    <x-button href="{{ route('teacher.results.index') }}" variant="secondary" icon="x">Reset</x-button>
                @endif
            </div>
        </form>
    </x-card>

    @if (!$selectedClass)
        <x-card>
            <x-empty-state icon="file-text" title="No class selected" message="Choose a class, subject and term to open the marking sheet." />
        </x-card>
    @elseif ($subjects->isEmpty())
        <x-card>
            <x-empty-state icon="book-open" title="No subjects in this class" message="You are not assigned to teach any subject in {{ $selectedClass->name }}." />
        </x-card>
    @elseif (!$selectedSubject || !$selectedTerm)
        <x-card>
            <x-empty-state icon="filter" title="Select subject and term" message="Pick a subject and term to load the student roster for {{ $selectedClass->name }}." />
        </x-card>
    @else
        <x-card>
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-display text-lg font-bold text-navy-900 dark:text-navy-50">Marking Sheet</h3>
                    <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">
                        {{ $selectedClass->name }} · {{ $selectedSubject->name }} · {{ $selectedTerm->name }} · {{ $students->count() }} student(s)
                    </p>
                </div>
                <x-badge color="gold">{{ $selectedClass->level }}</x-badge>
            </div>

            @if ($students->isEmpty())
                <x-empty-state icon="users" title="No active students" message="There are no active students enrolled in this class." />
            @else
                <form method="POST" action="{{ route('teacher.results.store') }}">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ $selectedClass->id }}">
                    <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                    <input type="hidden" name="term_id" value="{{ $selectedTerm->id }}">

                    <div class="overflow-x-auto -mx-5 px-5">
                        <table class="w-full min-w-[520px] text-sm">
                            <thead>
                                <tr class="border-b border-navy-100 dark:border-navy-700 text-left">
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">#</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Student</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Adm. No</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Marks (0–100)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                                @foreach ($students as $student)
                                    <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                        <td class="px-4 py-2.5 text-navy-400">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2.5 font-medium text-navy-900 dark:text-navy-100">{{ $student->full_name }}</td>
                                        <td class="px-4 py-2.5 text-navy-500 dark:text-navy-300">{{ $student->admission_number }}</td>
                                        <td class="px-4 py-2.5">
                                            <input type="number" name="marks[{{ $student->id }}]"
                                                value="{{ old('marks.' . $student->id, $marks[$student->id] ?? '') }}"
                                                min="0" max="100" step="1" inputmode="numeric"
                                                placeholder="—"
                                                class="w-24 rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 px-2.5 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @error('marks')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('marks.*')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-5 flex items-center justify-between gap-3 flex-wrap">
                        <p class="text-xs text-navy-500 dark:text-navy-400">
                            Grades and points are derived automatically from the configured grading rules. Positions are recalculated across the whole {{ $selectedClass->name }} after saving.
                        </p>
                        <x-button type="submit" icon="save">Save Marks</x-button>
                    </div>
                </form>
            @endif
        </x-card>
    @endif
@endsection