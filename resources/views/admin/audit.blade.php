@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
    <x-page-header title="Audit Logs" subtitle="A trail of actions performed across the system." />

    @if (session('success') || session('error') || session('warning') || session('info'))
        <x-alert flash />
    @endif

    <x-card title="Activity Trail" icon="history">
        <form method="GET" action="{{ route('admin.audit.index') }}" class="mb-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-navy-400">
                    <x-icon name="search" class="w-4 h-4" />
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by action, description, or user..."
                    class="w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 pl-10 py-2.5 focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Search</x-button>
        </form>

        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">User</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Action</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Description</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">IP</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                            <td class="px-4 py-3.5 align-middle">
                                <span class="font-medium text-navy-900 dark:text-navy-100">{{ $log->user?->name ?? 'System' }}</span>
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <x-badge color="navy">{{ $log->action }}</x-badge>
                            </td>
                            <td class="px-4 py-3.5 align-middle text-navy-700 dark:text-navy-200 max-w-md">{{ $log->description }}</td>
                            <td class="px-4 py-3.5 align-middle font-mono text-xs text-navy-500 dark:text-navy-400">{{ $log->ip_address ?? '—' }}</td>
                            <td class="px-4 py-3.5 align-middle text-navy-600 dark:text-navy-300 whitespace-nowrap">{{ $log->created_at?->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-navy-500 dark:text-navy-400">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </x-card>
@endsection
