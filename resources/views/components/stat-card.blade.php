@props([
    'label' => '',
    'value' => '',
    'icon' => 'check',
    'color' => 'navy',
    'sub' => null,
    'href' => null,
])

@php
    $colors = [
        'navy' => 'bg-navy-800 text-gold-300 dark:bg-gold-400/10 dark:text-gold-300',
        'gold' => 'bg-gold-400/15 text-gold-600 dark:text-gold-300',
        'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    ];
@endphp

<div class="relative overflow-hidden bg-white dark:bg-navy-900 border border-navy-100 dark:border-navy-700/60 rounded-xl shadow-card p-5 transition hover:shadow-card-hover hover:-translate-y-0.5">
    <div class="flex items-start justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400">{{ $label }}</p>
            <p class="mt-2 font-display text-2xl md:text-3xl font-bold text-navy-900 dark:text-navy-50 truncate">{{ $value }}</p>
            @if ($sub)
                <p class="mt-1.5 text-xs text-navy-500 dark:text-navy-400">{{ $sub }}</p>
            @endif
        </div>
        <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg shrink-0 {{ $colors[$color] ?? $colors['navy'] }}">
            <x-icon :name="$icon" class="w-5 h-5" />
        </span>
    </div>
</div>
