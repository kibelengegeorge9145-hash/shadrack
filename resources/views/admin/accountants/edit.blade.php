@extends('layouts.app')

@section('title', 'Edit Accountant')

@section('content')
    <x-page-header title="Edit Accountant" subtitle="{{ $accountant->full_name }} · {{ $accountant->staff_id }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.accountants.index') }}" variant="secondary" icon="chevron-left">Back to Accountants</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <x-alert type="error" title="Please fix the following errors" class="mb-6">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('admin.accountants.update', $accountant) }}">
        @csrf
        @method('PUT')

        <x-card title="Accountant Information" icon="briefcase">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="first_name" label="First Name" required :value="$accountant->first_name" />
                <x-input name="middle_name" label="Middle Name" :value="$accountant->middle_name" />
                <x-input name="last_name" label="Last Name" required :value="$accountant->last_name" />
                <x-input name="staff_id" label="Staff ID" required :value="$accountant->staff_id" />
                <x-input name="phone" label="Phone" leadingIcon="phone" :value="$accountant->phone" />
                <x-input name="email" label="Email" type="email" leadingIcon="mail" :value="$accountant->email" />
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" {{ $accountant->is_active ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-navy-800 dark:text-navy-100">Active account (can log in)</span>
                    </label>
                </div>
            </div>
        </x-card>

        <x-card title="Login Account" icon="key" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input name="password" label="New Password" type="password" placeholder="Leave blank to keep current" />
                <x-input name="password_confirmation" label="Confirm Password" type="password" placeholder="Repeat new password" />
                @if ($accountant->user)
                    <div>
                        <x-label>Username</x-label>
                        <p class="mt-1.5 text-sm text-navy-700 dark:text-navy-200 font-medium">{{ $accountant->user->username }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.accountants.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="save">Save Changes</x-button>
        </div>
    </form>
@endsection