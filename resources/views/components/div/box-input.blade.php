<div {{ $attributes->merge(['class' => 'form-group mb-4']) }}>
    <x-input.input-label for="{{ $for ?? '' }}">{{ $label ?? '' }}</x-input-label>
    {{ $slot }}
</div>