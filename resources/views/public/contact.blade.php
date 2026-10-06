@extends('layouts.guest')

@section('title', 'Contact')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">Contact Us</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">We'd love to hear from you. Reach out for inquiries about admissions, academics, or general information.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-8 shadow-card text-center">
                    <div class="w-14 h-14 rounded-xl bg-navy-800 text-gold-300 flex items-center justify-center mx-auto mb-4">
                        <x-icon name="map-pin" class="w-7 h-7" />
                    </div>
                    <h3 class="font-display font-bold text-navy-900 dark:text-white">Visit Us</h3>
                    <p class="mt-2 text-sm text-navy-500 dark:text-navy-400">East Region, Tanzania</p>
                </div>
                <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-8 shadow-card text-center">
                    <div class="w-14 h-14 rounded-xl bg-gold-400/15 text-gold-500 flex items-center justify-center mx-auto mb-4">
                        <x-icon name="phone" class="w-7 h-7" />
                    </div>
                    <h3 class="font-display font-bold text-navy-900 dark:text-white">Call Us</h3>
                    <p class="mt-2 text-sm text-navy-500 dark:text-navy-400">+255 000 000 000</p>
                </div>
                <div class="bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-8 shadow-card text-center">
                    <div class="w-14 h-14 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300 flex items-center justify-center mx-auto mb-4">
                        <x-icon name="mail" class="w-7 h-7" />
                    </div>
                    <h3 class="font-display font-bold text-navy-900 dark:text-white">Email Us</h3>
                    <p class="mt-2 text-sm text-navy-500 dark:text-navy-400">info@ebenezer.edu.tz</p>
                </div>
            </div>

            <div class="mt-16 max-w-2xl mx-auto">
                <x-alert flash />
                <x-card title="Send Us a Message" icon="mail" class="shadow-card">
                    <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4">
                        @csrf
                        <div style="position:absolute;left:-9999px" aria-hidden="true">
                            <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <x-input name="name" label="Your Name" required />
                            <x-input type="email" name="email" label="Your Email" required />
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <x-input name="phone" label="Phone (optional)" />
                            <x-input name="subject" label="Subject" required />
                        </div>
                        <x-textarea name="message" label="Message" rows="5" required />
                        <div class="flex justify-end">
                            <x-button type="submit" variant="gold" icon="send">Send Message</x-button>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </section>
@endsection
