@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-line shadow-none focus:border-crimson focus:ring-crimson']) }}>
