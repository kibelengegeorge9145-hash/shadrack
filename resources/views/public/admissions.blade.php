@extends('layouts.guest')

@section('title', 'Admissions')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">Admissions</h1>
        <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">Begin your journey at Ebenezer Secondary School. Applications are open for both O-Level and A-Level programs.</p>

        <div class="mt-6">
            <a href="{{ route('admissions.status-form') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-gold-400/40 bg-white/5 hover:bg-white/10 px-5 py-2.5 text-sm font-semibold text-gold-300 transition-colors">
                <x-icon name="search" class="w-4 h-4" />
                Already applied? Check your application status
            </a>
        </div>
    </div>
</section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-12">
                <div>
                    <h2 class="font-display text-3xl font-bold text-navy-900 dark:text-white tracking-tight">How to Apply</h2>
                    <p class="mt-4 text-navy-600 dark:text-navy-300 leading-relaxed">Follow these simple steps to submit your application:</p>

                    <div class="mt-8 space-y-6">
                        @foreach ([
                            ['step' => '01', 'title' => 'Fill the Form', 'desc' => 'Complete the online application form with your details and academic information.'],
                            ['step' => '02', 'title' => 'Submit Application', 'desc' => 'Submit your application and receive an application number for tracking.'],
                            ['step' => '03', 'title' => 'Review Process', 'desc' => 'Our admissions team will review your application and may schedule an interview.'],
                            ['step' => '04', 'title' => 'Enrollment', 'desc' => 'Upon acceptance, complete the enrollment process and begin your journey.'],
                        ] as $item)
                            <div class="flex gap-4">
                                <span class="shrink-0 w-10 h-10 rounded-xl bg-navy-800 text-gold-300 font-display font-bold text-sm flex items-center justify-center">{{ $item['step'] }}</span>
                                <div>
                                    <h3 class="font-display font-bold text-navy-900 dark:text-white">{{ $item['title'] }}</h3>
                                    <p class="text-sm text-navy-500 dark:text-navy-400 mt-1">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <x-card title="Apply Now" icon="user-plus">
                        <x-alert flash />

                        @if (session('success'))
                            <div class="mb-4">
                                <x-alert type="success">{{ session('success') }}</x-alert>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admissions.submit') }}" class="space-y-4">
                            @csrf
                            <div class="grid sm:grid-cols-3 gap-3">
                                <x-input name="first_name" label="First Name" required />
                                <x-input name="middle_name" label="Middle Name" />
                                <x-input name="last_name" label="Last Name" required />
                            </div>
                            <div class="grid sm:grid-cols-2 gap-3">
                                <x-input type="date" name="date_of_birth" label="Date of Birth" required />
                                <x-select name="gender" label="Gender" :options="['male' => 'Male', 'female' => 'Female']" placeholder="Select" required />
                            </div>
                            <div class="grid sm:grid-cols-2 gap-3">
                                <x-select name="level" label="Level" :options="['O-Level' => 'O-Level', 'A-Level' => 'A-Level']" placeholder="Select level" required />
                                <x-input name="applying_class" label="Applying For Class" placeholder="e.g. Form III" required />
                            </div>
                            <x-input name="current_school" label="Current School (Optional)" />
                            <div class="grid sm:grid-cols-2 gap-3">
                                <x-input name="parent_name" label="Parent/Guardian Name" required />
                                <x-input name="parent_phone" label="Parent Phone" required />
                            </div>
                            <x-input type="email" name="parent_email" label="Parent Email (Optional)" />
                            <x-textarea name="notes" label="Additional Notes (Optional)" rows="2" />
                            <x-button type="submit" variant="gold" icon="check" class="w-full">Submit Application</x-button>
</form>

<div class="mt-4 pt-4 border-t border-navy-100 dark:border-navy-700">
    <p class="text-xs text-navy-500 dark:text-navy-400 text-center">
        After submitting, you'll receive an application number.
        <a href="{{ route('admissions.status-form') }}"
           class="font-semibold text-navy-800 dark:text-gold-300 hover:underline">
            Use it to check your status →
        </a>
    </p>
</div>
</x-card>
                </div>
            </div>
        </div>
    </section>
@endsection
