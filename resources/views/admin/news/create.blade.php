@extends('layouts.app')

@section('title', 'New Article')

@section('content')
    <x-page-header title="New Article" subtitle="Write a new news article" />

    <x-alert flash />

    <div class="max-w-3xl">
        <x-card icon="newspaper">
            <form method="POST" action="{{ route('admin.news.store') }}" class="space-y-6">
                @csrf
                <x-input name="title" label="Title" required />
                <x-textarea name="excerpt" label="Excerpt" rows="2" placeholder="Brief summary of the article..." />
                <x-textarea name="body" label="Body" required rows="10" />
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-input name="image" label="Image URL (Optional)" placeholder="https://..." />
                    <x-input type="datetime-local" name="published_at" label="Publish Date (Optional)" />
                </div>
                <label class="flex items-center gap-3 text-sm text-navy-700 dark:text-navy-200">
                    <input type="checkbox" name="is_published" value="1" class="rounded border-navy-300 text-navy-800 focus:ring-gold-400" />
                    Publish immediately
                </label>
                <div class="flex justify-end gap-3 pt-2">
                    <x-button href="{{ route('admin.news.index') }}" variant="secondary">Cancel</x-button>
                    <x-button type="submit" variant="gold" icon="plus">Create Article</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
