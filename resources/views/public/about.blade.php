@extends('layouts.guest')

@section('title', 'About Us')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">About Ebenezer</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">A legacy of academic excellence and character formation in the heart of Tanzania.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">Our Story</h2>
                    <p class="mt-4 text-navy-600 dark:text-navy-300 leading-relaxed">Ebenezer Secondary School was established with a clear vision: to provide quality education that nurtures both academic excellence and strong moral values. Named after the biblical Ebenezer — "Thus far the Lord has helped us" — our school stands as a testament to faith-driven educational excellence.</p>
                    <p class="mt-4 text-navy-600 dark:text-navy-300 leading-relaxed">Over the years, we have grown from a small institution to a reputable school serving hundreds of students from the East Region and beyond. Our graduates consistently perform among the best in national examinations.</p>
                </div>
                <div class="bg-gradient-to-br from-navy-100 to-cream-100 dark:from-navy-800 dark:to-navy-700 rounded-2xl aspect-[4/3] flex items-center justify-center">
                    <div class="text-center">
                        <span class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-navy-950 text-gold-300 font-display font-extrabold text-3xl">E</span>
                        <p class="mt-4 font-display font-bold text-navy-900 dark:text-white text-lg">Ebenezer Secondary School</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-20 bg-cream-50 dark:bg-navy-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight text-center mb-12">Our Values</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                    ['icon' => 'target', 'title' => 'Excellence', 'desc' => 'Striving for the highest standards in academics and character.'],
                    ['icon' => 'heart', 'title' => 'Integrity', 'desc' => 'Building honest, responsible, and morally upright citizens.'],
                    ['icon' => 'users', 'title' => 'Community', 'desc' => 'Fostering a supportive and inclusive learning environment.'],
                    ['icon' => 'sparkles', 'title' => 'Innovation', 'desc' => 'Embracing modern teaching methods and technologies.'],
                ] as $value)
                    <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 text-center shadow-card">
                        <div class="w-12 h-12 rounded-xl bg-navy-800 text-gold-300 flex items-center justify-center mx-auto mb-4">
                            <x-icon :name="$value['icon']" class="w-6 h-6" />
                        </div>
                        <h3 class="font-display font-bold text-navy-900 dark:text-white">{{ $value['title'] }}</h3>
                        <p class="mt-2 text-sm text-navy-500 dark:text-navy-400">{{ $value['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">Our Mission</h2>
            <p class="mt-4 text-navy-600 dark:text-navy-300 max-w-2xl mx-auto text-lg leading-relaxed">To provide holistic education that develops intellectually competent, morally upright, and socially responsible individuals who contribute positively to society.</p>
        </div>
    </section>
@endsection
