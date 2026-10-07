<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ $title ?? ($header ?? null) }}
        {{ isset($title) || isset($header) ? ' — ' : '' }}
        {{ config('admin.name', config('app.name')) }} Admin
    </title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=oswald:500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-ink admin-shell">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-ink/60 lg:hidden"
            @click="sidebarOpen = false"
            style="display: none;"
        ></div>

        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="admin-sidebar fixed inset-y-0 left-0 z-50 flex w-72 flex-col text-white transition-transform duration-200 lg:static lg:translate-x-0"
        >
            <div class="flex h-[4.5rem] items-center gap-3 border-b border-white/10 px-5">
                <div class="flex h-11 w-11 items-center justify-center bg-crimson font-display text-xl font-semibold text-white">
                    W
                </div>
                <div class="min-w-0">
                    <div class="truncate font-display text-sm uppercase tracking-[0.14em] text-white">Wowz Martial Art</div>
                    <div class="text-[11px] uppercase tracking-[0.18em] text-white/40">Admin console</div>
                </div>
            </div>

            <div class="border-b border-white/10 px-5 py-3">
                <div class="text-[11px] uppercase tracking-[0.16em] text-white/35">Academy desk</div>
                <div class="mt-1 text-sm text-white/75">Students · Tests · Website</div>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4">
                @include('admin.partials.sidebar')
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
                <div class="truncate text-xs text-white/40">{{ auth()->user()->email }}</div>
                <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1">
                    <a href="{{ route('home') }}" target="_blank" class="text-xs font-semibold uppercase tracking-[0.12em] text-crimson hover:text-white">Website</a>
                    <a href="{{ route('profile.edit') }}" class="text-xs font-semibold uppercase tracking-[0.12em] text-white/45 hover:text-white">Profile</a>
                </div>
            </div>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col lg:min-w-0">
            <header class="sticky top-0 z-30 border-b border-line bg-white/95 backdrop-blur">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center border border-line p-2 text-stoneish hover:border-crimson hover:text-crimson lg:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Open sidebar"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div>
                            @isset($header)
                                <div class="font-display text-lg uppercase tracking-[0.08em] text-ink">{{ $header }}</div>
                            @else
                                <div class="font-display text-lg uppercase tracking-[0.08em] text-ink">{{ $title ?? 'Admin' }}</div>
                            @endisset
                            <div class="hidden text-xs text-stoneish sm:block">Manage academy operations</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('home') }}" target="_blank" class="admin-btn-secondary hidden sm:inline-flex">
                            View website
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="admin-btn-ink">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <x-admin.alerts />
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
