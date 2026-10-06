@extends('layouts.guest')

@section('title', 'Announcements')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">Announcements</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">The latest notices and updates from Ebenezer Secondary School.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            @if ($announcements->isEmpty())
                <div class="text-center py-16">
                    <x-icon name="megaphone" class="w-12 h-12 text-navy-300 mx-auto mb-4" />
                    <h3 class="font-display text-xl font-bold text-navy-800 dark:text-navy-100">No Announcements</h3>
                    <p class="text-navy-500 mt-2">There are no announcements at this time.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($announcements as $announcement)
                        <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 shadow-card">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <h3 class="font-display text-lg font-bold text-navy-900 dark:text-white">{{ $announcement->title }}</h3>
                                    <p class="mt-2 text-navy-600 dark:text-navy-300 whitespace-pre-line">{{ $announcement->body }}</p>
                                </div>
                                <x-badge color="gold" class="shrink-0">{{ ucfirst(str_replace('_', '-', $announcement->audience)) }}</x-badge>
                            </div>
                            <p class="mt-4 text-xs text-navy-400">{{ $announcement->published_at?->format('d M Y, H:i') ?? $announcement->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-10">{{ $announcements->links() }}</div>
            @endif
        </div>
    </section>
@endsection