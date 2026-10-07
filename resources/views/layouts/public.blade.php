<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('academy.name', 'Wowz Martial Art'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=oswald:500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #page-loader{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:#111;transition:opacity .45s ease,visibility .45s ease}
        #page-loader.is-done{opacity:0;visibility:hidden;pointer-events:none}
        #page-loader .pl-mark{width:3.25rem;height:3.25rem;display:flex;align-items:center;justify-content:center;background:#d01230;color:#fff;font-family:Oswald,sans-serif;font-size:1.5rem;letter-spacing:.06em}
        #page-loader .pl-ring{position:absolute;width:5.5rem;height:5.5rem;border:2px solid rgba(255,255,255,.12);border-top-color:#d01230;border-radius:50%;animation:pl-spin .8s linear infinite}
        #page-loader .pl-wrap{position:relative;display:flex;align-items:center;justify-content:center}
        #page-loader .pl-text{position:absolute;top:calc(100% + 1.25rem);left:50%;transform:translateX(-50%);white-space:nowrap;font-family:Oswald,sans-serif;font-size:.7rem;letter-spacing:.28em;text-transform:uppercase;color:rgba(255,255,255,.55)}
        @keyframes pl-spin{to{transform:rotate(360deg)}}
        @media (prefers-reduced-motion:reduce){#page-loader .pl-ring{animation:none}#page-loader{display:none!important}}
    </style>
</head>
<body class="font-sans text-ink bg-white" data-motion="public" x-data="{ open: false }">
    <div id="page-loader" aria-live="polite" aria-busy="true" role="status">
        <div class="pl-wrap">
            <span class="pl-ring" aria-hidden="true"></span>
            <span class="pl-mark">W</span>
            <span class="pl-text">Wowz Martial Art</span>
        </div>
    </div>

    {{-- Top contact bar (academy-style) --}}
    <div class="topbar hidden sm:block">
        <div class="site-container flex items-center justify-between py-2 text-xs tracking-wide text-white/80">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
                <a href="mailto:{{ config('academy.email') }}" class="hover:text-white">{{ config('academy.email') }}</a>
                <span class="text-white/25">|</span>
                <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone')) }}" class="hover:text-white">{{ config('academy.phone') }}</a>
                @if (config('academy.phone_alt'))
                    <span class="text-white/25">·</span>
                    <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone_alt')) }}" class="hover:text-white">{{ config('academy.phone_alt') }}</a>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <span class="hidden md:inline">{{ config('academy.hours') }}</span>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="font-semibold uppercase tracking-[0.12em] text-white hover:text-crimson">
                    {{ auth()->check() ? 'Portal' : 'Login' }}
                </a>
            </div>
        </div>
    </div>

    {{-- Main white header --}}
    <header class="site-header">
        <div class="site-container flex items-center justify-between py-3.5">
            <a href="{{ route('home') }}" class="group flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center bg-crimson font-display text-xl text-white shadow-sm transition group-hover:bg-crimson-dark">W</span>
                <span class="leading-tight">
                    <span class="block font-display text-lg font-semibold uppercase tracking-[0.14em] text-ink">Wowz</span>
                    <span class="block text-[11px] font-semibold uppercase tracking-[0.18em] text-stoneish">Martial Art Academy</span>
                </span>
            </a>

            <nav class="hidden items-center gap-6 lg:flex">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about*') ? 'is-active' : '' }}">About</a>
                <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'is-active' : '' }}">Programs</a>
                <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.*') ? 'is-active' : '' }}">Events</a>
                <a href="{{ route('gallery.index') }}" class="nav-link {{ request()->routeIs('gallery.*') ? 'is-active' : '' }}">Gallery</a>
                <a href="{{ route('achievements.index') }}" class="nav-link {{ request()->routeIs('achievements.*') ? 'is-active' : '' }}">Results</a>
                <a href="{{ route('black-belts.index') }}" class="nav-link {{ request()->routeIs('black-belts.*') ? 'is-active' : '' }}">Black Belts</a>
                <a href="{{ route('contact.create') }}" class="nav-link {{ request()->routeIs('contact.*') ? 'is-active' : '' }}">Contact</a>
                <a href="{{ route('contact.create') }}" class="btn-primary !px-5 !py-2.5">Book Trial</a>
            </nav>

            <button type="button" class="text-ink lg:hidden" @click="open = ! open" aria-label="Menu">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="border-t border-line bg-white lg:hidden"
        >
            <div class="site-container flex flex-col gap-1 py-4 text-sm font-semibold uppercase tracking-[0.08em]">
                <a href="{{ route('home') }}" class="py-2 text-ink hover:text-crimson">Home</a>
                <a href="{{ route('about') }}" class="py-2 text-ink hover:text-crimson">About</a>
                <a href="{{ route('services.index') }}" class="py-2 text-ink hover:text-crimson">Programs</a>
                <a href="{{ route('events.index') }}" class="py-2 text-ink hover:text-crimson">Events</a>
                <a href="{{ route('gallery.index') }}" class="py-2 text-ink hover:text-crimson">Gallery</a>
                <a href="{{ route('achievements.index') }}" class="py-2 text-ink hover:text-crimson">Results</a>
                <a href="{{ route('black-belts.index') }}" class="py-2 text-ink hover:text-crimson">Black Belts</a>
                <a href="{{ route('contact.create') }}" class="py-2 text-ink hover:text-crimson">Contact</a>
                <a href="{{ route('certificates.verify.form') }}" class="py-2 text-ink hover:text-crimson">Verify Certificate</a>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="py-2 text-ink hover:text-crimson">{{ auth()->check() ? 'Portal' : 'Login' }}</a>
                <a href="{{ route('contact.create') }}" class="btn-primary mt-2 w-fit">Book Trial</a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-navy text-white">
        <div class="site-container grid gap-12 py-16 md:grid-cols-12">
            <div class="md:col-span-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center bg-crimson font-display text-xl">W</span>
                    <div>
                        <div class="font-display text-2xl uppercase tracking-[0.12em]">Wowz Martial Art</div>
                        <div class="text-xs uppercase tracking-[0.16em] text-white/50">Chandigarh Academy</div>
                    </div>
                </div>
                <p class="mt-5 max-w-md text-sm leading-relaxed text-white/65">
                    Taekwondo, kickboxing, kids martial arts, and fitness coaching with clear belt progress and competition support.
                </p>
            </div>

            <div class="md:col-span-3">
                <div class="font-display text-sm uppercase tracking-[0.18em] text-white/45">Explore</div>
                <div class="mt-4 flex flex-col gap-2 text-sm text-white/80">
                    <a href="{{ route('about') }}" class="hover:text-white">About Us</a>
                    <a href="{{ route('services.index') }}" class="hover:text-white">Programs</a>
                    <a href="{{ route('events.index') }}" class="hover:text-white">Events</a>
                    <a href="{{ route('gallery.index') }}" class="hover:text-white">Gallery</a>
                    <a href="{{ route('black-belts.index') }}" class="hover:text-white">Our Black Belts</a>
                    <a href="{{ route('certificates.verify.form') }}" class="hover:text-white">Verify Certificate</a>
                </div>
            </div>

            <div class="md:col-span-4">
                <div class="font-display text-sm uppercase tracking-[0.18em] text-white/45">Our branches</div>
                <ul class="mt-4 space-y-3 text-sm leading-relaxed text-white/80">
                    @foreach (config('academy.branches') as $branch)
                        <li>
                            <span class="text-white">{{ $branch['name'] }}</span><br>
                            <span class="text-white/55">{{ $branch['area'] }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-5 text-sm text-white/80">
                    {{ config('academy.hours') }}<br>
                    <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone')) }}" class="hover:text-white">{{ config('academy.phone') }}</a><br>
                    <a href="mailto:{{ config('academy.email') }}" class="hover:text-white">{{ config('academy.email') }}</a>
                </p>
                <a href="{{ route('contact.create') }}" class="btn-primary mt-6">Book a trial class</a>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="site-container flex flex-col gap-2 py-5 text-xs text-white/40 sm:flex-row sm:justify-between">
                <span>© {{ date('Y') }} Wowz Martial Art Academy. All rights reserved.</span>
                <span class="font-display uppercase tracking-[0.16em]">Discipline · Fitness · Self Defence</span>
            </div>
        </div>
    </footer>

    <style>[x-cloak]{display:none!important}</style>
    <script>
        (function () {
            var loader = document.getElementById('page-loader');
            if (!loader) return;
            var hide = function () {
                loader.classList.add('is-done');
                loader.setAttribute('aria-busy', 'false');
                window.setTimeout(function () {
                    if (loader && loader.parentNode) loader.parentNode.removeChild(loader);
                }, 500);
            };
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                hide();
                return;
            }
            var done = false;
            var finish = function () {
                if (done) return;
                done = true;
                window.setTimeout(hide, 280);
            };
            if (document.readyState === 'complete') {
                finish();
            } else {
                window.addEventListener('load', finish);
            }
            window.setTimeout(finish, 4000);
        })();
    </script>
</body>
</html>
