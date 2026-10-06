@props(['name' => null, 'label' => null, 'required' => false, 'rows' => 4])

<div>
    @if ($label)
        <x-label :for="$name" :required="$required">{{ $label }}</x-label>
    @endif
    <textarea name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->merge(['class' => 'mt-1.5 w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 placeholder-navy-400 dark:placeholder-navy-500 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400 transition']) }}>{{ $slot }}</textarea>
    @error($name)
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
