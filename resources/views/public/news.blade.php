@extends('layouts.guest')

@section('title', 'News')

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-white tracking-tight">News</h1>
            <p class="mt-4 text-navy-300 max-w-2xl mx-auto text-lg">Stay informed about the latest happenings at Ebenezer Secondary School.</p>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            @if ($news->isEmpty())
                <div class="text-center py-16">
                    <x-icon name="newspaper" class="w-12 h-12 text-navy-300 mx-auto mb-4" />
                    <h3 class="font-display text-xl font-bold text-navy-800 dark:text-navy-100">No News Yet</h3>
                    <p class="text-navy-500 mt-2">Check back soon for the latest updates.</p>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($news as $article)
                        <a href="{{ route('news.show', $article) }}" class="block bg-white dark:bg-navy-800 rounded-xl border border-navy-100 dark:border-navy-700 overflow-hidden shadow-card hover:shadow-card-hover hover:border-gold-400 transition">
                            <div class="aspect-video bg-gradient-to-br from-navy-100 to-navy-200 dark:from-navy-700 dark:to-navy-800 flex items-center justify-center">
                                @if ($article->image)
                                    <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-full h-full object-cover" />
                                @else
                                    <x-icon name="newspaper" class="w-10 h-10 text-navy-300 dark:text-navy-500" />
                                @endif
                            </div>
                            <div class="p-5">
                                <p class="text-xs text-navy-400 mb-2">{{ $article->published_at?->format('d M Y') }}</p>
                                <h3 class="font-display font-bold text-navy-900 dark:text-white">{{ $article->title }}</h3>
                                <p class="mt-2 text-sm text-navy-500 dark:text-navy-400 line-clamp-3">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->body), 120) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-10">{{ $news->links() }}</div>
            @endif
        </div>
    </section>
@endsection
