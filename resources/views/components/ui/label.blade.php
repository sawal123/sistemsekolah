@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm txt-secondary']) }}>
    {{ $value ?? $slot }}
</label>