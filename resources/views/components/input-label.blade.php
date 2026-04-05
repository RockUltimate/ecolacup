@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-bold uppercase tracking-widest text-on-surface dark:text-[#e5e2dd]']) }}>
    {{ $value ?? $slot }}
</label>
