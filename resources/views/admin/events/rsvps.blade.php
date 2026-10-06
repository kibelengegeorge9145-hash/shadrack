@extends('layouts.app')

@section('title', 'RSVPs: ' . $event->title)

@section('content')
    <x-page-header title="Event RSVPs" subtitle="{{ $event->title }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.events.index') }}" variant="ghost" icon="arrow-left">Back to Events</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-alert flash />

    @php
        $totalGuests = $rsvps->sum('guests');
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-4 text-center">
            <p class="text-2xl font-bold text-navy-900 dark:text-white">{{ $rsvps->total() }}</p>
            <p class="text-xs text-navy-500 mt-1">RSVPs</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-4 text-center">
            <p class="text-2xl font-bold text-navy-900 dark:text-white">{{ $totalGuests }}</p>
            <p class="text-xs text-navy-500 mt-1">Total Guests</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-4 text-center">
            <p class="text-sm font-semibold text-navy-900 dark:text-white">{{ $event->starts_at?->format('d M Y H:i') ?? '—' }}</p>
            <p class="text-xs text-navy-500 mt-1">Date & Time</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-4 text-center">
            <p class="text-sm font-semibold text-navy-900 dark:text-white">{{ $event->location ?? '—' }}</p>
            <p class="text-xs text-navy-500 mt-1">Location</p>
        </div>
    </div>

    <x-card icon="users">
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Name</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Guests</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Date</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($rsvps as $rsvp)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                            <td class="px-4 py-3.5 font-medium text-navy-900 dark:text-navy-100">{{ $rsvp->name }}</td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $rsvp->email }}</td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $rsvp->guests }}</td>
                            <td class="px-4 py-3.5 text-navy-500">{{ $rsvp->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <form method="POST" action="{{ route('admin.events.rsvps.destroy', [$event, $rsvp]) }}" onsubmit="return confirm('Remove this RSVP?')">
                                    @csrf @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-navy-500">No RSVPs for this event.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $rsvps->links() }}</div>
    </x-card>
@endsection