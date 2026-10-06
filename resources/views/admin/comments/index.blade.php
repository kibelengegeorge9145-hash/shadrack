@extends('layouts.app')

@section('title', 'Comment Moderation')

@section('content')
    <x-page-header title="Comment Moderation" subtitle="Review and approve public comments">
    </x-page-header>

    <x-alert flash />

    <div class="flex items-center gap-2 mb-4">
        @php $status = request('status'); @endphp
        <a href="{{ route('admin.comments.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ !$status ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">All</a>
        <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $status === 'pending' ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">Pending</a>
        <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $status === 'approved' ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">Approved</a>
    </div>

    <x-card icon="message-square">
        <div class="space-y-3">
            @forelse ($comments as $comment)
                <div class="p-4 rounded-lg border {{ $comment->is_approved ? 'border-navy-100 dark:border-navy-700' : 'border-gold-400 bg-gold-50/30 dark:bg-gold-900/10' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-navy-900 dark:text-navy-100">{{ $comment->author_name }}</span>
                                @if ($comment->author_email)
                                    <span class="text-xs text-navy-400">({{ $comment->author_email }})</span>
                                @endif
                                @if (!$comment->is_approved)
                                    <x-badge color="yellow">Pending</x-badge>
                                @else
                                    <x-badge color="green">Approved</x-badge>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-navy-600 dark:text-navy-300 whitespace-pre-line">{{ $comment->body }}</p>
                            <p class="mt-2 text-xs text-navy-400">
                                On <span class="font-medium">{{ class_basename($comment->commentable_type) }}</span> #{{ $comment->commentable_id }}
                                · {{ $comment->created_at->diffForHumans() }}
                                @if ($comment->ip_address)
                                    · IP {{ $comment->ip_address }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            @if (!$comment->is_approved)
                                <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                                    @csrf @method('PATCH')
                                    <x-button type="submit" variant="ghost" size="sm" icon="check" />
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?')">
                                @csrf @method('DELETE')
                                <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-navy-500 text-center py-8">No comments to moderate.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $comments->links() }}</div>
    </x-card>
@endsection