@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-[#20392c]']) }}>
    {{ $value ?? $slot }}
</label>
