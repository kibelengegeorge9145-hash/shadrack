@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
    <x-page-header title="Edit Announcement" subtitle="{{ $announcement->title }}" />

    <x-alert flash />

    <div class="max-w-3xl">
        <x-card icon="megaphone">
            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="space-y-6">
                @csrf @method('PUT')
                <x-input name="title" label="Title" :value="$announcement->title" required />
                <x-textarea name="body" label="Body" required rows="6">{{ $announcement->body }}</x-textarea>
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-select name="audience" label="Audience" :options="$audiences" selected="{{ $announcement->audience }}" placeholder="Select audience" required />
                    <x-select name="school_class_id" label="Specific Class (Optional)" :options="$classes" selected="{{ $announcement->school_class_id }}" placeholder="All / Not class-specific" />
                </div>
                <label class="flex items-center gap-3 text-sm text-navy-700 dark:text-navy-200">
                    <input type="checkbox" name="is_active" value="1" {{ $announcement->is_active ? 'checked' : '' }} class="rounded border-navy-300 text-navy-800 focus:ring-gold-400" />
                    Active
                </label>
                <div class="flex justify-end gap-3 pt-2">
                    <x-button href="{{ route('admin.announcements.index') }}" variant="secondary">Cancel</x-button>
                    <x-button type="submit" variant="primary" icon="save">Update Announcement</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
