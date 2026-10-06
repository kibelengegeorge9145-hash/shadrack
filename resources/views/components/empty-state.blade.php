@props(['icon' => 'inbox', 'title' => 'Nothing here yet', 'message' => null, 'action' => null])

<div class="flex flex-col items-center justify-center py-12 text-center">
    <span class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-cream-100 dark:bg-navy-800 text-navy-300 dark:text-navy-600 mb-4">
        <x-icon :name="$icon" class="w-7 h-7" />
    </span>
    <h3 class="font-display text-lg font-bold text-navy-800 dark:text-navy-100">{{ $title }}</h3>
    @if ($message)
        <p class="mt-1 text-sm text-navy-500 dark:text-navy-400 max-w-sm">{{ $message }}</p>
    @endif
    @if ($action)
        <div class="mt-5">{{ $action }}</div>
    @endif
</div>
