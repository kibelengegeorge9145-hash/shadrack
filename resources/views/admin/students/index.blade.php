@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <x-page-header title="Students" subtitle="Manage student records and admissions">
        <x-slot:actions>
            <x-button href="{{ route('admin.students.create') }}" icon="user-plus" variant="gold">New Student</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false" title="Student Directory" icon="users">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col md:flex-row gap-3 p-5 pb-4 flex-wrap">
            <x-input name="search" label="Search" leadingIcon="search" value="{{ request('search') }}" placeholder="Name or admission no..." class="md:w-72" />
            <div class="md:w-56">
                <x-select name="level" label="Level" :options="$levelOptions" selected="{{ request('level') }}" placeholder="All levels" />
            </div>
            <div class="md:w-56">
                <x-select name="class" label="Class" :options="$classes" selected="{{ request('class') }}" placeholder="All classes" />
            </div>
            <div class="md:pt-7 flex items-center">
                <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
                @if (request('search') || request('level') || request('class'))
                    <a href="{{ route('admin.students.index') }}" class="ml-3 text-sm text-navy-500 hover:text-navy-900 dark:hover:text-navy-100 underline">Clear</a>
                @endif
            </div>
        </form>

        @if ($students->count())
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-navy-100 dark:border-navy-700">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Admission No</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Student</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Class</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Level</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Parent</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @foreach ($students as $student)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                <td class="px-4 py-3.5 font-medium text-navy-900 dark:text-navy-50 whitespace-nowrap">{{ $student->admission_number }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-navy-800 text-gold-300 text-xs font-bold shrink-0">{{ $student->initials }}</span>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $student->full_name }}</p>
                                            <p class="text-xs text-navy-500 dark:text-navy-400">{{ $student->gender ? ucfirst($student->gender) : '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">{{ $student->schoolClass?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">{{ $student->level }}</td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">{{ $student->parent?->full_name ?? '—' }}</td>
                                <td class="px-4 py-3.5">
                                    @php
                                        $statusColor = match ($student->status) {
                                            'active' => 'green',
                                            'inactive' => 'gray',
                                            'graduated' => 'blue',
                                            'transferred' => 'amber',
                                            default => 'navy',
                                        };
                                    @endphp
                                    <x-badge color="{{ $statusColor }}">{{ ucfirst($student->status) }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.students.show', $student) }}" class="p-2 rounded-lg text-navy-500 hover:text-navy-900 hover:bg-navy-50 dark:hover:text-white dark:hover:bg-navy-800" title="View">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student) }}" class="p-2 rounded-lg text-navy-500 hover:text-navy-900 hover:bg-navy-50 dark:hover:text-white dark:hover:bg-navy-800" title="Edit">
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="inline" data-confirm="Are you sure you want to remove this student?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-navy-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40" title="Delete">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-navy-100 dark:border-navy-700/60">
                {{ $students->links() }}
            </div>
        @else
            <div class="p-5">
                <x-empty-state icon="users" title="No students found"
                    message="Try adjusting your filters or register a new student.">
                    <x-slot:action>
                        <x-button href="{{ route('admin.students.create') }}" icon="user-plus" variant="gold">New Student</x-button>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @endif
    </x-card>
@endsection