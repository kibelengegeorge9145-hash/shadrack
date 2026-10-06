@extends('layouts.app')

@section('title', 'Subscribers')

@section('content')
    <x-page-header title="Subscribers" subtitle="Newsletter subscriber management">
    </x-page-header>

    <x-alert flash />

    <div class="flex items-center gap-2 mb-4">
        @php $status = request('status'); @endphp
        <a href="{{ route('admin.subscribers.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ !$status ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">All</a>
        <a href="{{ route('admin.subscribers.index', ['status' => 'active']) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $status === 'active' ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">Active</a>
        <a href="{{ route('admin.subscribers.index', ['status' => 'unsubscribed']) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $status === 'unsubscribed' ? 'bg-navy-950 text-white' : 'bg-navy-100 text-navy-700 hover:bg-navy-200' }}">Unsubscribed</a>
    </div>

    <x-card icon="users">
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Subscribed</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($subscribers as $subscriber)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                            <td class="px-4 py-3.5 font-medium text-navy-900 dark:text-navy-100">{{ $subscriber->email }}</td>
                            <td class="px-4 py-3.5">
                                @if ($subscriber->is_active)
                                    <x-badge color="green">Active</x-badge>
                                @else
                                    <x-badge color="gray">Unsubscribed</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-navy-500">{{ $subscriber->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <form method="POST" action="{{ route('admin.subscribers.destroy', $subscriber) }}" onsubmit="return confirm('Remove this subscriber?')">
                                    @csrf @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-navy-500">No subscribers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $subscribers->links() }}</div>
    </x-card>
@endsection