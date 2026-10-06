@props(['color' => 'navy'])

@php
    $colors = [
        'navy' => 'bg-navy-100 text-navy-800 dark:bg-navy-800 dark:text-navy-100',
        'gold' => 'bg-gold-400/15 text-gold-600 dark:text-gold-300',
        'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        'gray' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-300',
        'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ' . ($colors[$color] ?? $colors['navy'])]) }}>
    {{ $slot }}
</span>
