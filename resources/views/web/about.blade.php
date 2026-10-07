@php use App\Support\DemoMedia; @endphp
@extends('layouts.public')

@section('title', 'About — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="section-label">About the academy</p>
        <h1 class="font-display mt-3 text-5xl uppercase sm:text-6xl">Discipline. Respect.<br>Measurable progress.</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/75">
            Wowz Martial Art trains kids, teens, and adults across Chandigarh & Zirakpur — from first white belt to competition floor.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-primary">Book a trial class</a>
            <a href="{{ route('about.team') }}" class="btn-ghost">Meet the coaches</a>
        </div>
    </div>
</section>

{{-- Intro with image --}}
<section class="bg-white py-20 lg:py-24">
    <div class="site-container grid gap-12 lg:grid-cols-12 lg:items-center">
        <div class="media-frame aspect-[4/5] lg:col-span-5">
            <img src="{{ asset('storage/services/taekwondo.jpg') }}" alt="Taekwondo training at Wowz Martial Art">
        </div>
        <div class="lg:col-span-7">
            <p class="section-label">Who we are</p>
            <h2 class="section-title mt-3">A neighbourhood dojo with serious standards</h2>
            <p class="section-copy">
                {{ $sections->firstWhere('key', 'who_we_are')?->content
                    ?? 'We teach Olympic-style taekwondo, kids martial arts, kickboxing, and self-defence with clear belt progress and respectful coaching.' }}
            </p>
            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                <div class="border-t-2 border-crimson pt-4">
                    <div class="font-display text-3xl uppercase text-ink" data-count="12" data-count-suffix="+">0+</div>
                    <p class="mt-1 text-sm text-stoneish">Years of coaching experience</p>
                </div>
                <div class="border-t-2 border-crimson pt-4">
                    <div class="font-display text-3xl uppercase text-ink" data-count="100" data-count-suffix="+">0+</div>
                    <p class="mt-1 text-sm text-stoneish">Active students on the mats</p>
                </div>
                <div class="border-t-2 border-crimson pt-4">
                    <div class="font-display text-3xl uppercase text-ink" data-count="6">0</div>
                    <p class="mt-1 text-sm text-stoneish">Programs for every age group</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- How we train + goals --}}
<section class="bg-paper py-20 lg:py-24">
    <div class="site-container grid gap-8 lg:grid-cols-2">
        <article class="border border-line bg-white p-8">
            <p class="section-label">How we train</p>
            <h2 class="font-display mt-3 text-3xl uppercase text-ink">
                {{ $sections->firstWhere('key', 'how_we_do')?->title ?? 'How we train' }}
            </h2>
            <p class="mt-4 text-base leading-relaxed text-stoneish">
                {{ $sections->firstWhere('key', 'how_we_do')?->content }}
            </p>
            <ul class="mt-6 space-y-3 text-sm text-ink">
                <li class="flex gap-3"><span class="text-crimson">●</span> Warm-up → technique → partner drills → cool-down</li>
                <li class="flex gap-3"><span class="text-crimson">●</span> Belt tests scheduled in advance with clear syllabi</li>
                <li class="flex gap-3"><span class="text-crimson">●</span> Online applications and payment tracking</li>
                <li class="flex gap-3"><span class="text-crimson">●</span> Honest coach feedback after every grading cycle</li>
            </ul>
        </article>
        <article class="border border-line bg-white p-8">
            <p class="section-label">Our goals</p>
            <h2 class="font-display mt-3 text-3xl uppercase text-ink">
                {{ $sections->firstWhere('key', 'goals')?->title ?? 'Our goals' }}
            </h2>
            <p class="mt-4 text-base leading-relaxed text-stoneish">
                {{ $sections->firstWhere('key', 'goals')?->content }}
            </p>
            <ul class="mt-6 space-y-3 text-sm text-ink">
                <li class="flex gap-3"><span class="text-crimson">●</span> Clean technique and sportsmanship</li>
                <li class="flex gap-3"><span class="text-crimson">●</span> Confidence without ego on the floor</li>
                <li class="flex gap-3"><span class="text-crimson">●</span> Ready for belt exams and district events</li>
                <li class="flex gap-3"><span class="text-crimson">●</span> A safe, respectful training family</li>
            </ul>
        </article>
    </div>
</section>

{{-- Mission / vision / philosophy --}}
<section class="bg-ink py-20 text-white lg:py-24">
    <div class="site-container">
        <p class="section-label">Foundation</p>
        <h2 class="section-title mt-3 !text-white">Mission, vision & philosophy</h2>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach (['mission', 'vision', 'philosophy'] as $key)
                @php $section = $sections->firstWhere('key', $key); @endphp
                @if ($section)
                    <article class="border border-white/10 bg-white/5 p-6">
                        <h3 class="font-display text-xl uppercase text-crimson">{{ $section->title }}</h3>
                        <p class="mt-4 text-sm leading-relaxed text-white/75">{{ $section->content }}</p>
                    </article>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- Why train here --}}
<section class="bg-white py-20 lg:py-24">
    <div class="site-container">
        <div class="max-w-2xl">
            <p class="section-label">Why Wowz</p>
            <h2 class="section-title mt-3">What you get on the mat</h2>
        </div>
        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="font-display text-xl uppercase text-crimson">Certified coaches</div>
                <p class="mt-2 text-sm leading-relaxed text-stoneish">4th Dan taekwondo leadership, kids specialists, and kickboxing coaches who teach with patience.</p>
            </div>
            <div>
                <div class="font-display text-xl uppercase text-crimson">Belt pathway</div>
                <p class="mt-2 text-sm leading-relaxed text-stoneish">Clear syllabus from white belt upward, with scheduled grading camps and certificates.</p>
            </div>
            <div>
                <div class="font-display text-xl uppercase text-crimson">Competition support</div>
                <p class="mt-2 text-sm leading-relaxed text-stoneish">District and state prep, sparring clinics, and help with event registration paperwork.</p>
            </div>
            <div>
                <div class="font-display text-xl uppercase text-crimson">3 branch locations</div>
                <p class="mt-2 text-sm leading-relaxed text-stoneish">Sector 46, Sector 27, and Zirakpur — morning, evening, and weekend batches for busy families.</p>
            </div>
        </div>
    </div>
</section>

{{-- Team preview --}}
<section class="bg-paper py-20 lg:py-24">
    <div class="site-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-label">Coaching staff</p>
                <h2 class="section-title mt-3">Led by experience</h2>
            </div>
            <a href="{{ route('about.team') }}" class="btn-outline">Full team page</a>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($team->take(4) as $index => $member)
                <article>
                    <div class="media-frame aspect-[4/5]">
                        @if ($member->photo_path)
                            <img src="{{ asset('storage/'.$member->photo_path) }}" alt="{{ $member->name }}">
                        @else
                            <img src="{{ DemoMedia::team($index) }}" alt="{{ $member->name }}">
                        @endif
                    </div>
                    <h3 class="font-display mt-4 text-xl uppercase text-ink">{{ $member->name }}</h3>
                    <p class="mt-1 text-sm font-medium text-crimson">{{ $member->designation }}</p>
                    <p class="mt-2 text-sm text-stoneish">{{ \Illuminate\Support\Str::limit($member->biography, 90) }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-crimson py-16 text-white">
    <div class="site-container flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
        <div>
            <h2 class="font-display text-3xl uppercase sm:text-4xl">Ready to visit the dojo?</h2>
            <p class="mt-2 max-w-xl text-white/85">Tell us your age group and preferred program — we’ll suggest a trial slot that fits.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-ink">Contact the academy</a>
            <a href="{{ route('services.index') }}" class="btn-ghost">View programs</a>
        </div>
    </div>
</section>
@endsection
