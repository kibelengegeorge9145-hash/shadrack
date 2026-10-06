@extends('layouts.app')

@section('title', 'Add Subject')

@section('content')
    <x-page-header title="Add Subject" subtitle="Add a new subject to the catalog">
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

    <form method="POST" action="{{ route('admin.subjects.store') }}">
        @csrf

        <x-card title="Subject Details" icon="book-open">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="code" label="Code" required placeholder="MAT" />
                <x-input name="name" label="Subject Name" required placeholder="Mathematics" />
                <div>
                    <x-select name="level" label="Level" required :options="$levelOptions" placeholder="Select level" />
                </div>
                <x-input name="department" label="Department" placeholder="Science & Mathematics" />
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_optional" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40">
                        <span class="text-sm font-medium text-navy-800 dark:text-navy-100">Optional subject</span>
                    </label>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" checked>
                        <span class="text-sm font-medium text-navy-800 dark:text-navy-100">Active subject</span>
                    </label>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.subjects.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="check">Create Subject</x-button>
        </div>
    </form>
@endsection