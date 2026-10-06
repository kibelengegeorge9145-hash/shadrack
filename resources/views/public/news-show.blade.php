@extends('layouts.guest')

@section('title', $news->title)

@section('content')
    <section class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 py-16 sm:py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1 text-sm text-navy-300 hover:text-gold-300">
                <x-icon name="arrow-left" class="w-4 h-4" /> Back to News
            </a>
            <h1 class="mt-4 font-display text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ $news->title }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-navy-400">
                <span class="flex items-center gap-1"><x-icon name="calendar" class="w-4 h-4" /> {{ $news->published_at?->format('d M Y') ?? '—' }}</span>
                @if ($news->author)
                    <span class="flex items-center gap-1"><x-icon name="user" class="w-4 h-4" /> {{ $news->author->full_name ?? $news->author->name }}</span>
                @endif
            </div>
        </div>
    </section>

    <section class="py-12 sm:py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            @if ($news->image)
                <img src="{{ $news->image }}" alt="{{ $news->title }}" class="w-full rounded-xl border border-navy-100 dark:border-navy-700 mb-8">
            @endif

            <article class="prose prose-navy dark:prose-invert max-w-none">
                {!! nl2br(e($news->body)) !!}
            </article>

            @if ($news->excerpt)
                <p class="mt-6 text-navy-500 italic">{{ $news->excerpt }}</p>
            @endif
        </div>
    </section>

    <section class="pb-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <x-alert flash />

            <x-card title="Comments ({{ $comments->count() }})" icon="message-square">
                @forelse ($comments as $comment)
                    <div class="py-4 border-b border-navy-100 dark:border-navy-700 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-navy-900 dark:text-navy-100">{{ $comment->author_name }}</span>
                            <span class="text-xs text-navy-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-navy-600 dark:text-navy-300 whitespace-pre-line">{{ $comment->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-navy-500 py-2">No comments yet. Be the first to share your thoughts.</p>
                @endforelse
            </x-card>

            <div class="mt-6">
                <x-card title="Leave a Comment" icon="edit">
                    <form method="POST" action="{{ route('news.comments.store', $news) }}" class="space-y-4">
                        @csrf
                        <div style="position:absolute;left:-9999px" aria-hidden="true">
                            <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <x-input name="author_name" label="Your Name" required />
                            <x-input type="email" name="author_email" label="Email (optional)" />
                        </div>
                        <x-textarea name="body" label="Comment" rows="4" required />
                        <div class="flex justify-end">
                            <x-button type="submit" variant="gold" icon="send">Post Comment</x-button>
                        </div>
                    </form>
                </x-card>
            </div>

            <div class="mt-10">
                <x-card title="Subscribe for Updates" icon="mail">
                    <form method="POST" action="{{ route('subscribe') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                        @csrf
                        <div style="position:absolute;left:-9999px" aria-hidden="true">
                            <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>
                        <div class="flex-1">
                            <x-input type="email" name="email" label="Email address" required />
                        </div>
                        <x-button type="submit" variant="primary" icon="bell">Subscribe</x-button>
                    </form>
                </x-card>
            </div>

            @if ($related->isNotEmpty())
                <div class="mt-10">
                    <h2 class="font-display text-xl font-bold text-navy-900 dark:text-white mb-4">Related News</h2>
                    <div class="space-y-3">
                        @foreach ($related as $item)
                            <a href="{{ route('news.show', $item) }}" class="block bg-white dark:bg-navy-800 rounded-lg border border-navy-100 dark:border-navy-700 p-4 hover:border-gold-400 transition-colors">
                                <p class="font-medium text-navy-900 dark:text-navy-100">{{ $item->title }}</p>
                                <p class="text-xs text-navy-500 mt-1">{{ $item->published_at?->format('d M Y') ?? '—' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection