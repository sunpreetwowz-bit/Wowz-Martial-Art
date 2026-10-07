@php use App\Support\DemoMedia; @endphp
@extends('layouts.public')

@section('title', 'Team — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="section-label">Coaching staff</p>
        <h1 class="font-display mt-3 text-5xl uppercase sm:text-6xl">Meet the coaches</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/75">Instructors who lead by example — on the mat and in character.</p>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="site-container grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($team as $index => $member)
            <article>
                <div class="media-frame aspect-[4/5]">
                    @if ($member->photo_path)
                        <img src="{{ asset('storage/'.$member->photo_path) }}" alt="{{ $member->name }}">
                    @else
                        <img src="{{ DemoMedia::team($index) }}" alt="{{ $member->name }}">
                    @endif
                </div>
                <h2 class="font-display mt-5 text-2xl uppercase text-ink">{{ $member->name }}</h2>
                <p class="mt-1 text-sm font-medium text-crimson">{{ $member->designation }}</p>
                <p class="mt-3 text-sm leading-relaxed text-stoneish">{{ $member->biography }}</p>
            </article>
        @empty
            <p class="text-stoneish">No team members published yet.</p>
        @endforelse
    </div>
</section>

<section class="bg-crimson py-16 text-white">
    <div class="site-container flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
        <div>
            <h2 class="font-display text-3xl uppercase sm:text-4xl">Train with our coaches</h2>
            <p class="mt-2 max-w-xl text-white/85">Book a trial and meet the instructor for your age group.</p>
        </div>
        <a href="{{ route('contact.create') }}" class="btn-ink">Book a trial class</a>
    </div>
</section>
@endsection
