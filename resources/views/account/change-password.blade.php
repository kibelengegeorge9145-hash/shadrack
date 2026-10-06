@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
    <x-page-header title="Change Password" subtitle="Keep your account secure by using a strong, unique password." />

    @if (session('info'))
        <x-alert type="info" dismissible class="mb-6">{{ session('info') }}</x-alert>
    @endif
    @if (session('success'))
        <x-alert type="success" dismissible class="mb-6">{{ session('success') }}</x-alert>
    @endif
    @if ($errors->any())
        <x-alert type="error" title="Unable to update password" dismissible class="mb-6">
            <ul class="list-disc pl-4 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <x-card class="max-w-lg">
        @if (auth()->user()->must_change_password)
            <div class="mb-6 rounded-lg bg-gold-400/15 border border-gold-400/40 px-4 py-3 text-sm text-navy-800 dark:text-gold-300">
                <strong>First-time login:</strong> You must set a new password before you can continue.
            </div>
        @endif

        <form method="POST" action="{{ route('account.password.change') }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-input type="password" name="current_password" label="Current Password" required leadingIcon="lock"
                autocomplete="current-password" />
            <x-input type="password" name="password" label="New Password" required leadingIcon="lock"
                autocomplete="new-password" />
            <x-input type="password" name="password_confirmation" label="Confirm New Password" required leadingIcon="lock"
                autocomplete="new-password" />

            <div class="flex justify-end">
                <x-button type="submit" icon="check-circle" variant="gold">Update Password</x-button>
            </div>
        </form>
    </x-card>
@endsection
