@props(['name' => null, 'label' => null, 'required' => false, 'options' => [], 'placeholder' => null, 'selected' => null, 'emptyText' => 'No options available'])

<div>
    @if ($label)
        <x-label :for="$name" :required="$required">{{ $label }}</x-label>
    @endif
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'mt-1.5 w-full rounded-lg border border-navy-200 dark:border-navy-700 bg-white dark:bg-navy-800 text-navy-900 dark:text-navy-100 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-gold-400/40 focus:border-gold-400 transition']) }}>
        @if ($placeholder)
            <option value="" disabled {{ !$selected ? 'selected' : '' }}>{{ $placeholder }}</option>
        @endif
        @forelse ($options as $value => $labelText)
            <option value="{{ $value }}" @if((string)$selected === (string)$value || old($name) == $value) selected @endif>{{ $labelText }}</option>
        @empty
            <option disabled>{{ $emptyText }}</option>
        @endforelse
    </select>
    @error($name)
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
