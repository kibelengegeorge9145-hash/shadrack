@extends('layouts.app')

@section('title', 'News')

@section('content')
    <x-page-header title="News" subtitle="Manage published news articles">
        <x-slot:actions>
            <x-button href="{{ route('admin.news.create') }}" variant="gold" icon="plus">New Article</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-alert flash />

    <x-card icon="newspaper">
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Title</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Author</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Published</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($articles as $article)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                            <td class="px-4 py-3.5">
                                <p class="font-medium text-navy-900 dark:text-navy-100">{{ $article->title }}</p>
                                <p class="text-xs text-navy-500 mt-0.5 font-mono">/{{ $article->slug }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $article->author?->full_name ?? '—' }}</td>
                            <td class="px-4 py-3.5">
                                @if ($article->is_published)
                                    <x-badge color="green">Published</x-badge>
                                @else
                                    <x-badge color="gray">Draft</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-navy-500">{{ $article->published_at?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('admin.news.edit', $article) }}" variant="ghost" size="sm" icon="edit" />
                                    <form method="POST" action="{{ route('admin.news.destroy', $article) }}" onsubmit="return confirm('Delete this article?')">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-navy-500">No news articles yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $articles->links() }}</div>
    </x-card>
@endsection
