@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    <x-page-header title="My Dashboard" subtitle="Welcome back, {{ $student->full_name }}" />

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat-card label="My Class" value="{{ $student->schoolClass?->name ?? '—' }}" icon="building" color="navy" :sub="$student->level" />
        <x-stat-card label="Average" value="{{ $latestResult ? number_format($latestResult->average, 1) : '—' }}" icon="target" color="gold" :sub="$latestResult ? $latestResult->division : 'No results yet'" />
        <x-stat-card label="Attendance" value="{{ $attendance['percentage'] }}%" icon="check-circle" color="green" :sub="$academicYear?->name ?? ''" />
        <x-stat-card label="Fee Balance" value="TZS {{ number_format($totalBalance, 0) }}" icon="credit-card" color="{{ $totalBalance > 0 ? 'red' : 'green' }}" />
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @if ($latestResult)
                <x-card title="Latest Result" icon="file-text">
                    <div class="mb-4 flex items-center gap-3">
                        <x-badge color="green">Published</x-badge>
                        <span class="text-sm text-navy-500">{{ $latestResult->term?->name }} · {{ $latestResult->academicYear?->name }}</span>
                    </div>
                    <div class="overflow-x-auto -mx-5 px-5">
                        <table class="w-full min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-navy-100 dark:border-navy-700">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Subject</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Marks</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Grade</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500">Points</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
                                @foreach ($latestResult->items as $item)
                                    <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40">
                                        <td class="px-4 py-3 font-medium text-navy-900 dark:text-navy-100">{{ $item->subject?->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right text-navy-700 dark:text-navy-300">{{ $item->marks }}</td>
                                        <td class="px-4 py-3 text-center"><x-badge color="gold">{{ $item->grade }}</x-badge></td>
                                        <td class="px-4 py-3 text-right font-semibold text-navy-900 dark:text-navy-100">{{ $item->points }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-4 text-sm">
                        <span class="text-navy-600 dark:text-navy-300">Total Points: <strong class="text-navy-900 dark:text-navy-100">{{ $latestResult->total_points }}</strong></span>
                        <span class="text-navy-600 dark:text-navy-300">Average: <strong class="text-navy-900 dark:text-navy-100">{{ number_format($latestResult->average, 1) }}</strong></span>
                        <span class="text-navy-600 dark:text-navy-300">Division: <x-badge color="navy">{{ $latestResult->division }}</x-badge></span>
                        @if ($latestResult->position)
                            <span class="text-navy-600 dark:text-navy-300">Position: <strong class="text-navy-900 dark:text-navy-100">{{ $latestResult->position }}</strong></span>
                        @endif
                    </div>
                </x-card>
            @else
                <x-card title="Latest Result" icon="file-text">
                    <x-empty-state icon="file-text" title="No Results Yet" message="Your results will appear here once published." />
                </x-card>
            @endif
        </div>

        <div class="space-y-6">
            <x-card title="Quick Links" icon="sparkles">
                <div class="space-y-2">
                    <a href="{{ route('student.results.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-navy-700 dark:text-navy-200 hover:bg-cream-50 dark:hover:bg-navy-800 transition-colors">
                        <x-icon name="file-text" class="w-5 h-5 text-navy-400" /> My Results
                    </a>
                    <a href="{{ route('student.attendance.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-navy-700 dark:text-navy-200 hover:bg-cream-50 dark:hover:bg-navy-800 transition-colors">
                        <x-icon name="check-square" class="w-5 h-5 text-navy-400" /> My Attendance
                    </a>
                    <a href="{{ route('student.timetable.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-navy-700 dark:text-navy-200 hover:bg-cream-50 dark:hover:bg-navy-800 transition-colors">
                        <x-icon name="calendar" class="w-5 h-5 text-navy-400" /> My Timetable
                    </a>
                    <a href="{{ route('student.fees.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-navy-700 dark:text-navy-200 hover:bg-cream-50 dark:hover:bg-navy-800 transition-colors">
                        <x-icon name="credit-card" class="w-5 h-5 text-navy-400" /> Fee Statement
                    </a>
                </div>
            </x-card>

            <x-card title="Attendance Summary" icon="calendar-check">
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-navy-500">Present</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $attendance['present'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-navy-500">Absent</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">{{ $attendance['absent'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-navy-500">Late</span>
                        <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $attendance['late'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-navy-500">Excused</span>
                        <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $attendance['excused'] }}</span>
                    </div>
                    <div class="pt-2 border-t border-navy-100 dark:border-navy-700">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-navy-700 dark:text-navy-200">Attendance Rate</span>
                            <span class="font-bold text-navy-900 dark:text-navy-100">{{ $attendance['percentage'] }}%</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-navy-100 dark:bg-navy-700 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ $attendance['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
@endsection
