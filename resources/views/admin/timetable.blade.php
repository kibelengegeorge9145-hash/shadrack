@extends('layouts.app')

@section('title', 'Timetable')

@section('content')
    <x-page-header title="Timetable" subtitle="Manage the weekly subject timetable for each class." />

    <x-alert type="success" flash class="mb-6" />

    <x-card class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <form method="GET" action="{{ route('admin.timetable.index') }}" class="flex-1 grid md:grid-cols-[1fr_auto] gap-4 items-end">
                <x-select name="school_class_id" label="Class" :options="$classes->pluck('name', 'id')" placeholder="Select a class" :selected="$selectedClass?->id ?? request('school_class_id')" required />
                <div class="flex items-center gap-2">
                    <x-button type="submit" icon="calendar">View Timetable</x-button>
                    @if ($selectedClass)
                        <x-button href="{{ route('admin.timetable.index') }}" variant="secondary" icon="x">Reset</x-button>
                    @endif
                </div>
            </form>
            @if ($selectedClass)
                <x-button type="button" icon="plus" variant="gold" data-modal-open="modal">Add Entry</x-button>
            @endif
        </div>
    </x-card>

    @if (!$selectedClass)
        <x-card>
            <x-empty-state
                icon="calendar"
                title="No class selected"
                message="Select a class above to view or build its weekly timetable."
            />
        </x-card>
    @else
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($days as $day)
                <x-card :title="ucfirst($day)" :icon="'clock'" class="h-fit">
                    <div class="space-y-1" data-day="{{ $day }}">
                        @forelse (($entries[$day] ?? collect())->sortBy('start_time') as $entry)
                            <div class="flex items-center justify-between gap-3 rounded-lg border border-navy-100 dark:border-navy-700/60 px-3 py-2.5">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-navy-900 dark:text-navy-100">{{ substr($entry->start_time, 0, 5) }} – {{ substr($entry->end_time, 0, 5) }}</p>
                                    <p class="text-xs text-navy-500 dark:text-navy-300 truncate">{{ $entry->subject?->name ?? 'Free period' }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xs font-medium text-navy-700 dark:text-navy-200">{{ $entry->teacher?->full_name ?? '—' }}</p>
                                    @if ($entry->room)
                                        <p class="text-[11px] text-navy-400">{{ $entry->room }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-navy-400 dark:text-navy-500 py-2 text-center">No periods scheduled.</p>
                        @endforelse
                    </div>
                </x-card>
            @endforeach
        </div>

        <x-modal title="Add Timetable Entry">
            <form method="POST" action="{{ route('admin.timetable.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="school_class_id" value="{{ $selectedClass->id }}">
                <x-select name="day" label="Day" :options="collect($days)->mapWithKeys(fn ($d) => [$d => ucfirst($d)])" placeholder="Choose day" required />
                <div class="grid grid-cols-2 gap-4">
                    <x-input type="time" name="start_time" label="Start Time" required />
                    <x-input type="time" name="end_time" label="End Time" required />
                </div>
                <x-select name="subject_id" label="Subject" :options="$subjects" placeholder="Free period / no subject" :selected="null" />
                <x-select name="teacher_id" label="Teacher" :options="$teachers" placeholder="Unassigned" :selected="null" />
                <x-input name="room" label="Room" placeholder="e.g. Room 12 / Lab A" />
                <div class="flex justify-end gap-2">
                    <x-button type="button" variant="secondary" data-modal-close>Cancel</x-button>
                    <x-button type="submit" icon="save">Add Entry</x-button>
                </div>
            </form>
        </x-modal>
    @endif
@endsection