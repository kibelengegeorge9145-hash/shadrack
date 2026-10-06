@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <x-page-header title="My Profile" subtitle="View and update your account information." />

    @if (session('success'))
        <x-alert type="success" dismissible class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <x-card>
            <div class="flex flex-col items-center text-center">
                <span class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-navy-950 text-gold-300 text-3xl font-bold">
                    {{ auth()->user()->initials }}
                </span>
                <h3 class="mt-4 font-display text-xl font-bold text-navy-900 dark:text-white">{{ auth()->user()->full_name }}</h3>
                <p class="text-sm text-navy-500 capitalize">{{ auth()->user()->role_label }}</p>
                <div class="mt-4 w-full text-left text-sm space-y-1.5">
                    @if (auth()->user()->admission_number)
                        <p class="flex justify-between"><span class="text-navy-500">Adm. No</span> <span class="font-medium">{{ auth()->user()->admission_number }}</span></p>
                    @endif
                    @if (auth()->user()->staff_id)
                        <p class="flex justify-between"><span class="text-navy-500">Staff ID</span> <span class="font-medium">{{ auth()->user()->staff_id }}</span></p>
                    @endif
                    <p class="flex justify-between"><span class="text-navy-500">Username</span> <span class="font-medium">{{ auth()->user()->username }}</span></p>
                    <p class="flex justify-between"><span class="text-navy-500">Last login</span> <span class="font-medium">{{ auth()->user()->last_login_at?->format('d M Y, H:i') ?? '—' }}</span></p>
                </div>
            </div>
        </x-card>

        <x-card class="lg:col-span-2">
            <h3 class="font-display text-lg font-bold text-navy-900 dark:text-white mb-5">Contact Information</h3>
            <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')
                <x-input name="phone" label="Phone Number" leadingIcon="phone" value="{{ auth()->user()->phone }}" />
                <x-input type="email" name="email" label="Email Address" leadingIcon="mail" value="{{ auth()->user()->email }}" />
                <div class="flex justify-end">
                    <x-button type="submit" icon="save">Save Changes</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
