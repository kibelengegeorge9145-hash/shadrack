@extends('layouts.app')

@section('title', 'Edit Article')

@section('content')
    <x-page-header title="Edit Article" subtitle="{{ $news->title }}" />

    <x-alert flash />

    <div class="max-w-3xl">
        <x-card icon="newspaper">
            <form method="POST" action="{{ route('admin.news.update', $news) }}" class="space-y-6">
                @csrf @method('PUT')
                <x-input name="title" label="Title" :value="$news->title" required />
                <div class="text-sm text-navy-500">Slug: <span class="font-mono text-navy-700 dark:text-navy-300">/{{ $news->slug }}</span></div>
                <x-textarea name="excerpt" label="Excerpt" rows="2">{{ $news->excerpt }}</x-textarea>
                <x-textarea name="body" label="Body" required rows="10">{{ $news->body }}</x-textarea>
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-input name="image" label="Image URL (Optional)" :value="$news->image" placeholder="https://..." />
                    <x-input type="datetime-local" name="published_at" label="Publish Date" :value="$news->published_at?->format('Y-m-d\TH:i')" />
                </div>
                <label class="flex items-center gap-3 text-sm text-navy-700 dark:text-navy-200">
                    <input type="checkbox" name="is_published" value="1" {{ $news->is_published ? 'checked' : '' }} class="rounded border-navy-300 text-navy-800 focus:ring-gold-400" />
                    Published
                </label>
                <div class="flex justify-end gap-3 pt-2">
                    <x-button href="{{ route('admin.news.index') }}" variant="secondary">Cancel</x-button>
                    <x-button type="submit" variant="primary" icon="save">Update Article</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
