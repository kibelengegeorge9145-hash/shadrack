@extends('layouts.app')

@section('title', 'Edit Parent')

@section('content')
    <x-page-header title="Edit Parent" subtitle="{{ $parent->full_name }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.parents.index') }}" variant="secondary" icon="chevron-left">Back to Parents</x-button>
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

    <form method="POST" action="{{ route('admin.parents.update', $parent) }}">
        @csrf
        @method('PUT')

        <x-card title="Parent Information" icon="users">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="first_name" label="First Name" required :value="$parent->first_name" />
                <x-input name="middle_name" label="Middle Name" :value="$parent->middle_name" />
                <x-input name="last_name" label="Last Name" required :value="$parent->last_name" />
                <div>
                    <x-select name="relationship" label="Relationship" required :options="['father' => 'Father', 'mother' => 'Mother', 'guardian' => 'Guardian']" :selected="$parent->relationship" />
                </div>
                <x-input name="phone" label="Phone" leadingIcon="phone" :value="$parent->phone" />
                <x-input name="email" label="Email" type="email" leadingIcon="mail" :value="$parent->email" />
                <div class="md:col-span-2 lg:col-span-3">
                    <x-textarea name="address" label="Address" rows="3">{{ $parent->address }}</x-textarea>
                </div>
            </div>
        </x-card>

        @if ($parent->students()->count())
            <x-card title="Linked Children" icon="graduation-cap" class="mt-6" :padding="false">
                @foreach ($parent->students as $child)
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-navy-50 dark:border-navy-700/60 last:border-0">
                        <div>
                            <p class="text-sm font-semibold text-navy-900 dark:text-navy-50">{{ $child->full_name }}</p>
                            <p class="text-xs text-navy-500 dark:text-navy-400">{{ $child->admission_number }} · {{ $child->schoolClass?->name ?? '—' }}</p>
                        </div>
                        <a href="{{ route('admin.students.show', $child) }}" class="text-sm font-semibold text-gold-600 hover:text-gold-500">View student</a>
                    </div>
                @endforeach
            </x-card>
        @endif

        <x-card title="Login Account" icon="key" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input name="password" label="New Password" type="password" placeholder="Leave blank to keep current" />
                <x-input name="password_confirmation" label="Confirm Password" type="password" placeholder="Repeat new password" />
                @if ($parent->user)
                    <div>
                        <x-label>Username</x-label>
                        <p class="mt-1.5 text-sm text-navy-700 dark:text-navy-200 font-medium">{{ $parent->user->username }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.parents.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="save">Save Changes</x-button>
        </div>
    </form>
@endsection