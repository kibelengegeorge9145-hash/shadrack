@extends('layouts.app')

@section('title', 'New Admission')

@section('content')
    <x-page-header title="New Admission Application" subtitle="Register a new student admission" />

    <x-alert flash />

    <div class="max-w-3xl">
        <x-card icon="user-plus">
            <form method="POST" action="{{ route('admin.admissions.store') }}" class="space-y-6">
                @csrf
                <div class="grid sm:grid-cols-3 gap-4">
                    <x-input name="first_name" label="First Name" required />
                    <x-input name="middle_name" label="Middle Name" />
                    <x-input name="last_name" label="Last Name" required />
                </div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <x-input type="date" name="date_of_birth" label="Date of Birth" required />
                    <x-select name="gender" label="Gender" :options="['male' => 'Male', 'female' => 'Female']" placeholder="Select gender" required />
                    <x-select name="level" label="Level" :options="['O-Level' => 'O-Level', 'A-Level' => 'A-Level']" placeholder="Select level" required />
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-input name="current_school" label="Current School" />
                    <x-input name="applying_class" label="Applying For Class" required />
                </div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <x-input name="parent_name" label="Parent/Guardian Name" required />
                    <x-input name="parent_phone" label="Parent Phone" required />
                    <x-input type="email" name="parent_email" label="Parent Email" />
                </div>
                <x-textarea name="notes" label="Notes" placeholder="Any additional information..." rows="3" />
                <div class="flex justify-end gap-3 pt-2">
                    <x-button href="{{ route('admin.admissions.index') }}" variant="secondary">Cancel</x-button>
                    <x-button type="submit" variant="gold" icon="plus">Create Application</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
