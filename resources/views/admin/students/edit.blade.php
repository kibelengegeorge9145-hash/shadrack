@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
    <x-page-header title="Edit Student" subtitle="{{ $student->full_name }} · {{ $student->admission_number }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.students.show', $student) }}" variant="secondary" icon="eye">View Profile</x-button>
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

    <form method="POST" action="{{ route('admin.students.update', $student) }}">
        @csrf
        @method('PUT')

        <x-card title="Personal Information" icon="user">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="first_name" label="First Name" required :value="$student->first_name" />
                <x-input name="middle_name" label="Middle Name" :value="$student->middle_name" />
                <x-input name="last_name" label="Last Name" required :value="$student->last_name" />
                <x-input name="admission_number" label="Admission Number" required :value="$student->admission_number" />
                <x-input name="date_of_birth" label="Date of Birth" type="date" required :value="$student->date_of_birth?->format('Y-m-d')" />
                <div>
                    <x-select name="gender" label="Gender" required :options="['male' => 'Male', 'female' => 'Female']" :selected="$student->gender" />
                </div>
                <x-input name="phone" label="Phone" leadingIcon="phone" :value="$student->phone" />
                <x-input name="email" label="Email" type="email" leadingIcon="mail" :value="$student->email" />
            </div>
        </x-card>

        <x-card title="Enrolment" icon="graduation-cap" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-select name="level" label="Level" required :options="['O-Level' => 'O-Level', 'A-Level' => 'A-Level']" :selected="$student->level" />
                </div>
                <div>
                    <x-select name="school_class_id" label="Class" required :options="$classes" :selected="$student->school_class_id" />
                </div>
                <div>
                    <x-select name="parent_id" label="Parent / Guardian" :options="$parents" :selected="$student->parent_id" placeholder="Optional" />
                </div>
                <div>
                    <x-select name="status" label="Status" required :options="['active' => 'Active', 'inactive' => 'Inactive', 'graduated' => 'Graduated', 'transferred' => 'Transferred']" :selected="$student->status" />
                </div>
                <x-input name="enrolled_on" label="Enrolled On" type="date" :value="$student->enrolled_on?->format('Y-m-d')" />
            </div>
        </x-card>

        @if ($subjects->count())
            <x-card title="Subjects" icon="book-open" class="mt-6">
                <p class="text-sm text-navy-500 dark:text-navy-400 mb-4">
                    Subjects for {{ $academicYear?->name ?? 'the current academic year' }}.
                </p>
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

        <x-card title="Login Account" icon="key" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="password" label="New Password" type="password" placeholder="Leave blank to keep current" />
                <x-input name="password_confirmation" label="Confirm Password" type="password" placeholder="Repeat new password" />
                @if ($student->user)
                    <div>
                        <x-label>Username</x-label>
                        <p class="mt-1.5 text-sm text-navy-700 dark:text-navy-200 font-medium">{{ $student->user->username }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.students.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="save">Save Changes</x-button>
        </div>
    </form>
@endsection