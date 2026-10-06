@extends('layouts.app')

@section('title', 'Parents')

@section('content')
    <x-page-header title="Parents" subtitle="Manage parents and guardians">
        <x-slot:actions>
            <x-button href="{{ route('admin.parents.create') }}" icon="user-plus" variant="gold">New Parent</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false" title="Parents & Guardians" icon="users">
        <form method="GET" action="{{ route('admin.parents.index') }}" class="flex flex-col md:flex-row gap-3 p-5 pb-4">
            <x-input name="search" label="Search" leadingIcon="search" value="{{ request('search') }}" placeholder="Name, email or phone..." class="md:w-96" />
            <div class="md:pt-7 flex items-center">
                <x-button type="submit" variant="secondary" icon="search">Filter</x-button>
                @if (request('search'))
                    <a href="{{ route('admin.parents.index') }}" class="ml-3 text-sm text-navy-500 hover:text-navy-900 dark:hover:text-navy-100 underline">Clear</a>
                @endif
            </div>
        </form>

        @if ($parents->count())
            <div class="overflow-x-auto -mx-5 px-5">
                <table class="w-full min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-navy-100 dark:border-navy-700">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Parent</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Relationship</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Phone</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Email</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Children</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                        @foreach ($parents as $parent)
                            <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-navy-800 text-gold-300 text-xs font-bold shrink-0">{{ $parent->initials }}</span>
                                        <p class="font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $parent->full_name }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-badge color="gold">{{ ucfirst($parent->relationship) }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200 whitespace-nowrap">{{ $parent->phone ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-navy-700 dark:text-navy-200">{{ $parent->email ?? '—' }}</td>
                                <td class="px-4 py-3.5">
                                    <x-badge color="{{ $parent->students_count > 0 ? 'blue' : 'gray' }}">{{ $parent->students_count }}</x-badge>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.parents.edit', $parent) }}" class="p-2 rounded-lg text-navy-500 hover:text-navy-900 hover:bg-navy-50 dark:hover:text-white dark:hover:bg-navy-800" title="Edit">
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.parents.destroy', $parent) }}" class="inline" data-confirm="Are you sure you want to remove this parent?">
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
                {{ $parents->links() }}
            </div>
        @else
            <div class="p-5">
                <x-empty-state icon="users" title="No parents found" message="Try adjusting your filters or add a new parent.">
                    <x-slot:action>
                        <x-button href="{{ route('admin.parents.create') }}" icon="user-plus" variant="gold">New Parent</x-button>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @endif
    </x-card>
@endsection