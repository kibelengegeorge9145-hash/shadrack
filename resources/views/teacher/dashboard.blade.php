@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
    <x-page-header title="Teacher Dashboard" subtitle="Welcome back, {{ auth()->user()->full_name }}." />

    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
        <x-stat-card label="My Classes" :value="$classes->count()" icon="building" color="navy" :sub="$studentCount . ' students across my classes'" />
        <x-stat-card label="Subjects Taught" :value="$subjectCount" icon="book-open" color="gold" sub="Subjects I currently teach" />
        <x-stat-card label="Total Students" :value="$studentCount" icon="users" color="blue" sub="Across all assigned classes" />
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <x-card class="lg:col-span-2">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-display text-lg font-bold text-navy-900 dark:text-navy-50">My Classes</h3>
                    <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5">Classes you are assigned to this year.</p>
                </div>
                <x-button href="{{ route('teacher.classes.index') }}" size="sm" variant="secondary" icon="chevron-right">View All</x-button>
            </div>

            <div class="space-y-3">
                @forelse ($classes as $class)
                    <div class="flex items-center justify-between gap-3 rounded-lg border border-navy-100 dark:border-navy-700/60 px-4 py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-navy-800/5 dark:bg-gold-400/10 text-navy-800 dark:text-gold-300 shrink-0">
                                <x-icon name="building" class="w-5 h-5" />
                            </span>
                            <div class="min-w-0">
                                <p class="font-semibold text-navy-900 dark:text-navy-100">{{ $class->name }}</p>
                                <p class="text-xs text-navy-500 dark:text-navy-400">{{ $class->students_count }} student(s)</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <x-badge color="navy">{{ $class->level }}</x-badge>
                            <x-button href="{{ route('teacher.results.index', ['school_class_id' => $class->id]) }}" size="sm" variant="ghost" icon="file-text">Results</x-button>
                            <x-button href="{{ route('teacher.attendance.index', ['school_class_id' => $class->id]) }}" size="sm" variant="ghost" icon="check-square">Attendance</x-button>
                        </div>
                    </div>
                @empty
                    <x-empty-state icon="building" title="No classes assigned" message="Talk to an administrator to get assigned to a class." />
                @endforelse
            </div>
        </x-card>

        <x-card :icon="'megaphone'" title="Announcements">
            <div class="space-y-4">
                @forelse ($announcements as $announcement)
                    <div class="rounded-lg bg-cream-100 dark:bg-navy-800 px-4 py-3">
                        <p class="font-semibold text-sm text-navy-900 dark:text-navy-100">{{ $announcement->title }}</p>
                        <p class="text-xs text-navy-500 dark:text-navy-400 mt-0.5 line-clamp-2">{{ $announcement->body }}</p>
                        <p class="text-[11px] text-navy-400 mt-1">{{ $announcement->published_at?->format('d M Y') }}</p>
                    </div>
                @empty
                    <x-empty-state icon="bell" title="No announcements" message="There are no active announcements right now." />
                @endforelse
            </div>
        </x-card>
    </div>
@endsection