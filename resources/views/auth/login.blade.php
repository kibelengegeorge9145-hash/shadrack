@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h2 class="font-display text-3xl font-bold text-navy-950 dark:text-white">Welcome back</h2>
    <p class="mt-2 text-navy-500 dark:text-navy-400">Sign in to your school portal.</p>

    @if (session('status'))
        <x-alert type="success" class="mt-6">{{ session('status') }}</x-alert>
    @endif

    @if ($errors->any())
        <x-alert type="error" title="Unable to sign in" dismissible class="mt-6">
            <ul class="list-disc pl-4 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <x-input name="identifier" label="Username, Email, Phone or Admission/Staff ID" required
            leadingIcon="user" placeholder="e.g. john@example.com" autofocus autocomplete="username" />

        <div>
            <x-input type="password" name="password" label="Password" required leadingIcon="lock"
                placeholder="••••••••" autocomplete="current-password" />
            <div class="mt-2 text-right">
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-gold-600 dark:text-gold-300 hover:underline">Forgot password?</a>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-navy-700 dark:text-navy-200">
            <input type="checkbox" name="remember" value="1"
                class="h-4 w-4 rounded border-navy-300 text-gold-500 focus:ring-gold-400 dark:bg-navy-800 dark:border-navy-600">
            Remember me
        </label>

        <x-button type="submit" icon="log-in" class="w-full" size="lg">Sign in</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-navy-500 dark:text-navy-400">
        Need an account? Contact the school administration to register.
    </p>
@endsection
