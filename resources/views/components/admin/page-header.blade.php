@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 border-b border-line pb-5 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div>
        @if ($title)
            <h1 class="font-display text-3xl uppercase tracking-[0.06em] text-ink">{{ $title }}</h1>
        @endif
        @if ($description)
            <p class="mt-1.5 max-w-2xl text-sm text-stoneish">{{ $description }}</p>
        @endif
        {{ $slot }}
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
