@extends('layouts.app')

@section('title', 'Inquiry #' . $inquiry->id)

@section('content')
    <x-page-header title="Inquiry #{{ $inquiry->id }}" subtitle="{{ $inquiry->subject ?? 'No subject' }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.inquiries.index') }}" variant="ghost" icon="arrow-left">Back</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-card icon="message-square">
                <div class="prose prose-sm prose-navy dark:prose-invert max-w-none">
                    <p class="whitespace-pre-line">{!! nl2br(e($inquiry->message)) !!}</p>
                </div>
            </x-card>
        </div>
        <div>
            <x-card title="Details" icon="info">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-navy-500 text-xs uppercase tracking-wider">From</dt>
                        <dd class="mt-1 font-medium text-navy-900 dark:text-navy-100">{{ $inquiry->name }}</dd>
                        <dd class="text-navy-500">{{ $inquiry->email }}</dd>
                        @if ($inquiry->phone)
                            <dd class="text-navy-500">{{ $inquiry->phone }}</dd>
                        @endif
                    </div>
                    <div>
                        <dt class="text-navy-500 text-xs uppercase tracking-wider">IP Address</dt>
                        <dd class="mt-1 font-mono text-navy-700 dark:text-navy-300">{{ $inquiry->ip_address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-navy-500 text-xs uppercase tracking-wider">Received</dt>
                        <dd class="mt-1 text-navy-700 dark:text-navy-300">{{ $inquiry->created_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </x-card>
            <div class="mt-4">
                <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Delete this inquiry?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" icon="trash" class="w-full">Delete Inquiry</x-button>
                </form>
            </div>
        </div>
    </div>
@endsection