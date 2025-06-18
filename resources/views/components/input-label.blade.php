@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-lg text-white']) }}>
    {{ $value ?? $slot }}
</label>
