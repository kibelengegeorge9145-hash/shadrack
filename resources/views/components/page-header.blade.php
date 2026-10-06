@props(['title' => '', 'subtitle' => null, 'actions' => null])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-navy-900 dark:text-navy-50 tracking-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-navy-500 dark:text-navy-300">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($actions)
        <div class="flex items-center gap-2 flex-wrap shrink-0">{{ $actions }}</div>
    @endif
</div>
