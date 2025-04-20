@props([
    'input_id', 
    'name', 
    'label', 
    'label_for', 
    'option_value',
    'option_text',
    'placeholder',
])

@php
    $input_id = $input_id ?? 'input_id';
    $name = $name ?? $input_id;
    $label_for = $label_for ?? $name;
    $label = $label ?? $input_id;
    $placeholder = $placeholder ?? "Search & Select {$label}";
    $option_value = $option_value ?? null;
    $option_text = $option_text ?? null;
@endphp

<div class="mb-4">
    <x-input-label for="{{ $label_for }}">{{ $label }}</x-input-label>
    <select name="{{ $name }}" 
            id="{{ $input_id }}" 
            class="select2-ajax form-control w-full" 
            data-placeholder="{{ $placeholder }}"
            required>
        @if($option_value && $option_text)
            <option value="{{ $option_text }}" selected>{{ $option_text }}</option>
        @endif
    </select>
</div>