@extends('layouts.app')

@section('title', $teacher->full_name)

@section('content')
    <x-page-header title="Teacher Profile" subtitle="{{ $teacher->staff_id }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.teachers.edit', $teacher) }}" variant="secondary" icon="edit">Edit</x-button>
            <x-button href="{{ route('admin.teachers.index') }}" variant="secondary" icon="chevron-left">Back to Teachers</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <x-card>
                <div class="flex flex-col items-center text-center">
                    <span class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-navy-800 text-gold-300 text-2xl font-bold">{{ $teacher->initials }}</span>
                    <h2 class="mt-4 font-display text-xl font-bold text-navy-900 dark:text-navy-50">{{ $teacher->full_name }}</h2>
                    <p class="mt-1 text-sm text-navy-500 dark:text-navy-400">Staff ID: {{ $teacher->staff_id }}</p>
                    <div class="mt-3">
                        <x-badge color="{{ $teacher->is_active ? 'green' : 'gray' }}">
                            {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                        </x-badge>
                    </div>
                </div>
                <dl class="mt-6 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Gender</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50">{{ $teacher->gender ? ucfirst($teacher->gender) : '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Department</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50">{{ $teacher->department ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Qualification</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50 text-right">{{ $teacher->qualification ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Date of Birth</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50">{{ $teacher->date_of_birth?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Contact" icon="phone">
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <x-icon name="phone" class="w-4 h-4 text-navy-400" />
                        <span class="text-navy-700 dark:text-navy-200">{{ $teacher->phone ?? '—' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-icon name="mail" class="w-4 h-4 text-navy-400" />
                        <span class="text-navy-700 dark:text-navy-200 break-all">{{ $teacher->email ?? '—' }}</span>
                    </div>
                    @if ($teacher->user)
                        <div class="flex items-center gap-3">
                            <x-icon name="user" class="w-4 h-4 text-navy-400" />
                            <span class="text-navy-700 dark:text-navy-200">{{ $teacher->user->username }}</span>
                        </div>
                    @endif
                </dl>
            </x-card>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <x-card title="Subjects Taught" icon="book-open" :padding="false">
                @if ($teacher->subjects->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-5">
                        @foreach ($teacher->subjects as $subject)
                            <div class="flex items-center gap-3 rounded-lg border border-navy-100 dark:border-navy-700 px-4 py-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-navy-800/5 dark:bg-gold-400/10 text-navy-800 dark:text-gold-300 shrink-0">
                                    <x-icon name="book-open" class="w-4 h-4" />
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $subject->name }}</p>
                                    <p class="text-xs text-navy-500 dark:text-navy-400">{{ $subject->code }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-5">
                        <x-empty-state icon="book-open" title="No subjects assigned" message="Assign subjects to this teacher to plan their workload." />
                    </div>
                @endif
            </x-card>

            <x-card title="Classes Taught" icon="users" :padding="false">
                @if ($teacher->classes->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-5">
                        @foreach ($teacher->classes as $class)
                            <div class="flex items-center gap-3 rounded-lg border border-navy-100 dark:border-navy-700 px-4 py-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-navy-800/5 dark:bg-gold-400/10 text-navy-800 dark:text-gold-300 shrink-0">
                                    <x-icon name="users" class="w-4 h-4" />
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $class->name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-5">
                        <x-empty-state icon="users" title="No classes assigned" message="Assign classes to this teacher." />
                    </div>
                @endif
            </x-card>

            <div class="flex justify-end">
                <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" data-confirm="Are you sure you want to remove this teacher?">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" icon="trash">Remove Teacher</x-button>
                </form>
            </div>
        </div>
    </div>
@endsection
