@extends('layouts.guest')

@section('title', 'A-Level Program')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-gold-400/15 text-gold-300 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider mb-4">Form V – VI</span>
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">A-Level Program</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">Advanced studies preparing students for university and beyond.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="max-w-3xl mx-auto">
                <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">Program Overview</h2>
                <p class="mt-4 text-navy-600 dark:text-navy-300 leading-relaxed">The A-Level program at Ebenezer covers Forms V and VI, offering specialized subject combinations that prepare students for the National Advanced Certificate of Secondary Education Examinations (ACSEE). Our program emphasizes critical thinking, research skills, and depth of knowledge in chosen subject areas.</p>

                <h3 class="font-display text-xl font-bold text-navy-900 dark:text-white mt-10 mb-4">Science Combinations</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach ([
                        'PCB' => 'Physics, Chemistry, Biology',
                        'PCM' => 'Physics, Chemistry, Mathematics',
                        'PGM' => 'Physics, Geography, Mathematics',
                        'CBG' => 'Chemistry, Biology, Geography',
                    ] as $key => $combo)
                        <div class="rounded-lg border border-navy-100 dark:border-navy-700 bg-white dark:bg-navy-800 px-4 py-3">
                            <p class="font-semibold text-navy-900 dark:text-white text-sm">{{ $key }}</p>
                            <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">{{ $combo }}</p>
                        </div>
                    @endforeach
                </div>

                <h3 class="font-display text-xl font-bold text-navy-900 dark:text-white mt-10 mb-4">Arts Combinations</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach ([
                        'HGK' => 'History, Geography, Kiswahili',
                        'HGL' => 'History, Geography, English Literature',
                        'HEK' => 'History, Economics, Kiswahili',
                        'HEL' => 'History, Economics, English Literature',
                    ] as $key => $combo)
                        <div class="rounded-lg border border-navy-100 dark:border-navy-700 bg-white dark:bg-navy-800 px-4 py-3">
                            <p class="font-semibold text-navy-900 dark:text-white text-sm">{{ $key }}</p>
                            <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">{{ $combo }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 text-center">
                    <x-button href="{{ route('admissions') }}" variant="gold" size="lg" icon="user-plus">Apply for A-Level</x-button>
                </div>
            </div>
        </div>
    </section>
@endsection
