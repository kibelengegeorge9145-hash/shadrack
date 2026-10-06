@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <x-page-header title="Admin Dashboard" subtitle="Overview of Ebenezer Secondary School" />

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card label="Students" :value="$totalStudents" icon="graduation-cap" color="navy" sub="Active students" />
        <x-stat-card label="Teachers" :value="$totalTeachers" icon="users" color="gold" sub="All teaching staff" />
        <x-stat-card label="Parents" :value="$totalParents" icon="users" color="blue" sub="Registered guardians" />
        <x-stat-card label="Classes" :value="$totalClasses" icon="building" color="green" sub="Active classes" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-2">
            <x-card title="Students by Class" icon="bar-chart" :padding="false">
                @forelse ($classesWithCounts as $class)
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-navy-50 dark:border-navy-700/60 last:border-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-navy-800/5 dark:bg-gold-400/10 text-navy-800 dark:text-gold-300 shrink-0">
                                <x-icon name="building" class="w-4 h-4" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $class->name }}</p>
                                <p class="text-xs text-navy-500 dark:text-navy-400">{{ $class->level }} · {{ $class->academic_year }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-sm font-bold text-navy-900 dark:text-navy-100">{{ $class->students_count }}</span>
                            <x-badge color="{{ $class->students_count > 0 ? 'green' : 'gray' }}">
                                {{ $class->students_count > 0 ? 'Active' : 'Empty' }}
                            </x-badge>
                        </div>
                    </div>
                @empty
                    <div class="p-5">
                        <x-empty-state icon="building" title="No classes yet" message="Create a class to start tracking students." />
                    </div>
                @endforelse
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card title="Recent Announcements" icon="megaphone" :padding="false">
                @forelse ($recentAnnouncements as $announcement)
                    <div class="px-5 py-3.5 border-b border-navy-50 dark:border-navy-700/60 last:border-0">
                        <p class="text-sm font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $announcement->title }}</p>
                        <p class="mt-0.5 text-xs text-navy-500 dark:text-navy-400 capitalize">
                            {{ ucfirst($announcement->audience) }} · {{ $announcement->published_at?->format('M d, Y') }}
                        </p>
                    </div>
                @empty
                    <div class="p-5">
                        <x-empty-state icon="megaphone" title="No announcements" message="Recent announcements will appear here." :padding="false" />
                    </div>
                @endforelse
            </x-card>

            <x-card title="Recent Results" icon="trophy" :padding="false">
                @forelse ($recentResults as $result)
                    <div class="px-5 py-3.5 border-b border-navy-50 dark:border-navy-700/60 last:border-0">
                        <p class="text-sm font-semibold text-navy-900 dark:text-navy-50 truncate">{{ $result->student?->full_name }}</p>
                        <p class="mt-0.5 text-xs text-navy-500 dark:text-navy-400">
                            {{ $result->schoolClass?->name }} · {{ $result->term?->name }}
                            <span class="ml-1 text-navy-700 dark:text-navy-200 font-medium">{{ $result->average }}</span>
                        </p>
                    </div>
                @empty
                    <div class="p-5">
                        <x-empty-state icon="trophy" title="No results yet" message="Published results will appear here." />
                    </div>
                @endforelse
            </x-card>
        </div>
    </div>
@endsection