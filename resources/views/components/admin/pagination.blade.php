@props([
    'paginator',
])

@if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'border-t border-slate-200 bg-white px-4 py-3 sm:px-6']) }}>
        {{ $paginator->withQueryString()->links() }}
    </div>
@endif
