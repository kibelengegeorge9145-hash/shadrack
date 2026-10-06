@extends('layouts.guest')

@section('title', 'O-Level Program')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-gold-400/15 text-gold-300 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider mb-4">Form I – IV</span>
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">O-Level Program</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">Building strong academic foundations for national examinations.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="max-w-3xl mx-auto">
                <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">Program Overview</h2>
                <p class="mt-4 text-navy-600 dark:text-navy-300 leading-relaxed">The O-Level program at Ebenezer covers Forms I through IV, providing students with a comprehensive education that balances core academic subjects with optional specializations. Our curriculum follows the Tanzania National Curriculum, enhanced with supplementary materials and practical sessions.</p>

                <h3 class="font-display text-xl font-bold text-navy-900 dark:text-white mt-10 mb-4">Core Subjects</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach (['Mathematics', 'English Language', 'Kiswahili', 'Civics', 'History', 'Geography', 'Physics', 'Chemistry', 'Biology', 'Commerce'] as $subject)
                        <div class="flex items-center gap-3 rounded-lg border border-navy-100 dark:border-navy-700 bg-white dark:bg-navy-800 px-4 py-3">
                            <x-icon name="check-circle" class="w-5 h-5 text-emerald-500 shrink-0" />
                            <span class="text-sm font-medium text-navy-900 dark:text-navy-100">{{ $subject }}</span>
                        </div>
                    @endforeach
                </div>

                <h3 class="font-display text-xl font-bold text-navy-900 dark:text-white mt-10 mb-4">Optional Subjects</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach (['Information and Computer Studies', 'Food and Nutrition', 'Agriculture', 'Fine Art', 'Physical Education', 'French'] as $subject)
                        <div class="flex items-center gap-3 rounded-lg border border-navy-100 dark:border-navy-700 bg-white dark:bg-navy-800 px-4 py-3">
                            <x-icon name="plus" class="w-5 h-5 text-gold-400 shrink-0" />
                            <span class="text-sm font-medium text-navy-900 dark:text-navy-100">{{ $subject }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 text-center">
                    <x-button href="{{ route('admissions') }}" variant="gold" size="lg" icon="user-plus">Apply for O-Level</x-button>
                </div>
            </div>
        </div>
    </section>
@endsection
