@props([
    'label',
    'value' => '0',
    'hint' => null,
    'tone' => 'slate',
])

@php
    $tones = [
        'slate' => 'border-l-ink',
        'red' => 'border-l-crimson',
        'emerald' => 'border-l-emerald-600',
        'amber' => 'border-l-amber-500',
        'sky' => 'border-l-sky-600',
    ];
    $toneClass = $tones[$tone] ?? $tones['slate'];
@endphp

<div {{ $attributes->merge(['class' => 'admin-panel border-l-4 p-5 '.$toneClass]) }}>
    <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-stoneish">{{ $label }}</div>
    <div class="mt-2 font-display text-3xl uppercase tracking-wide text-ink">{{ $value }}</div>
    @if ($hint)
        <div class="mt-2 text-xs text-stoneish">{{ $hint }}</div>
    @endif
</div>
