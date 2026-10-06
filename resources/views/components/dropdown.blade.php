@props(['align' => 'right', 'width' => 'w-56', 'label' => null, 'icon' => 'chevron-down'])

@php
    $alignClass = $align === 'left' ? 'left-0' : 'right-0';
@endphp

<div class="relative" data-dropdown>
    <button type="button" data-dropdown-trigger class="inline-flex items-center gap-1.5 ...">
        @if ($label)
            <span>{{ $label }}</span>
        @else
            {{ $trigger ?? 'Menu' }}
        @endif
        <x-icon :name="$icon" class="w-4 h-4" />
    </button>

    <div data-dropdown-menu class="hidden absolute z-50 mt-2 {{ $width }} {{ $alignClass }} rounded-xl border border-navy-100 dark:border-navy-700 bg-white dark:bg-navy-900 shadow-card-hover p-1.5">
        {{ $slot }}
    </div>
</div>
