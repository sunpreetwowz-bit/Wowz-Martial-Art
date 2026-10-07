@php use App\Support\DemoMedia; @endphp
@extends('layouts.public')

@section('title', 'Our Black Belts — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="section-label">Hall of honour</p>
        <h1 class="font-display mt-3 text-5xl uppercase sm:text-6xl">Our Black Belts</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/75">
            Students who earned their black belt through years of discipline, grading, and dedication on the mat.
        </p>
    </div>
</section>

<section class="border-b border-line bg-white">
    <div class="site-container grid gap-6 py-10 sm:grid-cols-3">
        <div>
            <div class="font-display text-lg uppercase text-crimson">Earned, not given</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Every black belt listed here completed the full syllabus, belt tests, and coach review.</p>
        </div>
        <div>
            <div class="font-display text-lg uppercase text-crimson">Across our branches</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Promotions from Sector 46, Sector 27, and Zirakpur — one academy standard.</p>
        </div>
        <div>
            <div class="font-display text-lg uppercase text-crimson">Path for every student</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">White belt to black belt is a journey. Your first class is the first step.</p>
        </div>
    </div>
</section>

<section class="bg-paper py-20 lg:py-24">
    <div class="site-container">
        <div class="max-w-2xl">
            <p class="section-label">Promoted students</p>
            <h2 class="section-title mt-3">Black belt family</h2>
            <p class="section-copy">Meet the students who reached dan rank at Wowz Martial Art.</p>
        </div>

        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($blackBelts as $index => $member)
                <article class="border border-line bg-white">
                    <div class="media-frame aspect-[4/5]">
                        @if ($member->photo_path)
                            <img src="{{ asset('storage/'.$member->photo_path) }}" alt="{{ $member->name }}">
                        @else
                            <img src="{{ DemoMedia::blackBelt($index) }}" alt="{{ $member->name }}">
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-crimson">{{ $member->rank }}</div>
                        <h3 class="font-display mt-2 text-2xl uppercase text-ink">{{ $member->name }}</h3>
                        <p class="mt-2 text-sm text-stoneish">
                            @if ($member->promoted_on)
                                Promoted {{ $member->promoted_on->format('M Y') }}
                            @endif
                            @if ($member->branch)
                                <span class="text-line"> · </span>{{ $member->branch }}
                            @endif
                        </p>
                        @if ($member->biography)
                            <p class="mt-4 text-sm leading-relaxed text-stoneish">{{ $member->biography }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="border border-line bg-white p-10 sm:col-span-2 lg:col-span-3">
                    <p class="font-display text-2xl uppercase text-ink">Black belt profiles coming soon</p>
                    <p class="mt-3 max-w-lg text-sm text-stoneish">Ask our coaches about the dan pathway — or book a trial to start your own journey.</p>
                    <a href="{{ route('contact.create') }}" class="btn-primary mt-6">Book a trial class</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="bg-crimson py-16 text-white">
    <div class="site-container flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
        <div>
            <h2 class="font-display text-3xl uppercase sm:text-4xl">Start your belt journey</h2>
            <p class="mt-2 max-w-xl text-white/85">From white belt onward — clear syllabus, honest coaching, and grading when you are ready.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-ink">Book a trial</a>
            <a href="{{ route('services.index') }}" class="btn-ghost">View programs</a>
        </div>
    </div>
</section>
@endsection
