@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
    <x-page-header title="My Attendance" subtitle="{{ $academicYear?->name ?? 'Current Academic Year' }}" />

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat-card label="Present" value="{{ $summary['present'] }}" icon="check-circle" color="green" />
        <x-stat-card label="Absent" value="{{ $summary['absent'] }}" icon="x-circle" color="red" />
        <x-stat-card label="Late" value="{{ $summary['late'] }}" icon="clock" color="amber" />
        <x-stat-card label="Excused" value="{{ $summary['excused'] }}" icon="info" color="blue" />
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <x-card title="Attendance Rate" icon="calendar-check">
            <div class="flex flex-col items-center py-6">
                <div class="relative w-32 h-32">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke-width="12" class="stroke-navy-100 dark:stroke-navy-700" />
                        <circle cx="60" cy="60" r="52" fill="none" stroke-width="12" stroke-linecap="round"
                            class="stroke-emerald-500" stroke-dasharray="{{ 2 * pi() * 52 }}"
                            stroke-dashoffset="{{ 2 * pi() * 52 * (1 - $summary['percentage'] / 100) }}" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="font-display text-2xl font-bold text-navy-900 dark:text-navy-100">{{ $summary['percentage'] }}%</span>
                    </div>
                </div>
                <p class="mt-4 text-sm text-navy-500">Overall Attendance Rate</p>
            </div>
        </x-card>

        <x-card title="Summary" icon="bar-chart">
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-navy-600 dark:text-navy-300">Present</span>
                        <span class="font-medium text-navy-900 dark:text-navy-100">{{ $summary['present'] }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-navy-100 dark:bg-navy-700 overflow-hidden">
                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $summary['present'] + $summary['absent'] + $summary['late'] + $summary['excused'] > 0 ? ($summary['present'] / ($summary['present'] + $summary['absent'] + $summary['late'] + $summary['excused'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-navy-600 dark:text-navy-300">Absent</span>
                        <span class="font-medium text-navy-900 dark:text-navy-100">{{ $summary['absent'] }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-navy-100 dark:bg-navy-700 overflow-hidden">
                        <div class="h-full rounded-full bg-red-500" style="width: {{ $summary['present'] + $summary['absent'] + $summary['late'] + $summary['excused'] > 0 ? ($summary['absent'] / ($summary['present'] + $summary['absent'] + $summary['late'] + $summary['excused'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-navy-600 dark:text-navy-300">Late</span>
                        <span class="font-medium text-navy-900 dark:text-navy-100">{{ $summary['late'] }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-navy-100 dark:bg-navy-700 overflow-hidden">
                        <div class="h-full rounded-full bg-amber-500" style="width: {{ $summary['present'] + $summary['absent'] + $summary['late'] + $summary['excused'] > 0 ? ($summary['late'] / ($summary['present'] + $summary['absent'] + $summary['late'] + $summary['excused'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-navy-600 dark:text-navy-300">Excused</span>
                        <span class="font-medium text-navy-900 dark:text-navy-100">{{ $summary['excused'] }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-navy-100 dark:bg-navy-700 overflow-hidden">
                        <div class="h-full rounded-full bg-blue-500" style="width: {{ $summary['present'] + $summary['absent'] + $summary['late'] + $summary['excused'] > 0 ? ($summary['excused'] / ($summary['present'] + $summary['absent'] + $summary['late'] + $summary['excused'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
@endsection
