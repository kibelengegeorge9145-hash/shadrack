@extends('layouts.app')

@section('title', 'Events')

@section('content')
    <x-page-header title="Events" subtitle="Manage school events">
        <x-slot:actions>
            <x-button href="{{ route('admin.events.create') }}" variant="gold" icon="plus">New Event</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-alert flash />

    <x-card icon="calendar-check">
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Title</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Location</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Starts</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Ends</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Status</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($events as $event)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                            <td class="px-4 py-3.5 font-medium text-navy-900 dark:text-navy-100">{{ $event->title }}</td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $event->location ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $event->starts_at?->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $event->ends_at?->format('d M Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3.5">
                                @if ($event->is_published)
                                    <x-badge color="green">Published</x-badge>
                                @else
                                    <x-badge color="gray">Draft</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @php $rsvpCount = $event->rsvps()->count(); @endphp
                                    <x-button href="{{ route('admin.events.rsvps', $event) }}" variant="ghost" size="sm" icon="users" title="View {{ $rsvpCount }} RSVP{{ $rsvpCount !== 1 ? 's' : '' }}" />
                                    <x-button href="{{ route('admin.events.edit', $event) }}" variant="ghost" size="sm" icon="edit" />
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?')">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-navy-500">No events yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $events->links() }}</div>
    </x-card>
@endsection
