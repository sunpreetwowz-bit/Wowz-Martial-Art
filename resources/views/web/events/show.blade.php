@php
    use App\Support\DemoMedia;
    $image = $event->image_path
        ? asset('storage/'.$event->image_path)
        : DemoMedia::event();
    $isPast = $event->start_date->isPast() && ! $event->start_date->isToday();
@endphp
@extends('layouts.public')

@section('title', $event->title.' — Wowz Martial Art')

@section('content')
<section class="relative min-h-[68svh] overflow-hidden bg-ink text-white">
    <img src="{{ $image }}" alt="{{ $event->title }}" class="kenburns absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/85 to-ink/40"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-transparent to-ink/45"></div>

    <div class="site-container relative flex min-h-[68svh] flex-col justify-end pb-14 pt-28">
        <a href="{{ route('events.index') }}" class="text-sm text-white/70 hover:text-white">← All events</a>
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <span class="section-label !text-crimson">{{ $isPast ? 'Past event' : 'Upcoming event' }}</span>
            @if ($event->venue)
                <span class="border border-white/20 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/80">{{ $event->venue }}</span>
            @endif
        </div>
        <h1 class="font-display mt-4 max-w-4xl text-5xl uppercase leading-[0.95] sm:text-6xl">{{ $event->title }}</h1>
        <p class="mt-5 max-w-2xl text-lg text-white/80">
            {{ $event->start_date->format('l, d F Y') }}
            @if ($event->end_date && ! $event->end_date->equalTo($event->start_date))
                – {{ $event->end_date->format('d F Y') }}
            @endif
            @if ($event->start_time)
                · {{ \Illuminate\Support\Str::of($event->start_time)->substr(0, 5) }}
                @if ($event->end_time)
                    – {{ \Illuminate\Support\Str::of($event->end_time)->substr(0, 5) }}
                @endif
            @endif
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-primary">Enquire / reserve a seat</a>
            <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone')) }}" class="btn-ghost">Call {{ config('academy.phone') }}</a>
        </div>
    </div>
</section>

{{-- Quick facts --}}
<section class="border-b border-line bg-white">
    <div class="site-container grid gap-6 py-10 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-stoneish">Date</div>
            <p class="mt-2 font-display text-xl uppercase text-ink">{{ $event->start_date->format('d M Y') }}</p>
        </div>
        <div>
            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-stoneish">Time</div>
            <p class="mt-2 font-display text-xl uppercase text-ink">
                @if ($event->start_time)
                    {{ \Illuminate\Support\Str::of($event->start_time)->substr(0, 5) }}
                    @if ($event->end_time)
                        – {{ \Illuminate\Support\Str::of($event->end_time)->substr(0, 5) }}
                    @endif
                @else
                    See details
                @endif
            </p>
        </div>
        <div>
            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-stoneish">Venue</div>
            <p class="mt-2 font-display text-xl uppercase text-ink">{{ $event->venue ?: 'Academy branch' }}</p>
        </div>
        <div>
            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-stoneish">Status</div>
            <p class="mt-2 font-display text-xl uppercase text-ink">{{ $isPast ? 'Completed' : 'Open for enquiry' }}</p>
        </div>
    </div>
</section>

