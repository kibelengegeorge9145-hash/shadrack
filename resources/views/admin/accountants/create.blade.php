@extends('layouts.app')

@section('title', 'Add Accountant')

@section('content')
    <x-page-header title="Add Accountant" subtitle="Register a new accountant and create their login account">
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

    <form method="POST" action="{{ route('admin.accountants.store') }}">
        @csrf

        <x-card title="Accountant Information" icon="briefcase">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="first_name" label="First Name" required placeholder="James" />
                <x-input name="middle_name" label="Middle Name" placeholder="Peter" />
                <x-input name="last_name" label="Last Name" required placeholder="Mwenda" />
                <x-input name="staff_id" label="Staff ID" placeholder="Leave blank to auto-generate" />
                <x-input name="phone" label="Phone" leadingIcon="phone" placeholder="+255 7XX XXX XXX" />
                <x-input name="email" label="Email" type="email" leadingIcon="mail" placeholder="finance@ebenezer.edu.tz" />
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" checked>
                        <span class="text-sm font-medium text-navy-800 dark:text-navy-100">Active account (can log in)</span>
                    </label>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.accountants.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="check">Create Accountant</x-button>
        </div>
    </form>
@endsection