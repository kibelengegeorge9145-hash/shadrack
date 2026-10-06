@extends('layouts.guest')

@section('title', 'Application Status')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">Application Status</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">Track the progress of your admission application using your application number.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-xl mx-auto px-4 sm:px-6">
            <x-alert flash />

            @isset($checked)
                <div class="mb-6 bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 p-6 shadow-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-navy-500">Application Number</p>
                            <p class="font-mono font-semibold text-navy-900 dark:text-white">{{ $checked->application_number }}</p>
                        </div>
                        @php
                            $colors = ['pending' => 'yellow', 'under_review' => 'blue', 'accepted' => 'green', 'rejected' => 'red'];
                        @endphp
                        <x-badge color="{{ $colors[$checked->status] ?? 'gray' }}">{{ ucwords(str_replace('_', ' ', $checked->status)) }}</x-badge>
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-navy-500">Applicant</dt>
                            <dd class="text-navy-900 dark:text-navy-100 font-medium text-right">
                                {{ $checked->first_name }} {{ $checked->middle_name }} {{ $checked->last_name }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-navy-500">Applying For</dt>
                            <dd class="text-navy-900 dark:text-navy-100 text-right">{{ $checked->applying_class }} ({{ $checked->level }})</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-navy-500">Submitted</dt>
                            <dd class="text-navy-900 dark:text-navy-100 text-right">{{ $checked->created_at->format('d M Y') }}</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-xs text-navy-400">Last updated {{ $checked->updated_at->diffForHumans() }}</p>
                </div>
            @endisset

            <x-card title="Check Status" icon="search">
                <form method="POST" action="{{ route('admissions.status') }}" class="space-y-4">
                    @csrf
                    <div style="position:absolute;left:-9999px" aria-hidden="true">
                        <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>
                    <x-input name="application_number" label="Application Number" placeholder="ADM-2026-00001" required />
                    <p class="text-xs text-navy-500">Provide the email or phone number used during application.</p>
                    <x-input type="email" name="parent_email" label="Parent Email" />
                    <x-input name="parent_phone" label="Parent Phone" />
                    <x-button type="submit" variant="gold" icon="search" class="w-full">Check Status</x-button>
                </form>
            </x-card>
        </div>
    </section>
@endsection