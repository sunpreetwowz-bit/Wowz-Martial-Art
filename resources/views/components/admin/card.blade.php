@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'admin-panel overflow-hidden']) }}>
    @isset($header)
        <div class="border-b border-line bg-paper/60 px-4 py-3 sm:px-6">
            {{ $header }}
        </div>
    @endisset

    <div @class(['p-4 sm:p-6' => $padding])>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-line bg-paper px-4 py-3 sm:px-6">
            {{ $footer }}
        </div>
    @endisset
</div>
