@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <x-page-header title="User Management" subtitle="Reset passwords for staff, parents and students." />

    <x-alert flash class="mb-6" />

    @if (session('temp_password'))
        <div class="rounded-xl border-2 border-amber-300 dark:border-amber-600 bg-amber-50 dark:bg-amber-900/30 p-6 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <x-icon name="alert" class="w-5 h-5 text-amber-600 dark:text-amber-300" />
                <p class="font-semibold text-amber-900 dark:text-amber-100">Temporary Password Issued for {{ session('temp_password.user') }}</p>
            </div>
            <p class="text-sm text-amber-800 dark:text-amber-200 mb-2">Give this password to the user. They will be forced to set a new one immediately after their next sign-in.</p>
            <div class="inline-flex items-center gap-3 bg-white dark:bg-navy-900 rounded-lg border border-amber-200 dark:border-amber-700 px-4 py-2.5">
                <code class="font-mono text-lg font-bold tracking-wide text-navy-900 dark:text-white">{{ session('temp_password.password') }}</code>
            </div>
        </div>
    @endif

    <x-card icon="users">
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-full text-sm">
                <thead>
                    <tr class="border-b border-navy-100 dark:border-navy-700">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">User</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Role</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Contact</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Status</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                    @forelse ($users as $user)
                        <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                            <td class="px-4 py-3.5">
                                <p class="font-medium text-navy-900 dark:text-navy-100">{{ $user->full_name }}</p>
                                <p class="text-xs text-navy-400">{{ $user->username }}</p>
                            </td>
                            <td class="px-4 py-3.5">
                                <x-badge :color="$user->role === 'admin' ? 'gold' : 'gray'">{{ ucfirst($user->role) }}</x-badge>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-navy-700 dark:text-navy-200">{{ $user->email }}</p>
                                @if ($user->phone)
                                    <p class="text-xs text-navy-400">{{ $user->phone }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <x-badge :color="$user->is_active ? 'green' : 'red'">{{ $user->is_active ? 'Active' : 'Inactive' }}</x-badge>
                                @if ($user->must_change_password)
                                    <span class="block mt-1"><x-badge color="amber">Temp password pending</x-badge></span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}"
                                    onsubmit="return confirm('Reset the password for {{ $user->full_name }}?\nA temporary password will be generated and the user will be forced to change it on next login.')">
                                    @csrf
                                    <x-button type="submit" variant="ghost" size="sm" icon="lock" class="text-gold-600">
                                        Reset Password
                                    </x-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-navy-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pt-5">{{ $users->links() }}</div>
    </x-card>
@endsection