@props(['for' => null, 'required' => false])

<label {{ $attributes->merge(['for' => $for, 'class' => 'block text-sm font-medium text-navy-700 dark:text-navy-200']) }}>
    {{ $slot }}
    @if ($required)
        <span class="text-red-500">*</span>
    @endif
</label>
