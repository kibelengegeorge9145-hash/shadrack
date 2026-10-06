@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
    <x-page-header title="Edit Event" subtitle="{{ $event->title }}" />

    <x-alert flash />

    <div class="max-w-3xl">
        <x-card icon="calendar-check">
            <form method="POST" action="{{ route('admin.events.update', $event) }}" class="space-y-6">
                @csrf @method('PUT')
                <x-input name="title" label="Title" :value="$event->title" required />
                <x-textarea name="description" label="Description" rows="4">{{ $event->description }}</x-textarea>
                <x-input name="location" label="Location" :value="$event->location" />
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-input type="datetime-local" name="starts_at" label="Starts At" :value="$event->starts_at?->format('Y-m-d\TH:i')" required />
                    <x-input type="datetime-local" name="ends_at" label="Ends At" :value="$event->ends_at?->format('Y-m-d\TH:i')" />
                </div>
                <x-input name="image" label="Image URL (Optional)" :value="$event->image" placeholder="https://..." />
                <label class="flex items-center gap-3 text-sm text-navy-700 dark:text-navy-200">
                    <input type="checkbox" name="is_published" value="1" {{ $event->is_published ? 'checked' : '' }} class="rounded border-navy-300 text-navy-800 focus:ring-gold-400" />
                    Published
                </label>
                <div class="flex justify-end gap-3 pt-2">
                    <x-button href="{{ route('admin.events.index') }}" variant="secondary">Cancel</x-button>
                    <x-button type="submit" variant="primary" icon="save">Update Event</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
