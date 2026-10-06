@props(['type' => 'text', 'leadingIcon' => null, 'trailingIcon' => null, 'label' => null, 'name' => null, 'required' => false, 'value' => null])

<div>
    @if ($label)
        <x-label :for="$name" :required="$required">{{ $label }}</x-label>
    @endif
    <div class="relative mt-1.5">
        @if ($leadingIcon)
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-navy-400 dark:text-navy-500">
                <x-icon :name="$leadingIcon" class="w-4 h-4" />
            </span>
        @endif
        <input type="{{ $type }}" name="{{ $name }}" value="{{ $value ?? old($name) }}"
            {{ $attributes->merge(['class' => 'w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 placeholder-navy-400 dark:placeholder-navy-500 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400 transition ' . ($leadingIcon ? 'pl-10 ' : '') . ($trailingIcon ? 'pr-10 ' : '')]) }} />
        @if ($trailingIcon)
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-navy-400 dark:text-navy-500">
                <x-icon :name="$trailingIcon" class="w-4 h-4" />
            </span>
        @endif
    </div>
    @error($name)
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
