@extends('layouts.app')

@section('title', 'New Event')

@section('content')
    <x-page-header title="New Event" subtitle="Create a new school event" />

    <x-alert flash />

    <div class="max-w-3xl">
        <x-card icon="calendar-check">
            <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-6">
                @csrf
                <x-input name="title" label="Title" required />
                <x-textarea name="description" label="Description" rows="4" />
                <x-input name="location" label="Location" />
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-input type="datetime-local" name="starts_at" label="Starts At" required />
                    <x-input type="datetime-local" name="ends_at" label="Ends At" />
                </div>
                <x-input name="image" label="Image URL (Optional)" placeholder="https://..." />
                <label class="flex items-center gap-3 text-sm text-navy-700 dark:text-navy-200">
                    <input type="checkbox" name="is_published" value="1" checked class="rounded border-navy-300 text-navy-800 focus:ring-gold-400" />
                    Published
                </label>
                <div class="flex justify-end gap-3 pt-2">
                    <x-button href="{{ route('admin.events.index') }}" variant="secondary">Cancel</x-button>
                    <x-button type="submit" variant="gold" icon="plus">Create Event</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
