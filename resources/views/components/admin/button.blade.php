@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])

@php
    $variants = [
        'primary' => 'admin-btn-primary',
        'secondary' => 'admin-btn-secondary',
        'danger' => 'admin-btn-ink',
        'ghost' => 'admin-btn bg-transparent text-stoneish hover:bg-paper hover:text-ink',
    ];

    $classes = ($variants[$variant] ?? $variants['primary']).' focus:outline-none focus:ring-2 focus:ring-crimson focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
