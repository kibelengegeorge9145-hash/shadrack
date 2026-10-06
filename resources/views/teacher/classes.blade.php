@extends('layouts.app')

@section('title', 'My Classes')

@section('content')
    <x-page-header title="My Classes" subtitle="Classes you are assigned to teach." />

    <x-alert type="success" flash class="mb-6" />

    <x-card padding="false">
        <div class="px-5 pt-5 pb-3">
            <h3 class="font-display text-lg font-bold text-navy-900 dark:text-navy-50">Assigned Classes</h3>
            <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">{{ $classes->count() }} class(es)</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700 text-left">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Class</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Level</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Students</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($classes as $class)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-navy-800/5 dark:bg-gold-400/10 text-navy-800 dark:text-gold-300 shrink-0">
                                        <x-icon name="building" class="w-4.5 h-4.5" />
                                    </span>
                                    <div>
                                        <p class="font-semibold text-navy-900 dark:text-navy-100">{{ $class->name }}</p>
                                        @if ($class->classTeacher)
                                            <p class="text-xs text-navy-400">CT: {{ $class->classTeacher->full_name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5"><x-badge color="navy">{{ $class->level }}</x-badge></td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-navy-700 dark:text-navy-200">
                                    <x-icon name="users" class="w-4 h-4 text-navy-400" />
                                    {{ $class->students_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                    <x-button href="{{ route('teacher.results.index', ['school_class_id' => $class->id]) }}" size="sm" variant="secondary" icon="edit">Enter Results</x-button>
                                    <x-button href="{{ route('teacher.attendance.index', ['school_class_id' => $class->id]) }}" size="sm" variant="secondary" icon="calendar-check">Take Attendance</x-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="building" title="No classes assigned" message="Talk to an administrator to get assigned to a class." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection