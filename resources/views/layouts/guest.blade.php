<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Wowz Martial Art') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=oswald:500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-ink antialiased">
    <div class="relative min-h-screen overflow-hidden bg-ink">
        <img
            src="{{ asset('storage/gallery/evening-pads.jpg') }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover opacity-35"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/90 to-ink/70"></div>

        <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10">
            <a href="{{ route('home') }}" class="mb-8 flex items-center gap-3 text-white">
                <span class="flex h-11 w-11 items-center justify-center bg-crimson font-display text-xl">W</span>
                <span class="leading-tight">
                    <span class="block font-display text-xl uppercase tracking-[0.14em]">Wowz Martial Art</span>
                    <span class="block text-xs uppercase tracking-[0.16em] text-white/55">Member access</span>
                </span>
            </a>

            <div class="w-full max-w-md border border-white/10 bg-white p-6 shadow-2xl sm:p-8">
                {{ $slot }}
            </div>

            <a href="{{ route('home') }}" class="mt-6 text-sm text-white/60 hover:text-white">← Back to website</a>
        </div>
    </div>
</body>
</html>
