@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <x-page-header title="Announcements" subtitle="Manage school announcements">
        <x-slot:actions>
            <x-button href="{{ route('admin.announcements.create') }}" variant="gold" icon="plus">New Announcement</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-alert flash />

    <x-card icon="megaphone">
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Title</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Audience</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Author</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Published</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($announcements as $announcement)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                            <td class="px-4 py-3.5">
                                <p class="font-medium text-navy-900 dark:text-navy-100">{{ $announcement->title }}</p>
                                <p class="text-xs text-navy-500 mt-0.5 line-clamp-1">{{ \Illuminate\Support\Str::limit($announcement->body, 80) }}</p>
                            </td>
                            <td class="px-4 py-3.5"><x-badge color="gold">{{ ucfirst($announcement->audience) }}</x-badge></td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $announcement->author?->full_name ?? '—' }}</td>
                            <td class="px-4 py-3.5">
                                @if ($announcement->is_active)
                                    <x-badge color="green">Active</x-badge>
                                @else
                                    <x-badge color="gray">Inactive</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-navy-500">{{ $announcement->published_at?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('admin.announcements.edit', $announcement) }}" variant="ghost" size="sm" icon="edit" />
                                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?')">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-navy-500">No announcements yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $announcements->links() }}</div>
    </x-card>
@endsection
