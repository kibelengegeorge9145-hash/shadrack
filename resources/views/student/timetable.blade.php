@extends('layouts.app')

@section('title', 'My Timetable')

@section('content')
    <x-page-header title="My Timetable" subtitle="{{ $student->schoolClass?->name ?? '' }}" />

    @if ($entries->isEmpty())
        <x-card>
            <x-empty-state icon="calendar" title="No Timetable" message="No timetable entries found for your class." />
        </x-card>
    @else
        <div class="space-y-6">
            @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                @if ($entries->has($day))
                    <x-card title="{{ ucfirst($day) }}" icon="calendar">
                        <div class="overflow-x-auto -mx-5 px-5">
                            <table class="w-full min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-navy-100 dark:border-navy-700">
                                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Time</th>
                                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Subject</th>
                                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Teacher</th>
                                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Room</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                                    @foreach ($entries[$day] as $entry)
                                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                                            <td class="px-4 py-3 text-navy-700 dark:text-navy-300">
                                                {{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}
                                            </td>
                                            <td class="px-4 py-3 font-medium text-navy-900 dark:text-navy-100">{{ $entry->subject?->name ?? '—' }}</td>
                                            <td class="px-4 py-3 text-navy-700 dark:text-navy-300">{{ $entry->teacher?->full_name ?? '—' }}</td>
                                            <td class="px-4 py-3 text-navy-600 dark:text-navy-400">{{ $entry->room ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                @endif
            @endforeach
        </div>
    @endif
@endsection
