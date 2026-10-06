@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <x-page-header title="Settings" subtitle="Manage the active academic year and term used across the system." />

    @if (session('success') || session('error') || session('warning') || session('info'))
        <x-alert flash />
    @endif

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        <x-card title="Active Academic Year & Term" icon="calendar" class="lg:col-span-2">
            <p class="mb-4 text-sm text-navy-500 dark:text-navy-400">
                Select the academic year and term that should be treated as "current" throughout the system.
            </p>
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <x-select name="academic_year_id" label="Current Academic Year" :options="$academicYears->pluck('name', 'id')->all()" :selected="$currentYear?->id" placeholder="Select a year" required />
                <div>
                    <x-label for="term_id">Current Term (for selected year)</x-label>
                    <select name="term_id" id="term_id"
                        class="mt-1.5 w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 py-2.5 px-3 focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400 transition">
                        <option value="">No term selected</option>
                        @foreach ($academicYears as $year)
                            @foreach ($year->terms as $term)
                                <option value="{{ $term->id }}" data-year="{{ $year->id }}" @if ($term->is_current) selected @endif>
                                    {{ $year->name }} — {{ $term->name }}{{ $term->is_current ? ' (current)' : '' }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    @error('term_id')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <x-button type="submit" variant="gold" icon="save">Save Settings</x-button>
                </div>
            </form>
        </x-card>

        <x-card title="Current Selection" icon="check-circle">
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Academic Year</dt>
                    <dd class="mt-1 font-display text-lg font-bold text-navy-900 dark:text-navy-100">{{ $currentYear?->name ?? 'None selected' }}</dd>
                    @if ($currentYear)
                        <dd class="text-xs text-navy-500 dark:text-navy-400">{{ $currentYear->start_date?->format('d M Y') }} — {{ $currentYear->end_date?->format('d M Y') }}</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Term</dt>
                    <dd class="mt-1 font-display text-lg font-bold text-navy-900 dark:text-navy-100">
                        @foreach ($academicYears as $year)
                            @foreach ($year->terms as $term)
                                @if ($term->is_current)
                                    {{ $term->name }}
                                    @break
                                @endif
                            @endforeach
                        @endforeach
                    </dd>
                </div>
            </dl>
        </x-card>
    </div>
@endsection
