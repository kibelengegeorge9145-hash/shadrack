@extends('layouts.app')

@section('title', 'Edit Subject')

@section('content')
    <x-page-header title="Edit Subject" subtitle="{{ $subject->name }} · {{ $subject->code }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.subjects.index') }}" variant="secondary" icon="chevron-left">Back to Subjects</x-button>
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

    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
        @csrf
        @method('PUT')

        <x-card title="Subject Details" icon="book-open">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="code" label="Code" required :value="$subject->code" />
                <x-input name="name" label="Subject Name" required :value="$subject->name" />
                <div>
                    <x-select name="level" label="Level" required :options="$levelOptions" :selected="$subject->level" />
                </div>
                <x-input name="department" label="Department" :value="$subject->department" />
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_optional" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" {{ $subject->is_optional ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-navy-800 dark:text-navy-100">Optional subject</span>
                    </label>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" {{ $subject->is_active ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-navy-800 dark:text-navy-100">Active subject</span>
                    </label>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.subjects.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="save">Save Changes</x-button>
        </div>
    </form>
@endsection