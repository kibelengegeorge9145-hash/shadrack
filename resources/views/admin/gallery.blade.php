@extends('layouts.app')

@section('title', 'Gallery')

@section('content')
    <x-page-header title="Gallery" subtitle="Manage gallery images" />

    <x-alert flash />

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        <x-card title="Add Image" icon="image">
            <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <x-input name="title" label="Title" required />
                <div>
                    <label class="block text-sm font-medium text-navy-700 dark:text-navy-200 mb-1.5">Image File</label>
                    <input type="file" name="image_file" accept="image/*"
                        class="block w-full text-sm text-navy-600 dark:text-navy-300 file:mr-3 file:rounded-lg file:border-0 file:bg-navy-50 dark:file:bg-navy-800 file:px-3 file:py-2 file:text-sm file:font-medium file:text-navy-700 dark:file:text-navy-200 hover:file:bg-navy-100 dark:hover:file:bg-navy-700" />
                    <p class="mt-1 text-xs text-navy-400">Upload a photo (JPG, PNG, WebP — max 8 MB).</p>
                    @error('image_file')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-navy-400 font-medium uppercase tracking-wide">or</span>
                    <div class="flex-1 h-px bg-navy-100 dark:bg-navy-700"></div>
                </div>
                <x-input name="image" label="Image URL" placeholder="https://... (optional if file uploaded)" />
                <x-textarea name="caption" label="Caption" rows="2" />
                <div class="flex justify-end pt-1">
                    <x-button type="submit" variant="gold" icon="plus">Add Image</x-button>
                </div>
            </form>
        </x-card>

        <x-card title="Gallery Images" icon="grid" class="lg:col-span-2">
            @if ($photos->isEmpty())
                <x-empty-state icon="image" title="No Images" message="No gallery images yet." />
            @else
                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach ($photos as $photo)
                        <div class="rounded-lg border border-navy-100 dark:border-navy-700 overflow-hidden">
                            <div class="aspect-video bg-navy-100 dark:bg-navy-800 flex items-center justify-center">
                                @if ($photo->image)
                                    <img src="{{ $photo->image }}" alt="{{ $photo->title }}" class="w-full h-full object-cover" />
                                @else
                                    <x-icon name="image" class="w-8 h-8 text-navy-300" />
                                @endif
                            </div>
                            <div class="p-3 flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="font-medium text-sm text-navy-900 dark:text-navy-100 truncate">{{ $photo->title }}</p>
                                    @if ($photo->caption)
                                        <p class="text-xs text-navy-500 truncate">{{ $photo->caption }}</p>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('admin.gallery.destroy', $photo) }}" onsubmit="return confirm('Delete this image?')">
                                    @csrf @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $photos->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection
