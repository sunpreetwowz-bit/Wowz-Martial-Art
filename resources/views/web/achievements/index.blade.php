@extends('layouts.public')

@section('title', 'Results — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="font-display text-sm uppercase tracking-[0.25em] text-crimson">Results</p>
        <h1 class="font-display mt-3 text-5xl uppercase">Achievements</h1>
        <p class="mt-4 max-w-2xl text-white/75">Medals, team placements, and academy milestones earned by our students.</p>
    </div>
</section>

<section class="bg-paper py-16">
    <div class="site-container space-y-10">
        @forelse ($achievements as $achievement)
            <article class="border-t-4 border-crimson pt-5">
                <h2 class="font-display text-3xl uppercase text-ink">{{ $achievement->title }}</h2>
                <p class="mt-2 text-sm text-stoneish">
                    {{ $achievement->competition_name }}
                    @if ($achievement->position) · {{ $achievement->position }} @endif
                    @if ($achievement->achieved_on) · {{ $achievement->achieved_on->format('M Y') }} @endif
                </p>
                <p class="mt-3 max-w-3xl text-stoneish">{{ $achievement->description }}</p>
            </article>
        @empty
            <p class="text-stoneish">No published achievements yet.</p>
        @endforelse
        <div>{{ $achievements->links() }}</div>
    </div>
</section>
@endsection
