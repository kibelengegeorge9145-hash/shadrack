@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <h2 class="font-display text-3xl font-bold text-navy-950 dark:text-white">Reset your password</h2>
    <p class="mt-2 text-navy-500 dark:text-navy-400">Enter your email address and we'll send you a password reset link.</p>

    @if (session('status'))
        <x-alert type="success" dismissible class="mt-6">{{ session('status') }}</x-alert>
    @endif

    @if ($errors->any())
        <x-alert type="error" dismissible class="mt-6">{{ $errors->first() }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf
        <x-input type="email" name="email" label="Email Address" required leadingIcon="mail"
            autofocus autocomplete="email" />
        <x-button type="submit" icon="mail" class="w-full" size="lg">Send Reset Link</x-button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-medium text-gold-600 dark:text-gold-300 hover:underline">Back to sign in</a>
    </p>
@endsection
