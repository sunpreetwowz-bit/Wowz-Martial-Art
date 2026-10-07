@props([
    'title' => 'No records found',
    'description' => 'There is nothing to show here yet.',
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center']) }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
    <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
    @isset($actions)
        <div class="mt-6">{{ $actions }}</div>
    @endisset
</div>
