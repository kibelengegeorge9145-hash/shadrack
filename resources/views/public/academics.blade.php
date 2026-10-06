@extends('layouts.guest')

@section('title', 'Academics')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">Academics</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">Comprehensive academic programs designed to prepare students for success in national and international examinations.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-navy-800 rounded-2xl border border-navy-100 dark:border-navy-700 p-8 shadow-card">
                    <div class="w-14 h-14 rounded-xl bg-navy-800 text-gold-300 flex items-center justify-center mb-5">
                        <x-icon name="book-open" class="w-7 h-7" />
                    </div>
                    <h2 class="font-display text-2xl font-bold text-navy-900 dark:text-white">O-Level Program</h2>
                    <p class="mt-3 text-navy-600 dark:text-navy-300 leading-relaxed">Our O-Level program (Form I-IV) provides a broad-based education covering core and optional subjects. Students are prepared for the National Form Four Examinations with a focus on both theoretical knowledge and practical skills.</p>
                    <x-button href="{{ route('olevel') }}" variant="primary" size="sm" icon="arrow-up" class="mt-5">View O-Level Details</x-button>
                </div>
                <div class="bg-white dark:bg-navy-800 rounded-2xl border border-navy-100 dark:border-navy-700 p-8 shadow-card">
                    <div class="w-14 h-14 rounded-xl bg-gold-400/15 text-gold-500 flex items-center justify-center mb-5">
                        <x-icon name="graduation-cap" class="w-7 h-7" />
                    </div>
                    <h2 class="font-display text-2xl font-bold text-navy-900 dark:text-white">A-Level Program</h2>
                    <p class="mt-3 text-navy-600 dark:text-navy-300 leading-relaxed">Our A-Level program (Form V-VI) offers specialized subject combinations preparing students for the National Advanced Certificate of Secondary Education Examinations and university admission.</p>
                    <x-button href="{{ route('alevel') }}" variant="primary" size="sm" icon="arrow-up" class="mt-5">View A-Level Details</x-button>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-20 bg-cream-50 dark:bg-navy-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight mb-4">Academic Calendar</h2>
            <p class="text-navy-500 dark:text-navy-400 max-w-lg mx-auto">Our academic year is structured into three terms with well-planned breaks to ensure optimal learning and rest.</p>
            <div class="mt-10 grid sm:grid-cols-3 gap-6 max-w-3xl mx-auto">
                @foreach (['Term 1: January – April', 'Term 2: May – August', 'Term 3: September – November'] as $term)
                    <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 shadow-card">
                        <x-icon name="calendar" class="w-8 h-8 text-gold-400 mx-auto mb-3" />
                        <p class="font-medium text-navy-900 dark:text-white text-sm">{{ $term }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
