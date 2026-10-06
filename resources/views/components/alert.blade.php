@props(['type' => 'info', 'title' => null, 'flash' => false])

@php
    $types = [
        'success' => ['bg-emerald-50 dark:bg-emerald-950/50 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200', 'check-circle'],
        'error' => ['bg-red-50 dark:bg-red-950/50 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200', 'x-circle'],
        'warning' => ['bg-amber-50 dark:bg-amber-950/50 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200', 'alert'],
        'info' => ['bg-navy-50 dark:bg-navy-900/60 border-navy-200 dark:border-navy-700 text-navy-800 dark:text-navy-200', 'info'],
    ];
    [$styles, $icon] = $types[$type] ?? $types['info'];
    $flashType = session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') ? 'info' : null)));
    $flashMessage = session('success') ?? session('error') ?? session('warning') ?? session('info');
    $showFlash = $flash && $flashMessage;
    $activeType = $showFlash ? $flashType : $type;
    $activeMessage = $showFlash ? $flashMessage : null;
    [$activeStyles, $activeIcon] = $types[$activeType] ?? $types['info'];
@endphp

@if ($activeMessage !== null || !$showFlash)
    <div role="alert" data-alert="{{ $showFlash ? 'flash' : 'static' }}" {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm ' . $activeStyles]) }}>
        <x-icon :name="$activeIcon" class="w-5 h-5 shrink-0 mt-0.5" />
        <div class="flex-1 min-w-0">
            @if ($title && !$showFlash)
                <p class="font-semibold">{{ $title }}</p>
            @endif
            <div>{{ $activeMessage ?? $slot }}</div>
        </div>
        <button type="button" data-dismiss-alert class="shrink-0 opacity-60 hover:opacity-100 transition-opacity" aria-label="Dismiss">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>
@endif
