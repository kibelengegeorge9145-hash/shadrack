@extends('layouts.guest')

@section('title', 'Home')

@section('content')
    {{-- ================= HERO ================= --}}
    <section class="relative isolate overflow-hidden py-20 sm:py-28 lg:py-36">
        {{-- Background image --}}
       <img src="{{ asset('storage/gallery/picha.png') }}"
     alt=""
     class="absolute inset-0 -z-20 h-full w-full object-cover object-center"
     aria-hidden="true">

        {{-- Navy overlay — darker on the left where the text sits, lighter on the right --}}
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-navy-950/95 via-navy-950/85 to-navy-900/70"></div>
        {{-- Soft top/bottom darkening for smooth blending with adjacent sections --}}
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-navy-950/60 via-transparent to-navy-950/40"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-gold-400/15 text-gold-300 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider mb-6">
                    <x-icon name="sparkles" class="w-4 h-4" /> Excellence in Education
                </span>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight">
                    Nurturing Tomorrow's Leaders at <span class="text-gold-300">Ebenezer</span>
                </h1>
                <p class="mt-6 text-lg text-navy-200 max-w-xl leading-relaxed">
                    A centre of academic excellence and character formation, empowering students to achieve their fullest potential in O-Level and A-Level education.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <x-button href="{{ route('admissions') }}" variant="gold" size="lg" icon="user-plus">Apply Now</x-button>
                    <x-button href="{{ route('about') }}" variant="outline" size="lg" icon="eye" class="border-navy-600 text-white hover:bg-navy-800">Learn More</x-button>
                </div>
            </div>
            <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-6 max-w-2xl">
                <div class="text-center">
                    <p class="font-display text-3xl font-extrabold text-gold-300">500+</p>
                    <p class="mt-1 text-sm text-navy-300">Students</p>
                </div>
                <div class="text-center">
                    <p class="font-display text-3xl font-extrabold text-gold-300">30+</p>
                    <p class="mt-1 text-sm text-navy-300">Teachers</p>
                </div>
                <div class="text-center">
                    <p class="font-display text-3xl font-extrabold text-gold-300">15+</p>
                    <p class="mt-1 text-sm text-navy-300">Years</p>
                </div>
                <div class="text-center">
                    <p class="font-display text-3xl font-extrabold text-gold-300">98%</p>
                    <p class="mt-1 text-sm text-navy-300">Pass Rate</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= PROGRAMS ================= --}}
    <section class="py-16 sm:py-20 bg-cream-50 dark:bg-navy-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12">
                <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">Our Programs</h2>
                <p class="mt-3 text-navy-500 dark:text-navy-400 max-w-lg mx-auto">Comprehensive academic programs designed to prepare students for national examinations and beyond.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 shadow-card hover:shadow-card-hover transition">
                    <div class="w-12 h-12 rounded-xl bg-navy-800 text-gold-300 flex items-center justify-center mb-4">
                        <x-icon name="book-open" class="w-6 h-6" />
                    </div>
                    <h3 class="font-display text-lg font-bold text-navy-900 dark:text-white">O-Level</h3>
                    <p class="mt-2 text-sm text-navy-500 dark:text-navy-400 leading-relaxed">Form I-IV comprehensive curriculum covering sciences, humanities, and vocational subjects.</p>
                    <x-button href="{{ route('olevel') }}" variant="ghost" size="sm" icon="arrow-up" class="mt-4">Learn More</x-button>
                </div>
                <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 shadow-card hover:shadow-card-hover transition">
                    <div class="w-12 h-12 rounded-xl bg-gold-400/15 text-gold-500 flex items-center justify-center mb-4">
                        <x-icon name="graduation-cap" class="w-6 h-6" />
                    </div>
                    <h3 class="font-display text-lg font-bold text-navy-900 dark:text-white">A-Level</h3>
                    <p class="mt-2 text-sm text-navy-500 dark:text-navy-400 leading-relaxed">Form V-VI advanced studies with specialized subject combinations for university preparation.</p>
                    <x-button href="{{ route('alevel') }}" variant="ghost" size="sm" icon="arrow-up" class="mt-4">Learn More</x-button>
                </div>
                <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 shadow-card hover:shadow-card-hover transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300 flex items-center justify-center mb-4">
                        <x-icon name="heart" class="w-6 h-6" />
                    </div>
                    <h3 class="font-display text-lg font-bold text-navy-900 dark:text-white">Character Formation</h3>
                    <p class="mt-2 text-sm text-navy-500 dark:text-navy-400 leading-relaxed">Holistic development through spiritual growth, discipline, and community service.</p>
                    <x-button href="{{ route('about') }}" variant="ghost" size="sm" icon="arrow-up" class="mt-4">Learn More</x-button>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= GALLERY ================= --}}
    @if ($recentPhotos->isNotEmpty())
        <section class="py-16 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">School Life Gallery</h2>
                        <p class="mt-2 text-navy-500">Moments from classrooms, labs, sports and events.</p>
                    </div>
                    <x-button href="{{ route('gallery.index') }}" variant="secondary" size="sm" icon="arrow-up">View All Photos</x-button>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($recentPhotos as $photo)
                        <a href="{{ route('gallery.index') }}" class="group relative rounded-xl overflow-hidden bg-navy-100 dark:bg-navy-800 aspect-square block">
                            @if ($photo->image)
                                <img src="{{ $photo->image }}" alt="{{ $photo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <x-icon name="image" class="w-10 h-10 text-navy-300" />
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-950/80 to-transparent p-3 pt-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                <p class="font-medium text-white text-xs">{{ $photo->title }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= LATEST NEWS ================= --}}
    @if ($latestNews->isNotEmpty())
        <section class="py-16 sm:py-20 bg-navy-1000">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 bg-cream-50">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">Latest News</h2>
                        <p class="mt-2 text-navy-500">Stay updated with what's happening at Ebenezer.</p>
                    </div>
                    <x-button href="{{ route('news.index') }}" variant="secondary" size="sm" icon="arrow-up">View All</x-button>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($latestNews as $article)
                        <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 overflow-hidden shadow-card hover:shadow-card-hover transition">
                            <div class="aspect-video bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-700 dark:to-navy-800 flex items-center justify-center">
                                @if ($article->image)
                                    <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-full h-full object-cover" />
                                @else
                                    <x-icon name="newspaper" class="w-10 h-10 text-navy-300 dark:text-navy-500" />
                                @endif
                            </div>
                            <div class="p-5">
                                <p class="text-xs text-navy-400 mb-2">{{ $article->published_at?->format('d M Y') }}</p>
                                <h3 class="font-display font-bold text-navy-900 dark:text-white">{{ $article->title }}</h3>
                                <p class="mt-2 text-sm text-navy-500 dark:text-navy-400 line-clamp-2">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->body), 100) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= UPCOMING EVENTS ================= --}}
    @if ($upcomingEvents->isNotEmpty())
        <section class="py-16 sm:py-20 bg-cream-50 dark:bg-navy-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">Upcoming Events</h2>
                        <p class="mt-2 text-navy-500">Don't miss what's coming up.</p>
                    </div>
                    <x-button href="{{ route('events.index') }}" variant="secondary" size="sm" icon="arrow-up">View All</x-button>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($upcomingEvents as $event)
                        <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 shadow-card">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-14 h-14 rounded-xl bg-navy-800 text-gold-300 flex flex-col items-center justify-center">
                                    <span class="text-xs font-semibold uppercase">{{ $event->starts_at?->format('M') }}</span>
                                    <span class="font-display text-lg font-bold leading-none">{{ $event->starts_at?->format('d') }}</span>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-navy-900 dark:text-white">{{ $event->title }}</h3>
                                    <p class="text-sm text-navy-500 dark:text-navy-400 mt-1">{{ $event->starts_at?->format('H:i') }}{{ $event->location ? ' · ' . $event->location : '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= CTA ================= --}}
    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">Ready to Join Ebenezer?</h2>
            <p class="mt-3 text-navy-500 dark:text-navy-400 max-w-lg mx-auto">Applications are now open for the upcoming academic year. Give your child the gift of quality education.</p>
            <div class="mt-8">
                <x-button href="{{ route('admissions') }}" variant="gold" size="lg" icon="user-plus">Apply Now</x-button>
            </div>
        </div>
    </section>
@endsection