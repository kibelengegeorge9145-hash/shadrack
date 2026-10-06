@extends('layouts.guest')

@section('title', 'Events')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">Events</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">Upcoming events and activities at Ebenezer Secondary School.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            @if ($events->isEmpty())
                <div class="text-center py-16">
                    <x-icon name="calendar-check" class="w-12 h-12 text-navy-300 mx-auto mb-4" />
                    <h3 class="font-display text-xl font-bold text-navy-800 dark:text-navy-100">No Events</h3>
                    <p class="text-navy-500 mt-2">No upcoming events at this time.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($events as $event)
                        <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 shadow-card">
                            <div class="flex items-start gap-5">
                                <div class="shrink-0 w-16 h-16 rounded-xl bg-navy-800 text-gold-300 flex flex-col items-center justify-center">
                                    <span class="text-xs font-semibold uppercase">{{ $event->starts_at?->format('M') }}</span>
                                    <span class="font-display text-xl font-bold leading-none">{{ $event->starts_at?->format('d') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-display text-lg font-bold text-navy-900 dark:text-white">{{ $event->title }}</h3>
                                    <div class="mt-1 flex flex-wrap items-center gap-3 text-sm text-navy-500 dark:text-navy-400">
                                        <span class="flex items-center gap-1"><x-icon name="clock" class="w-4 h-4" /> {{ $event->starts_at?->format('H:i') }}{{ $event->ends_at ? ' – ' . $event->ends_at->format('H:i') : '' }}</span>
                                        @if ($event->location)
                                            <span class="flex items-center gap-1"><x-icon name="map-pin" class="w-4 h-4" /> {{ $event->location }}</span>
                                        @endif
                                        @php $count = $event->rsvps()->sum('guests'); @endphp
                                        @if ($count > 0)
                                            <span class="flex items-center gap-1"><x-icon name="users" class="w-4 h-4" /> {{ $count }} attending</span>
                                        @endif
                                    </div>
                                    @if ($event->description)
                                        <p class="mt-2 text-sm text-navy-600 dark:text-navy-300">{{ \Illuminate\Support\Str::limit($event->description, 200) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-navy-100 dark:border-navy-700">
                                <details>
                                    <summary class="cursor-pointer text-sm font-semibold text-navy-700 dark:text-navy-200 hover:text-gold-600 dark:hover:text-gold-300">RSVP to this event</summary>
                                    <form method="POST" action="{{ route('events.rsvp', $event) }}" class="mt-4 space-y-3">
                                        @csrf
                                        <div style="position:absolute;left:-9999px" aria-hidden="true">
                                            <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                                        </div>
                                        <div class="grid sm:grid-cols-3 gap-3">
                                            <x-input name="name" label="Your Name" required />
                                            <x-input type="email" name="email" label="Your Email" required />
                                            <x-input type="number" name="guests" label="Guests" value="1" min="1" max="10" required />
                                        </div>
                                        <x-button type="submit" variant="gold" size="sm" icon="check">Submit RSVP</x-button>
                                    </form>
                                </details>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-10">{{ $events->links() }}</div>
            @endif
        </div>
    </section>
@endsection
