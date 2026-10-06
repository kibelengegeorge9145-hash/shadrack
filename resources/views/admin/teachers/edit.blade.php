@extends('layouts.app')

@section('title', 'Edit Teacher')

@section('content')
    <x-page-header title="Edit Teacher" subtitle="{{ $teacher->full_name }} · {{ $teacher->staff_id }}">
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

    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
        @csrf
        @method('PUT')

        <x-card title="Personal Information" icon="user">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="first_name" label="First Name" required :value="$teacher->first_name" />
                <x-input name="middle_name" label="Middle Name" :value="$teacher->middle_name" />
                <x-input name="last_name" label="Last Name" required :value="$teacher->last_name" />
                <x-input name="staff_id" label="Staff ID" required :value="$teacher->staff_id" />
                <div>
                    <x-select name="gender" label="Gender" required :options="['male' => 'Male', 'female' => 'Female']" :selected="$teacher->gender" />
                </div>
                <x-input name="date_of_birth" label="Date of Birth" type="date" :value="$teacher->date_of_birth?->format('Y-m-d')" />
                <x-input name="phone" label="Phone" leadingIcon="phone" :value="$teacher->phone" />
                <x-input name="email" label="Email" type="email" leadingIcon="mail" :value="$teacher->email" />
            </div>
        </x-card>

        <x-card title="Employment" icon="briefcase" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input name="department" label="Department" :value="$teacher->department" />
                <x-input name="qualification" label="Qualification" :value="$teacher->qualification" />
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" {{ $teacher->is_active ? 'checked' : '' }}>
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
                            <input type="checkbox" name="subjects[]" value="{{ $id }}" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" {{ in_array($id, old('subjects', $selectedSubjects)) ? 'checked' : '' }}>
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
                            <input type="checkbox" name="classes[]" value="{{ $id }}" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" {{ in_array($id, old('classes', $selectedClasses)) ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-navy-800 dark:text-navy-100">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
            </x-card>
        @endif

        <x-card title="Login Account" icon="key" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input name="password" label="New Password" type="password" placeholder="Leave blank to keep current" />
                <x-input name="password_confirmation" label="Confirm Password" type="password" placeholder="Repeat new password" />
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.teachers.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="save">Save Changes</x-button>
        </div>
    </form>
@endsection