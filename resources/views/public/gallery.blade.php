@extends('layouts.guest')

@section('title', 'Gallery')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">Gallery</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">A glimpse into life at Ebenezer Secondary School.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            @if ($photos->isEmpty())
                <div class="text-center py-16">
                    <x-icon name="image" class="w-12 h-12 text-navy-300 mx-auto mb-4" />
                    <h3 class="font-display text-xl font-bold text-navy-800 dark:text-navy-100">No Photos Yet</h3>
                    <p class="text-navy-500 mt-2">Gallery images will appear here once uploaded.</p>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($photos as $photo)
                        <div class="group relative rounded-xl overflow-hidden bg-navy-100 dark:bg-navy-800 aspect-square">
                            @if ($photo->image)
                                <img src="{{ $photo->image }}" alt="{{ $photo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <x-icon name="image" class="w-10 h-10 text-navy-300" />
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-950/80 to-transparent p-4 pt-12">
                                <p class="font-medium text-white text-sm">{{ $photo->title }}</p>
                                @if ($photo->caption)
                                    <p class="text-xs text-navy-300 mt-0.5">{{ $photo->caption }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-10">{{ $photos->links() }}</div>
            @endif
        </div>
    </section>
@endsection
