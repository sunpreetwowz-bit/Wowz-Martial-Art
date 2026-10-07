@props([])

<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <table class="min-w-full divide-y divide-line text-left text-sm">
        @isset($head)
            <thead class="bg-paper text-[11px] font-semibold uppercase tracking-[0.14em] text-stoneish">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endisset

        <tbody class="divide-y divide-line bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
