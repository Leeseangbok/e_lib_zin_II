@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-lg ']) }}>
    {{ $value ?? $slot }}
</label>
