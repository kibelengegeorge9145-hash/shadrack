@extends('layouts.app')

@section('title', 'Admissions')

@section('content')
    <x-page-header title="Admissions" subtitle="Manage student admission applications">
        <x-slot:actions>
            <x-button href="{{ route('admin.admissions.create') }}" variant="gold" icon="plus">New Application</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-alert flash />

    <div class="grid sm:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Applications" value="{{ $admissions->total() }}" icon="inbox" color="navy" />
        <x-stat-card label="Pending" value="{{ $pendingCount }}" icon="clock" color="amber" />
        <x-stat-card label="Accepted" value="{{ \App\Models\Admission::where('status','accepted')->count() }}" icon="check-circle" color="green" />
        <x-stat-card label="Rejected" value="{{ \App\Models\Admission::where('status','rejected')->count() }}" icon="x-circle" color="red" />
    </div>

    <x-card icon="list">
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('admin.admissions.index') }}" class="flex items-center gap-2">
                <x-input name="search" placeholder="Search applications..." value="{{ request('search') }}" leadingIcon="search" />
                <x-select name="status" :options="$statuses" selected="{{ request('status') }}" placeholder="All Statuses" />
                <x-button type="submit" variant="secondary" icon="filter">Filter</x-button>
            </form>
        </div>

        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Application No.</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Student Name</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Level</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Applying Class</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Date</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($admissions as $admission)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                            <td class="px-4 py-3.5 font-mono text-xs font-medium text-navy-900 dark:text-navy-100">{{ $admission->application_number }}</td>
                            <td class="px-4 py-3.5 font-medium text-navy-900 dark:text-navy-100">{{ $admission->full_name }}</td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $admission->level }}</td>
                            <td class="px-4 py-3.5 text-navy-700 dark:text-navy-300">{{ $admission->applying_class }}</td>
                            <td class="px-4 py-3.5">
                                @php
                                    $statusColors = ['pending' => 'amber', 'under_review' => 'blue', 'accepted' => 'green', 'rejected' => 'red'];
                                @endphp
                                <x-badge :color="$statusColors[$admission->status] ?? 'gray'">{{ ucfirst(str_replace('_', ' ', $admission->status)) }}</x-badge>
                            </td>
                            <td class="px-4 py-3.5 text-navy-500">{{ $admission->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('admin.admissions.show', $admission) }}" variant="ghost" size="sm" icon="eye" />
                                    <form method="POST" action="{{ route('admin.admissions.destroy', $admission) }}" onsubmit="return confirm('Delete this application?')">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" icon="trash" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-navy-500">No admission applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $admissions->links() }}
        </div>
    </x-card>
@endsection
