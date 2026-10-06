@extends('layouts.app')

@section('title', 'Add Student')

@section('content')
    <x-page-header title="Add Student" subtitle="Register a new student and create their login account">
        <x-slot:actions>
            <x-button href="{{ route('admin.students.index') }}" variant="secondary" icon="chevron-left">Back to Students</x-button>
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

    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf

        <x-card title="Personal Information" icon="user">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="first_name" label="First Name" required placeholder="John" />
                <x-input name="middle_name" label="Middle Name" placeholder="Michael" />
                <x-input name="last_name" label="Last Name" required placeholder="Doe" />
                <x-input name="admission_number" label="Admission Number" placeholder="Leave blank to auto-generate" />
                <x-input name="date_of_birth" label="Date of Birth" type="date" required />
                <div>
                    <x-select name="gender" label="Gender" :options="['male' => 'Male', 'female' => 'Female']" required placeholder="Select gender" />
                </div>
                <x-input name="phone" label="Phone" leadingIcon="phone" placeholder="+255 7XX XXX XXX" />
                <x-input name="email" label="Email" type="email" leadingIcon="mail" placeholder="student@example.com" />
            </div>
        </x-card>

        <x-card title="Enrolment" icon="graduation-cap" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-select name="level" label="Level" required :options="['O-Level' => 'O-Level', 'A-Level' => 'A-Level']" placeholder="Select level" />
                </div>
                <div>
                    <x-select name="school_class_id" label="Class" required :options="$classes" placeholder="Select class" />
                </div>
                <div>
                    <x-select name="parent_id" label="Parent / Guardian" :options="$parents" placeholder="Optional" />
                </div>
                <div>
                    <x-select name="status" label="Status" required :options="['active' => 'Active', 'inactive' => 'Inactive', 'graduated' => 'Graduated', 'transferred' => 'Transferred']" selected="active" />
                </div>
                <x-input name="enrolled_on" label="Enrolled On" type="date" value="{{ now()->toDateString() }}" />
            </div>
        </x-card>

        @if ($subjects->count())
            <x-card title="Subjects" icon="book-open" class="mt-6">
                <p class="text-sm text-navy-500 dark:text-navy-400 mb-4">Select the subjects this student will study ({{ $academicYear?->name ?? 'current academic year' }}).</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($subjects as $id => $name)
                        <label class="flex items-center gap-3 rounded-lg border border-navy-100 dark:border-navy-700 px-4 py-3 hover:bg-cream-50 dark:hover:bg-navy-800 transition-colors cursor-pointer">
                            <input type="checkbox" name="subjects[]" value="{{ $id }}" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" {{ in_array($id, old('subjects', [])) ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-navy-800 dark:text-navy-100">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
            </x-card>
        @endif

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.students.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="check">Create Student</x-button>
        </div>
    </form>
@endsection