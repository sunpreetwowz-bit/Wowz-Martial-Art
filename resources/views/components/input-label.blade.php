@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-[0.14em] text-stoneish']) }}>
    {{ $value ?? $slot }}
</label>
