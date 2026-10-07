@props([
    'status',
    'label' => null,
])

@php
    $map = [
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'inactive' => 'bg-paper text-stoneish ring-line',
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'payment_pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'submitted' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'accepted' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'rejected' => 'bg-red-50 text-red-700 ring-red-600/20',
        'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'passed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
        'expired' => 'bg-paper text-stoneish ring-line',
        'issued' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'revoked' => 'bg-red-50 text-red-700 ring-red-600/20',
        'responded' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'not_required' => 'bg-paper text-stoneish ring-line',
        'assigned' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'draft' => 'bg-paper text-stoneish ring-line',
        'open' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'closed' => 'bg-paper text-stoneish ring-line',
        'unread' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'read' => 'bg-paper text-stoneish ring-line',
        'published' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    ];

    $value = $status instanceof \BackedEnum
        ? $status->value
        : (string) $status;

    $classes = $map[$value] ?? 'bg-paper text-ink ring-line';

    $display = $label
        ?? (is_object($status) && method_exists($status, 'label') ? $status->label() : str_replace('_', ' ', ucfirst($value)));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.08em] ring-1 ring-inset {$classes}"]) }}>
    {{ $display }}
</span>
