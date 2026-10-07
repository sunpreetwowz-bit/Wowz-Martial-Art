@php
    use App\Support\AdminNavigation;
    $items = AdminNavigation::items();
@endphp

<ul class="space-y-1">
    @foreach ($items as $item)
        @if (! empty($item['children']))
            <li x-data="{ open: {{ $item['active'] ? 'true' : 'false' }} }" class="pt-2">
                <button
                    type="button"
                    class="flex w-full items-center justify-between px-3 py-2.5 text-left text-sm font-medium text-white/65 hover:bg-white/5 hover:text-white"
                    @click="open = ! open"
                >
                    <span class="flex items-center gap-2.5">
                        <x-admin.nav-icon :name="$item['icon'] ?? 'folder'" />
                        <span class="uppercase tracking-[0.08em]">{{ $item['label'] }}</span>
                    </span>
                    <svg class="h-4 w-4 transition" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <ul x-show="open" x-transition class="mt-1 space-y-1 border-l border-white/10 ml-4 pl-2" style="display: none;">
                    @foreach ($item['children'] as $child)
                        <li>
                            @if ($child['available'])
                                <a
                                    href="{{ $child['url'] }}"
                                    @class([
                                        'block px-3 py-2 text-sm transition',
                                        'bg-crimson text-white' => $child['active'],
                                        'text-white/50 hover:bg-white/5 hover:text-white' => ! $child['active'],
                                    ])
                                >
                                    {{ $child['label'] }}
                                </a>
                            @else
                                <span class="flex cursor-not-allowed items-center justify-between px-3 py-2 text-sm text-white/25" title="Coming soon">
                                    <span>{{ $child['label'] }}</span>
                                    <span class="text-[10px] uppercase tracking-wide">Soon</span>
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </li>
        @else
            <li>
                @if ($item['available'])
                    <a
                        href="{{ $item['url'] }}"
                        @class([
                            'admin-nav-link',
                            'is-active' => $item['active'],
                        ])
                    >
                        <x-admin.nav-icon :name="$item['icon'] ?? 'folder'" />
                        <span class="uppercase tracking-[0.08em]">{{ $item['label'] }}</span>
                    </a>
                @else
                    <span class="flex cursor-not-allowed items-center justify-between px-3 py-2.5 text-sm text-white/25" title="Coming soon">
                        <span class="flex items-center gap-2.5">
                            <x-admin.nav-icon :name="$item['icon'] ?? 'folder'" />
                            <span class="uppercase tracking-[0.08em]">{{ $item['label'] }}</span>
                        </span>
                        <span class="text-[10px] uppercase tracking-wide">Soon</span>
                    </span>
                @endif
            </li>
        @endif
    @endforeach
</ul>
