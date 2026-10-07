@php use App\Support\DemoMedia; @endphp
@extends('layouts.public')

@section('title', 'Events — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="section-label">Events</p>
        <h1 class="font-display mt-3 text-5xl uppercase sm:text-6xl">Dojo calendar</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/75">
            Open houses, belt grading camps, and sparring clinics across our Chandigarh & Zirakpur branches.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-primary">Ask about an event</a>
            <a href="#upcoming" class="btn-ghost">Jump to upcoming</a>
        </div>
    </div>
</section>

{{-- Event types strip --}}
<section class="border-b border-line bg-white">
    <div class="site-container grid gap-6 py-10 sm:grid-cols-3">
        <div>
            <div class="font-display text-lg uppercase text-crimson">Open house</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Tour a branch, meet coaches, and try a beginner session with your family.</p>
        </div>
        <div>
            <div class="font-display text-lg uppercase text-crimson">Belt grading</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Scheduled assessment days with clear syllabi, examiner feedback, and certificates.</p>
        </div>
        <div>
            <div class="font-display text-lg uppercase text-crimson">Clinics</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Sparring, poomsae polish, and competition prep for students ready for the next level.</p>
        </div>
    </div>
</section>

<section id="upcoming" class="bg-paper py-20 lg:py-24">
    <div class="site-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-label">Coming up</p>
                <h2 class="section-title mt-3">Upcoming events</h2>
            </div>
            <p class="max-w-sm text-sm text-stoneish">{{ $upcoming->count() }} event{{ $upcoming->count() === 1 ? '' : 's' }} on the calendar</p>
        </div>

        <div class="mt-12 grid gap-6 lg:grid-cols-2">
            @forelse ($upcoming as $event)
                @php
                    $image = $event->image_path
                        ? asset('storage/'.$event->image_path)
                        : DemoMedia::event();
                @endphp
                <a href="{{ route('events.show', $event) }}" class="group grid overflow-hidden border border-line bg-white transition hover:border-crimson sm:grid-cols-12">
                    <div class="relative aspect-[16/11] overflow-hidden sm:col-span-5 sm:aspect-auto">
                        <img src="{{ $image }}" alt="{{ $event->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                        <div class="absolute left-4 top-4 bg-crimson px-3 py-2 text-center text-white">
                            <div class="font-display text-2xl leading-none">{{ $event->start_date->format('d') }}</div>
                            <div class="mt-0.5 text-[10px] font-semibold uppercase tracking-[0.14em]">{{ $event->start_date->format('M') }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center p-6 sm:col-span-7 sm:p-8">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-crimson">
                            {{ $event->start_date->format('l') }}
                            @if ($event->start_time)
                                · {{ \Illuminate\Support\Str::of($event->start_time)->substr(0, 5) }}
                            @endif
                        </div>
                        <h3 class="font-display mt-2 text-2xl uppercase text-ink transition group-hover:text-crimson sm:text-3xl">{{ $event->title }}</h3>
                        @if ($event->venue)
                            <p class="mt-3 text-sm text-stoneish">{{ $event->venue }}</p>
                        @endif
                        <p class="mt-3 text-sm leading-relaxed text-stoneish">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 120) }}</p>
                        <span class="mt-5 inline-flex text-xs font-semibold uppercase tracking-[0.16em] text-crimson">Event details →</span>
                    </div>
                </a>
            @empty
                <div class="border border-line bg-white p-10 sm:col-span-2">
                    <p class="font-display text-2xl uppercase text-ink">No upcoming events right now</p>
                    <p class="mt-3 max-w-lg text-sm text-stoneish">Follow our calendar for the next open house or grading camp — or message us to join the next trial day.</p>
                    <a href="{{ route('contact.create') }}" class="btn-primary mt-6">Contact the academy</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="site-container">
        <div class="max-w-2xl">
            <p class="section-label">Archive</p>
            <h2 class="section-title mt-3">Previous events</h2>
            <p class="section-copy">Past camps and clinics — browse photos in the gallery after each weekend.</p>
        </div>

        <div class="mt-10 divide-y divide-line border-y border-line">
            @forelse ($past as $event)
                @php
                    $image = $event->image_path
                        ? asset('storage/'.$event->image_path)
                        : DemoMedia::event();
                @endphp
                <a href="{{ route('events.show', $event) }}" class="group grid gap-6 py-6 transition hover:bg-paper/80 sm:grid-cols-12 sm:items-center sm:px-4">
                    <div class="sm:col-span-2">
                        <div class="font-display text-3xl uppercase text-crimson">{{ $event->start_date->format('d') }}</div>
                        <div class="text-xs font-semibold uppercase tracking-[0.14em] text-stoneish">{{ $event->start_date->format('M Y') }}</div>
                    </div>
                    <div class="hidden sm:col-span-2 sm:block">
                        <div class="media-frame aspect-[4/3]">
                            <img src="{{ $image }}" alt="{{ $event->title }}">
                        </div>
                    </div>
                    <div class="sm:col-span-6">
                        <h3 class="font-display text-2xl uppercase text-ink group-hover:text-crimson">{{ $event->title }}</h3>
                        @if ($event->venue)
                            <p class="mt-1 text-sm text-stoneish">{{ $event->venue }}</p>
                        @endif
                    </div>
                    <div class="sm:col-span-2 sm:text-right">
                        <span class="text-xs font-semibold uppercase tracking-[0.16em] text-crimson">View →</span>
                    </div>
                </a>
            @empty
                <p class="py-10 text-stoneish">No previous events listed yet.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $past->links() }}</div>
    </div>
</section>

<section class="bg-ink py-16 text-white">
    <div class="site-container grid gap-8 lg:grid-cols-12 lg:items-center">
        <div class="lg:col-span-7">
            <p class="section-label">Branches</p>
            <h2 class="font-display mt-3 text-3xl uppercase sm:text-4xl">Events run at all three locations</h2>
            <p class="mt-4 max-w-xl text-white/70">Check the event page for the exact venue — or ask which branch is closest for your trial.</p>
        </div>
        <div class="space-y-3 lg:col-span-5">
            @foreach (config('academy.branches') as $branch)
                <div class="border border-white/10 bg-white/5 px-5 py-4">
                    <div class="font-display text-lg uppercase text-white">{{ $branch['name'] }}</div>
                    <div class="mt-1 text-sm text-white/60">{{ $branch['area'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-crimson py-16 text-white">
    <div class="site-container flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
        <div>
            <h2 class="font-display text-3xl uppercase sm:text-4xl">Want a seat at the next open house?</h2>
            <p class="mt-2 max-w-xl text-white/85">Tell us your age group and preferred branch — we’ll confirm the next trial slot.</p>
        </div>
        <a href="{{ route('contact.create') }}" class="btn-ink">Contact the academy</a>
    </div>
</section>
@endsection
