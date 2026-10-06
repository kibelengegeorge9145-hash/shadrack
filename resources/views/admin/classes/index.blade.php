@extends('layouts.app')

@section('title', 'Classes')

@section('content')
    <x-page-header title="Classes" subtitle="Manage classes and form teachers">
        <x-slot:actions>
            <x-button href="{{ route('admin.classes.create') }}" icon="plus" variant="gold">New Class</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false" title="School Classes" icon="building">
        <form method="GET" action="{{ route('admin.classes.index') }}" class="flex flex-col md:flex-row gap-3 p-5 pb-4">
            <x-input name="search" label="Search" leadingIcon="search" value="{{ request('search') }}" placeholder="Name, label or academic year..." class="md:w-72" />
            <div class="md:w-56">
                <x-select name="level" label="Level" :options="$levelOptions" selected="{{ request('level') }}" placeholder="All levels" />
            </div>
            <div class="md:pt-7 flex items-center">
                <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
                @if (request('search') || request('level'))
                    <a href="{{ route('admin.classes.index') }}" class="ml-3 text-sm text-navy-500 hover:text-navy-900 dark:hover:text-navy-100 underline">Clear</a>
                @endif
            </div>
        </form>

        @if ($classes->count())
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-navy-100 dark:border-navy-700">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Class</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Level</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Form</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Academic Year</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Class Teacher</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Students</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @foreach ($classes as $class)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-navy-800/5 dark:bg-gold-400/10 text-navy-800 dark:text-gold-300 text-xs font-bold shrink-0">
                                            <x-icon name="building" class="w-4 h-4" />
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $class->name }}</p>
                                            <p class="text-xs text-navy-500 dark:text-navy-400">{{ $class->label ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-badge color="{{ $class->level === 'A-Level' ? 'navy' : 'gold' }}">{{ $class->level }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">Form {{ $class->form_number ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200 whitespace-nowrap">{{ $class->academic_year }}</td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">{{ $class->classTeacher?->full_name ?? '—' }}</td>
                                <td class="px-4 py-3.5">
                                    <x-badge color="{{ $class->students_count > 0 ? 'blue' : 'gray' }}">{{ $class->students_count }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.classes.edit', $class) }}" class="p-2 rounded-lg text-navy-500 hover:text-navy-900 hover:bg-navy-50 dark:hover:text-white dark:hover:bg-navy-800" title="Edit">
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" class="inline" data-confirm="Are you sure you want to remove this class?">
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
                {{ $classes->links() }}
            </div>
        @else
            <div class="p-5">
                <x-empty-state icon="building" title="No classes found" message="Try adjusting your filters or create a new class.">
                    <x-slot:action>
                        <x-button href="{{ route('admin.classes.create') }}" icon="plus" variant="gold">New Class</x-button>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @endif
    </x-card>
@endsection