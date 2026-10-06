@extends('layouts.app')

@section('title', 'Add Teacher')

@section('content')
    <x-page-header title="Add Teacher" subtitle="Register a new teacher and create their login account">
        <x-slot:actions>
            <x-button href="{{ route('admin.teachers.index') }}" variant="secondary" icon="chevron-left">Back to Teachers</x-button>
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

    <form method="POST" action="{{ route('admin.teachers.store') }}">
        @csrf

        <x-card title="Personal Information" icon="user">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="first_name" label="First Name" required placeholder="Jane" />
                <x-input name="middle_name" label="Middle Name" placeholder="Ann" />
                <x-input name="last_name" label="Last Name" required placeholder="Kamau" />
                <x-input name="staff_id" label="Staff ID" placeholder="Leave blank to auto-generate" />
                <div>
                    <x-select name="gender" label="Gender" required :options="['male' => 'Male', 'female' => 'Female']" placeholder="Select gender" />
                </div>
                <x-input name="date_of_birth" label="Date of Birth" type="date" />
                <x-input name="phone" label="Phone" leadingIcon="phone" placeholder="+255 7XX XXX XXX" />
                <x-input name="email" label="Email" type="email" leadingIcon="mail" placeholder="teacher@ebenezer.edu.tz" />
            </div>
        </x-card>

        <x-card title="Employment" icon="briefcase" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input name="department" label="Department" placeholder="Mathematics" />
                <x-input name="qualification" label="Qualification" placeholder="BSc. Mathematics, PGDE" />
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" checked>
                        <span class="text-sm font-medium text-navy-800 dark:text-navy-100">Active account (can log in)</span>
                    </label>
                </div>
            </div>
        </x-card>

        @if ($subjects->count())
            <x-card title="Subjects" icon="book-open" class="mt-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($subjects as $id => $name)
                        <label class="flex items-center gap-3 rounded-lg border border-navy-100 dark:border-navy-700 px-4 py-3 hover:bg-cream-50 dark:hover:bg-navy-800 transition-colors cursor-pointer">
                            <input type="checkbox" name="subjects[]" value="{{ $id }}" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40">
                            <span class="text-sm font-medium text-navy-800 dark:text-navy-100">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
            </x-card>
        @endif

        @if ($classes->count())
            <x-card title="Assigned Classes" icon="building" class="mt-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($classes as $id => $name)
                        <label class="flex items-center gap-3 rounded-lg border border-navy-100 dark:border-navy-700 px-4 py-3 hover:bg-cream-50 dark:hover:bg-navy-800 transition-colors cursor-pointer">
                            <input type="checkbox" name="classes[]" value="{{ $id }}" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40">
                            <span class="text-sm font-medium text-navy-800 dark:text-navy-100">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
            </x-card>
        @endif

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.teachers.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="check">Create Teacher</x-button>
        </div>
    </form>
@endsection