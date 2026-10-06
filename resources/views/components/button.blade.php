@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'disabled' => false,
    'loading' => false,
])

@php
    $variants = [
        'primary' => 'bg-navy-800 text-white hover:bg-navy-700 dark:bg-gold-400 dark:text-navy-950 dark:hover:bg-gold-300 shadow-sm',
        'gold' => 'bg-gold-400 text-navy-950 hover:bg-gold-300 shadow-sm',
        'secondary' => 'bg-white text-navy-800 border border-navy-200 hover:bg-cream-100 dark:bg-navy-800 dark:text-navy-100 dark:border-navy-700 dark:hover:bg-navy-700',
        'outline' => 'border border-navy-300 text-navy-800 hover:bg-navy-50 dark:border-navy-700 dark:text-navy-100 dark:hover:bg-navy-800',
        'danger' => 'bg-red-600 text-white hover:bg-red-500 shadow-sm',
        'ghost' => 'text-navy-700 hover:bg-navy-100/60 dark:text-navy-200 dark:hover:bg-navy-800',
    ];
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    $classes = 'inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gold-400 focus-visible:ring-offset-1 disabled:opacity-60 disabled:cursor-not-allowed ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($loading) <x-icon name="loader" class="w-4 h-4 animate-spin" /> @elseif ($icon) <x-icon name="{{ $icon }}" class="w-4 h-4" /> @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => $attributes->get('type', 'button')]) }} @if($disabled || $loading) disabled @endif>
        @if ($loading) <x-icon name="loader" class="w-4 h-4 animate-spin" /> @elseif ($icon) <x-icon name="{{ $icon }}" class="w-4 h-4" /> @endif
        {{ $slot }}
    </button>
@endif
