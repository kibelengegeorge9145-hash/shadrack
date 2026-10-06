@extends('layouts.app')

@section('title', 'Subjects')

@section('content')
    <x-page-header title="Subjects" subtitle="Manage subjects offered at the school">
        <x-slot:actions>
            <x-button href="{{ route('admin.subjects.create') }}" icon="plus" variant="gold">New Subject</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false" title="Subject Catalog" icon="book-open">
        <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex flex-col md:flex-row gap-3 p-5 pb-4">
            <x-input name="search" label="Search" leadingIcon="search" value="{{ request('search') }}" placeholder="Code, name or department..." class="md:w-72" />
            <div class="md:w-56">
                <x-select name="level" label="Level" :options="$levelOptions" selected="{{ request('level') }}" placeholder="All levels" />
            </div>
            <div class="md:pt-7 flex items-center">
                <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
                @if (request('search') || request('level'))
                    <a href="{{ route('admin.subjects.index') }}" class="ml-3 text-sm text-navy-500 hover:text-navy-900 dark:hover:text-navy-100 underline">Clear</a>
                @endif
            </div>
        </form>

        @if ($subjects->count())
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-navy-100 dark:border-navy-700">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Code</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Subject</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Level</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Department</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Type</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @foreach ($subjects as $subject)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-navy-800/5 dark:bg-gold-400/10 text-xs font-bold text-navy-800 dark:text-gold-300">{{ $subject->code }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-navy-800/5 dark:bg-gold-400/10 text-navy-800 dark:text-gold-300 shrink-0">
                                            <x-icon name="book-open" class="w-4 h-4" />
                                        </span>
                                        <p class="font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $subject->name }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-badge color="{{ $subject->level === 'A-Level' ? 'navy' : 'gold' }}">{{ $subject->level }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">{{ $subject->department ?? '—' }}</td>
                                <td class="px-4 py-3.5">
                                    <x-badge color="{{ $subject->is_optional ? 'amber' : 'green' }}">{{ $subject->is_optional ? 'Optional' : 'Core' }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-badge color="{{ $subject->is_active ? 'green' : 'gray' }}">{{ $subject->is_active ? 'Active' : 'Inactive' }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.subjects.edit', $subject) }}" class="p-2 rounded-lg text-navy-500 hover:text-navy-900 hover:bg-navy-50 dark:hover:text-white dark:hover:bg-navy-800" title="Edit">
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" class="inline" data-confirm="Are you sure you want to remove this subject?">
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
                {{ $subjects->links() }}
            </div>
        @else
            <div class="p-5">
                <x-empty-state icon="book-open" title="No subjects found" message="Try adjusting your filters or create a new subject.">
                    <x-slot:action>
                        <x-button href="{{ route('admin.subjects.create') }}" icon="plus" variant="gold">New Subject</x-button>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @endif
    </x-card>
@endsection