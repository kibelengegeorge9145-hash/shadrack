@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
    <x-page-header title="Attendance" subtitle="Record daily attendance for your classes." />

    <x-alert type="success" flash class="mb-6" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route('teacher.attendance.index') }}" class="grid md:grid-cols-3 gap-4 items-end">
            <x-select name="school_class_id" label="Class" :options="$classes->pluck('name', 'id')" placeholder="Select class" :selected="request('school_class_id')" required />
            <x-input type="date" name="date" label="Date" value="{{ $date }}" required />
            <div class="flex items-center gap-2">
                <x-button type="submit" icon="calendar-check">Load Roster</x-button>
                @if (request('school_class_id'))
                    <x-button href="{{ route('teacher.attendance.index') }}" variant="secondary" icon="x">Reset</x-button>
                @endif
            </div>
        </form>
    </x-card>

    @if (!$selectedClass)
        <x-card>
            <x-empty-state icon="calendar-check" title="No class selected" message="Pick a class and date above to load the attendance roster." />
        </x-card>
    @else
        <x-card>
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-display text-lg font-bold text-navy-900 dark:text-navy-50">{{ $selectedClass->name }}</h3>
                    <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">{{ $students->count() }} student(s) · {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</p>
                </div>
                <x-badge color="navy">{{ $selectedClass->level }}</x-badge>
            </div>

            @if ($students->isEmpty())
                <x-empty-state icon="users" title="No active students" message="There are no active students enrolled in this class." />
            @else
                <form method="POST" action="{{ route('teacher.attendance.store') }}">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ $selectedClass->id }}">
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div class="overflow-x-auto -mx-5 px-5">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-navy-100 dark:border-navy-700 text-left">
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">#</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Student</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Adm. No</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                                @foreach ($students as $student)
                                    <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                        <td class="px-4 py-2.5 text-navy-400">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2.5 font-medium text-navy-900 dark:text-navy-100">{{ $student->full_name }}</td>
                                        <td class="px-4 py-2.5 text-navy-500 dark:text-navy-300">{{ $student->admission_number }}</td>
                                        <td class="px-4 py-2.5">
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach (\App\Models\Attendance::STATUSES as $status)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="status[{{ $student->id }}]" value="{{ $status }}" class="peer sr-only"
                                                            @checked(($existing[$student->id]->status ?? 'present') === $status)>
                                                        <span class="inline-flex items-center rounded-full border border-navy-200 dark:border-navy-700 px-3 py-1 text-xs font-semibold text-navy-600 dark:text-navy-300 transition-colors peer-checked:bg-navy-800 peer-checked:border-navy-800 peer-checked:text-white dark:peer-checked:bg-gold-400 dark:peer-checked:border-gold-400 dark:peer-checked:text-navy-950">
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @error('status')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end mt-5">
                        <x-button type="submit" icon="save">Save Attendance</x-button>
                    </div>
                </form>
            @endif
        </x-card>
    @endif
@endsection