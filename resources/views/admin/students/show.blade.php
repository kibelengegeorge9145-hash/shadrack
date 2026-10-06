@extends('layouts.app')

@section('title', $student->full_name)

@section('content')
    <x-page-header title="Student Profile" subtitle="{{ $student->admission_number }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.students.edit', $student) }}" variant="secondary" icon="edit">Edit</x-button>
            <x-button href="{{ route('admin.students.index') }}" variant="secondary" icon="chevron-left">Back to Students</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <x-card>
                <div class="flex flex-col items-center text-center">
                    <span class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-navy-800 text-gold-300 text-2xl font-bold">{{ $student->initials }}</span>
                    <h2 class="mt-4 font-display text-xl font-bold text-navy-900 dark:text-navy-50">{{ $student->full_name }}</h2>
                    <p class="mt-1 text-sm text-navy-500 dark:text-navy-400">{{ $student->admission_number }}</p>
                    <div class="mt-3">
                        @php
                            $statusColor = match ($student->status) {
                                'active' => 'green',
                                'inactive' => 'gray',
                                'graduated' => 'blue',
                                'transferred' => 'amber',
                                default => 'navy',
                            };
                        @endphp
                        <x-badge color="{{ $statusColor }}">{{ ucfirst($student->status) }}</x-badge>
                    </div>
                </div>
                <dl class="mt-6 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Level</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50">{{ $student->level }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Class</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50">{{ $student->schoolClass?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Gender</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50">{{ $student->gender ? ucfirst($student->gender) : '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Date of Birth</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50">{{ $student->date_of_birth?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-navy-500 dark:text-navy-400">Enrolled On</dt>
                        <dd class="font-medium text-navy-900 dark:text-navy-50">{{ $student->enrolled_on?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Contact" icon="phone">
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <x-icon name="phone" class="w-4 h-4 text-navy-400" />
                        <span class="text-navy-700 dark:text-navy-200">{{ $student->phone ?? '—' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-icon name="mail" class="w-4 h-4 text-navy-400" />
                        <span class="text-navy-700 dark:text-navy-200 break-all">{{ $student->email ?? '—' }}</span>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <x-card title="Subjects" icon="book-open" :padding="false">
                @if ($student->subjects->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-5">
                        @foreach ($student->subjects as $subject)
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
                        <x-empty-state icon="book-open" title="No subjects assigned" message="Assign subjects to this student to track their learning." />
                    </div>
                @endif
            </x-card>

            <x-card title="Parent / Guardian" icon="users">
                @if ($student->parent)
                    <div class="flex items-center gap-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gold-400/15 text-gold-600 dark:text-gold-300 font-bold">{{ $student->parent->initials }}</span>
                        <div class="min-w-0">
                            <p class="font-semibold text-navy-900 dark:text-navy-50">{{ $student->parent->full_name }}</p>
                            <p class="text-sm text-navy-500 dark:text-navy-400 capitalize">{{ $student->parent->relationship }} · {{ $student->parent->phone ?? '—' }}</p>
                        </div>
                    </div>
                @else
                    <x-empty-state icon="users" title="No parent linked" message="Link a parent or guardian to this student." />
                @endif
            </x-card>

            <x-card title="Academic Results" icon="trophy" :padding="false">
                @forelse ($student->results as $result)
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-navy-50 dark:border-navy-700/60 last:border-0">
                        <div>
                            <p class="text-sm font-semibold text-navy-900 dark:text-navy-50">{{ $result->term?->name }} — {{ $result->academicYear?->name }}</p>
                            <p class="text-xs text-navy-500 dark:text-navy-400">
                                Average {{ $result->average }} · Points {{ $result->total_points }} · Position {{ $result->position }}
                            </p>
                        </div>
                        <x-badge color="{{ $result->status === 'published' ? 'green' : 'amber' }}">
                            {{ ucfirst($result->status) }}
                        </x-badge>
                    </div>
                @empty
                    <div class="p-5">
                        <x-empty-state icon="trophy" title="No results yet" message="Results will appear here once published." />
                    </div>
                @endforelse
            </x-card>

            <div class="flex justify-end">
                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" data-confirm="Are you sure you want to remove this student?">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" icon="trash">Remove Student</x-button>
                </form>
            </div>
        </div>
    </div>
@endsection