<section class="bg-paper py-20 lg:py-24">
    <div class="site-container grid gap-12 lg:grid-cols-12 lg:items-start">
        <div class="lg:col-span-7">
            <p class="section-label">About this event</p>
            <h2 class="section-title mt-3">What to expect</h2>
            <div class="section-copy whitespace-pre-line">{{ $event->description }}</div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                <div class="border border-line bg-white p-5">
                    <div class="h-1 w-8 bg-crimson"></div>
                    <h3 class="font-display mt-4 text-lg uppercase text-ink">Who should come</h3>
                    <p class="mt-2 text-sm leading-relaxed text-stoneish">Students, parents, and trial guests — check registration notes for belt or age requirements.</p>
                </div>
                <div class="border border-line bg-white p-5">
                    <div class="h-1 w-8 bg-crimson"></div>
                    <h3 class="font-display mt-4 text-lg uppercase text-ink">What to bring</h3>
                    <p class="mt-2 text-sm leading-relaxed text-stoneish">Comfortable training clothes, water bottle, and student ID if you already train with us. Pads provided when needed.</p>
                </div>
            </div>

            @if ($event->registration_info)
                <div class="mt-10 border-l-4 border-crimson bg-white px-6 py-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-crimson">Registration</div>
                    <p class="mt-2 text-base leading-relaxed text-ink">{{ $event->registration_info }}</p>
                </div>
            @endif
        </div>

        <aside class="space-y-6 lg:col-span-5">
            <div class="media-frame aspect-[4/3]">
                <img src="{{ $image }}" alt="{{ $event->title }}">
            </div>

            <div class="border border-line bg-white p-6">
                <h3 class="font-display text-xl uppercase text-ink">Venue details</h3>
                @if ($event->venue)
                    <p class="mt-4 font-medium text-ink">{{ $event->venue }}</p>
                @endif
                @if ($event->address)
                    <p class="mt-2 text-sm leading-relaxed text-stoneish">{{ $event->address }}</p>
                @endif
                <p class="mt-4 text-sm text-stoneish">
                    Training also runs at our other branches — ask which location is best for your schedule.
                </p>
                <ul class="mt-4 space-y-2 text-sm text-ink">
                    @foreach (config('academy.branches') as $branch)
                        <li class="flex gap-2 border-t border-line pt-2">
                            <span class="text-crimson">●</span>
                            <span>{{ $branch['name'] }} · {{ $branch['area'] }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('contact.create') }}" class="btn-primary mt-6 w-full sm:w-auto">
                    {{ $isPast ? 'Ask about the next date' : 'Reserve / enquire' }}
                </a>
            </div>

            <div class="border border-line bg-ink p-6 text-white">
                <h3 class="font-display text-lg uppercase">Need directions?</h3>
                <p class="mt-2 text-sm text-white/70">Call or message us with your preferred branch and we’ll share the exact gate / entry notes.</p>
                <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone')) }}" class="mt-4 inline-block text-sm font-semibold text-crimson hover:text-white">
                    {{ config('academy.phone') }}
                </a>
            </div>
        </aside>
    </div>
</section>

@if ($related->isNotEmpty())
<section class="bg-white py-20 lg:py-24">
    <div class="site-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-label">More on the calendar</p>
                <h2 class="section-title mt-3">Related events</h2>
            </div>
            <a href="{{ route('events.index') }}" class="btn-outline">All events</a>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($related as $item)
                @php
                    $relatedImage = $item->image_path
                        ? asset('storage/'.$item->image_path)
                        : DemoMedia::event();
                @endphp
                <a href="{{ route('events.show', $item) }}" class="group border border-line bg-paper transition hover:border-crimson">
                    <div class="aspect-[16/10] overflow-hidden">
                        <img src="{{ $relatedImage }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <div class="p-5">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-crimson">{{ $item->start_date->format('d M Y') }}</div>
                        <h3 class="font-display mt-2 text-xl uppercase text-ink group-hover:text-crimson">{{ $item->title }}</h3>
                        @if ($item->venue)
                            <p class="mt-2 text-sm text-stoneish">{{ $item->venue }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="bg-crimson py-16 text-white">
    <div class="site-container flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
        <div>
            <h2 class="font-display text-3xl uppercase sm:text-4xl">
                {{ $isPast ? 'Join the next academy event' : 'See you on the mat' }}
            </h2>
            <p class="mt-2 max-w-xl text-white/85">
                Prefer a regular class instead? Book a trial at Sector 46, Sector 27, or Zirakpur.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-ink">Contact us</a>
            <a href="{{ route('services.index') }}" class="btn-ghost">View programs</a>
        </div>
    </div>
</section>
@endsection
