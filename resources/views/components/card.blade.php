@props([
    'padding' => true,
    'title' => null,
    'icon' => null,
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-navy-900 border border-navy-100 dark:border-navy-700/60 rounded-xl shadow-card']) }}>
    @if ($title || $action || $icon)
        <div class="flex items-center justify-between gap-3 px-5 pt-5 {{ $padding ? '' : '' }}">
            <div class="flex items-center gap-2.5">
                @if ($icon)
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-navy-800/5 dark:bg-gold-400/10 text-navy-800 dark:text-gold-300">
                        <x-icon name="{{ $icon }}" class="w-4.5 h-4.5" />
                    </span>
                @endif
                @if ($title)
                    <h3 class="font-display text-base font-bold text-navy-900 dark:text-navy-50">{{ $title }}</h3>
                @endif
            </div>
            @if ($action)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif
    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>
</div>
