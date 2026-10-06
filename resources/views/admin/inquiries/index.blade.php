@extends('layouts.app')

@section('title', 'Inquiries')

@section('content')
    <x-page-header title="Inquiries" subtitle="Messages received through the contact form">
        <x-slot:actions>
            <x-button href="{{ route('admin.inquiries.index', array_filter(['status' => request('status')])) }}" variant="ghost" icon="refresh">Refresh</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-alert flash />

    <div class="flex items-center gap-2 mb-4">
        @php $status = request('status'); @endphp
        <a href="{{ route('admin.inquiries.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ !$status ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">All</a>
        <a href="{{ route('admin.inquiries.index', ['status' => 'unread']) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $status === 'unread' ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">Unread</a>
        <a href="{{ route('admin.inquiries.index', ['status' => 'read']) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $status === 'read' ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">Read</a>
    </div>

    <x-card icon="inbox">
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">From</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Subject</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Received</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($inquiries as $inquiry)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                            <td class="px-4 py-3.5">
                                <p class="font-medium text-navy-900 dark:text-navy-100">{{ $inquiry->name }}</p>
                                <p class="text-xs text-navy-500">{{ $inquiry->email }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $inquiry->subject ?: '—' }}</td>
                            <td class="px-4 py-3.5">
                                @if ($inquiry->is_read)
                                    <x-badge color="gray">Read</x-badge>
                                @else
                                    <x-badge color="blue">New</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-navy-500">{{ $inquiry->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('admin.inquiries.show', $inquiry) }}" variant="ghost" size="sm" icon="eye" />
                                    <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Delete this inquiry?')">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-navy-500">No inquiries yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $inquiries->links() }}</div>
    </x-card>
@endsection