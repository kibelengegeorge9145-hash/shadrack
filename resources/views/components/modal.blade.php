@props(['title' => null, 'maxWidth' => 'max-w-lg', 'open' => false])

<div data-modal-trigger-wrappers><!-- placeholder for JS wiring -->
<div id="{{ $id ?? 'modal' }}" data-modal {{ $open ? '' : 'class="hidden"' }} class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div data-modal-backdrop class="absolute inset-0 bg-navy-950/60 backdrop-blur-sm"></div>
    <div class="relative w-full {{ $maxWidth }} rounded-2xl bg-white dark:bg-navy-900 shadow-card-hover border border-navy-100 dark:border-navy-700">
        <div class="flex items-center justify-between px-6 py-4 border-b border-navy-100 dark:border-navy-700">
            @if ($title)
                <h3 class="font-display text-lg font-bold text-navy-900 dark:text-navy-50">{{ $title }}</h3>
            @endif
            <button type="button" data-modal-close class="text-navy-400 hover:text-navy-700 dark:hover:text-navy-100">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
        <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>
