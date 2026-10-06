@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <h2 class="font-display text-3xl font-bold text-navy-950 dark:text-white">Choose a new password</h2>
    <p class="mt-2 text-navy-500 dark:text-navy-400">Set a new password for your account.</p>

    @if ($errors->any())
        <x-alert type="error" dismissible class="mt-6">{{ $errors->first() }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-input type="email" name="email" label="Email Address" required leadingIcon="mail"
            value="{{ $email ?? old('email') }}" autocomplete="email" />
        <x-input type="password" name="password" label="New Password" required leadingIcon="lock"
            autocomplete="new-password" />
        <x-input type="password" name="password_confirmation" label="Confirm Password" required leadingIcon="lock"
            autocomplete="new-password" />
        <x-button type="submit" icon="check-circle" class="w-full" size="lg">Reset Password</x-button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-medium text-gold-600 dark:text-gold-300 hover:underline">Back to sign in</a>
    </p>
@endsection
