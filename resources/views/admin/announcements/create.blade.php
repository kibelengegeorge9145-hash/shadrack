@extends('layouts.app')

@section('title', 'New Announcement')

@section('content')
    <x-page-header title="New Announcement" subtitle="Create a new school announcement" />

    <x-alert flash />

    <div class="max-w-3xl">
        <x-card icon="megaphone">
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-6">
                @csrf
                <x-input name="title" label="Title" required />
                <x-textarea name="body" label="Body" required rows="6" />
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-select name="audience" label="Audience" :options="$audiences" placeholder="Select audience" required />
                    <x-select name="school_class_id" label="Specific Class (Optional)" :options="$classes" placeholder="All / Not class-specific" />
                </div>
                <label class="flex items-center gap-3 text-sm text-navy-700 dark:text-navy-200">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-navy-300 text-navy-800 focus:ring-gold-400" />
                    Active
                </label>
                <div class="flex justify-end gap-3 pt-2">
                    <x-button href="{{ route('admin.announcements.index') }}" variant="secondary">Cancel</x-button>
                    <x-button type="submit" variant="gold" icon="plus">Create Announcement</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
