@php use App\Support\DemoMedia; @endphp
@extends('layouts.public')

@section('title', 'Wowz Martial Art — Academy in Chandigarh')

@section('content')
{{-- Hero --}}
<section class="relative min-h-[88svh] overflow-hidden bg-ink text-white">
    <img
        src="{{ asset('storage/services/taekwondo.jpg') }}"
        alt="Taekwondo training at Wowz Martial Art"
        class="kenburns absolute inset-0 h-full w-full object-cover"
    >
    <div class="absolute inset-0 bg-gradient-to-r from-ink/95 via-ink/75 to-ink/35"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-transparent to-ink/30"></div>

    <div class="site-container relative flex min-h-[88svh] flex-col justify-center py-28">
        <p class="reveal section-label !text-crimson">Taekwondo Academy · Chandigarh</p>
        <h1 class="reveal-delay font-display mt-4 max-w-4xl text-5xl font-semibold uppercase leading-[0.95] sm:text-6xl lg:text-7xl">
            Learn to <span class="text-crimson">defend</span>.<br>
            Train to <span class="text-crimson">win</span>.
        </h1>
        <p class="reveal-delay-2 mt-6 max-w-xl text-lg leading-relaxed text-white/80">
            Wowz Martial Art teaches Olympic-style taekwondo, kids martial arts, kickboxing, and self-defence — with belt grading and competition coaching across Chandigarh & Zirakpur.
        </p>
        <div class="reveal-delay-2 mt-9 flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-primary">Book Free Trial</a>
            <a href="{{ route('services.index') }}" class="btn-ghost">View Programs</a>
        </div>
    </div>
</section>

{{-- Benefits strip inspired by Royal MMA --}}
<section class="border-b border-line bg-white">
    <div class="site-container grid gap-8 py-12 sm:grid-cols-2 lg:grid-cols-4">
        <div class="reveal">
            <div class="font-display text-xl uppercase text-crimson">Self Defence</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Practical techniques for awareness, reaction, and real-world confidence.</p>
        </div>
        <div class="reveal-delay">
            <div class="font-display text-xl uppercase text-crimson">Discipline</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Structured classes that build focus, respect, and daily consistency.</p>
        </div>
        <div class="reveal-delay">
            <div class="font-display text-xl uppercase text-crimson">Fitness</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Strength, mobility, and conditioning built into every training session.</p>
        </div>
        <div class="reveal-delay-2">
            <div class="font-display text-xl uppercase text-crimson">Confidence</div>
            <p class="mt-2 text-sm leading-relaxed text-stoneish">Belt grading and honest coaching so progress is visible and motivating.</p>
        </div>
    </div>
</section>

{{-- About --}}
<section class="bg-paper py-20 lg:py-24">
    <div class="site-container grid gap-12 lg:grid-cols-12 lg:items-center">
        <div class="media-frame aspect-[4/5] lg:col-span-5">
            <img src="{{ asset('storage/services/kids.jpg') }}" alt="Kids taekwondo class at Wowz Martial Art">
        </div>
        <div class="lg:col-span-7">
            <p class="section-label">About the academy</p>
            <h2 class="section-title mt-3">Train with certified coaches in Chandigarh</h2>
            <p class="section-copy">{{ $about->get('who_we_are')?->content }}</p>

            <div class="mt-10 grid gap-6 sm:grid-cols-2">
                <div class="border-l-2 border-crimson pl-4">
                    <h3 class="font-display text-lg uppercase text-ink">{{ $about->get('how_we_do')?->title ?? 'How we train' }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-stoneish">{{ $about->get('how_we_do')?->content }}</p>
                </div>
                <div class="border-l-2 border-crimson pl-4">
                    <h3 class="font-display text-lg uppercase text-ink">{{ $about->get('goals')?->title ?? 'Our goals' }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-stoneish">{{ $about->get('goals')?->content }}</p>
                </div>
            </div>

            <a href="{{ route('about') }}" class="btn-outline mt-10">Learn more about us</a>
        </div>
    </div>
</section>

{{-- Programs as photo overlay cards (Shastrang style) --}}
<section class="bg-white py-20 lg:py-24">
    <div class="site-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-label">Our classes</p>
                <h2 class="section-title mt-3">Choose your program</h2>
                <p class="section-copy">Whether you want fitness, self-defence, or competition prep — there is a clear path for every age group.</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn-outline">All programs</a>
        </div>

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($services as $service)
                @php
                    $image = $service->image_path
                        ? asset('storage/'.$service->image_path)
                        : DemoMedia::service($service->slug);
                @endphp
                <a href="{{ route('services.show', $service) }}" class="program-card aspect-[4/5]">
                    <img src="{{ $image }}" alt="{{ $service->name }}">
                    <div class="program-card-overlay"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                        <h3 class="font-display text-2xl uppercase">{{ $service->name }}</h3>
                        <p class="mt-2 text-sm text-white/75">{{ \Illuminate\Support\Str::limit($service->description, 80) }}</p>
                        <span class="mt-4 inline-block text-xs font-semibold uppercase tracking-[0.16em] text-crimson">View program →</span>
                    </div>
                </a>
            @empty
                <p class="text-stoneish">Programs will appear here once published.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- Stats band --}}
<section class="bg-ink py-16 text-white">
    <div class="site-container grid gap-8 text-center sm:grid-cols-3">
        <div>
            <div class="font-display text-5xl uppercase text-crimson" data-count="12" data-count-suffix="+">0+</div>
            <p class="mt-2 text-sm uppercase tracking-[0.16em] text-white/60">Years coaching</p>
        </div>
        <div>
            <div class="font-display text-5xl uppercase text-crimson" data-count="100" data-count-suffix="+">0+</div>
            <p class="mt-2 text-sm uppercase tracking-[0.16em] text-white/60">Active students</p>
        </div>
        <div>
            <div class="font-display text-5xl uppercase text-crimson" data-count="6">0</div>
            <p class="mt-2 text-sm uppercase tracking-[0.16em] text-white/60">Training programs</p>
        </div>
    </div>
</section>

{{-- Schedule --}}
<section class="bg-paper py-20 lg:py-24">
    <div class="site-container">
        <p class="section-label">Weekly timetable</p>
        <h2 class="section-title mt-3">Sample class schedule</h2>
        <p class="section-copy">Demo batches — ask reception for the current sheet and trial timings.</p>

        <div class="mt-10 overflow-hidden border border-line bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-ink text-white">
                        <tr>
                            <th class="px-5 py-4 font-display text-xs uppercase tracking-[0.16em]">Batch</th>
                            <th class="px-5 py-4 font-display text-xs uppercase tracking-[0.16em]">Days</th>
                            <th class="px-5 py-4 font-display text-xs uppercase tracking-[0.16em]">Time</th>
                            <th class="px-5 py-4 font-display text-xs uppercase tracking-[0.16em]">Coach</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line text-stoneish">
                        <tr class="hover:bg-paper"><td class="px-5 py-4 font-medium text-ink">Kids Martial Arts</td><td class="px-5 py-4">Mon · Wed · Fri</td><td class="px-5 py-4">5:00 – 6:00 PM</td><td class="px-5 py-4">Sensei Rohan Das</td></tr>
                        <tr class="hover:bg-paper"><td class="px-5 py-4 font-medium text-ink">Taekwondo (Teens)</td><td class="px-5 py-4">Tue · Thu · Sat</td><td class="px-5 py-4">6:00 – 7:15 PM</td><td class="px-5 py-4">Coach Arjun Singh</td></tr>
                        <tr class="hover:bg-paper"><td class="px-5 py-4 font-medium text-ink">Kickboxing</td><td class="px-5 py-4">Mon · Wed · Fri</td><td class="px-5 py-4">7:30 – 8:45 PM</td><td class="px-5 py-4">Coach Priya Nair</td></tr>
                        <tr class="hover:bg-paper"><td class="px-5 py-4 font-medium text-ink">Adult Fitness</td><td class="px-5 py-4">Tue · Thu</td><td class="px-5 py-4">7:00 – 8:00 AM</td><td class="px-5 py-4">Coach Meera Kapoor</td></tr>
                        <tr class="hover:bg-paper"><td class="px-5 py-4 font-medium text-ink">Yoga & Recovery</td><td class="px-5 py-4">Saturday</td><td class="px-5 py-4">8:00 – 9:00 AM</td><td class="px-5 py-4">Coach Meera Kapoor</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@if ($nextEvent)
<section class="bg-white py-20 lg:py-24">
    <div class="site-container grid gap-10 lg:grid-cols-2 lg:items-center">
        <div class="media-frame aspect-[16/10]">
            <img
                src="{{ $nextEvent->image_path ? asset('storage/'.$nextEvent->image_path) : DemoMedia::event() }}"
                alt="{{ $nextEvent->title }}"
            >
        </div>
        <div>
            <p class="section-label">Upcoming event</p>
            <h2 class="section-title mt-3">{{ $nextEvent->title }}</h2>
            <p class="mt-4 font-display text-sm uppercase tracking-[0.14em] text-crimson">
                {{ $nextEvent->start_date->format('d M Y') }}
                @if ($nextEvent->start_time) · {{ \Illuminate\Support\Str::of($nextEvent->start_time)->substr(0, 5) }} @endif
                · {{ $nextEvent->venue }}
            </p>
            <p class="mt-5 text-stoneish">{{ \Illuminate\Support\Str::limit($nextEvent->description, 180) }}</p>
            <a href="{{ route('events.show', $nextEvent) }}" class="btn-primary mt-8">Event details</a>
        </div>
    </div>
</section>
@endif

{{-- Gallery --}}
<section class="bg-paper py-20 lg:py-24">
    <div class="site-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-label">Gallery</p>
                <h2 class="section-title mt-3">Life on the mat</h2>
            </div>
            <a href="{{ route('gallery.index') }}" class="btn-outline">Full gallery</a>
        </div>
        <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($gallery as $image)
                <figure class="media-frame aspect-[4/3]">
                    <img src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $image->title }}">
                </figure>
            @empty
                @foreach (range(0, 5) as $index)
                    <div class="media-frame aspect-[4/3]">
                        <img src="{{ DemoMedia::gallery($index) }}" alt="Academy training">
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

@if ($achievements->isNotEmpty())
<section class="bg-white py-20 lg:py-24">
    <div class="site-container">
        <p class="section-label">Results</p>
        <h2 class="section-title mt-3">Recent achievements</h2>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($achievements as $achievement)
                <article class="border border-line bg-paper p-6">
                    <div class="h-1 w-12 bg-crimson"></div>
                    <h3 class="font-display mt-5 text-xl uppercase text-ink">{{ $achievement->title }}</h3>
                    <p class="mt-2 text-xs uppercase tracking-[0.14em] text-stoneish">
                        {{ $achievement->competition_name }}
                        @if ($achievement->position) · {{ $achievement->position }} @endif
                    </p>
                    <p class="mt-4 text-sm leading-relaxed text-stoneish">{{ \Illuminate\Support\Str::limit($achievement->description, 110) }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Testimonials --}}
<section class="bg-ink py-20 text-white lg:py-24">
    <div class="site-container">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title mt-3 !text-white">Words from our members</h2>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @forelse ($testimonials as $testimonial)
                <blockquote class="border border-white/10 bg-white/5 p-6">
                    <p class="text-base leading-relaxed text-white/85">“{{ $testimonial->content }}”</p>
                    <footer class="mt-6">
                        <div class="font-display text-sm uppercase tracking-[0.14em] text-crimson">{{ $testimonial->author_name }}</div>
                        @if ($testimonial->author_title)
                            <div class="mt-1 text-sm text-white/50">{{ $testimonial->author_title }}</div>
                        @endif
                    </footer>
                </blockquote>
            @empty
                <p class="text-white/60">Testimonials coming soon.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- Black Belts --}}
@if ($blackBelts->isNotEmpty())
<section class="bg-ink py-20 text-white lg:py-24">
    <div class="site-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-label">Hall of honour</p>
                <h2 class="section-title mt-3 !text-white">Our Black Belts</h2>
                <p class="mt-4 max-w-xl text-white/65">Students promoted to dan rank through discipline, grading, and years on the mat.</p>
            </div>
            <a href="{{ route('black-belts.index') }}" class="btn-ghost">View all black belts</a>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($blackBelts as $index => $member)
                <article>
                    <div class="media-frame aspect-[4/5]">
                        @if ($member->photo_path)
                            <img src="{{ asset('storage/'.$member->photo_path) }}" alt="{{ $member->name }}">
                        @else
                            <img src="{{ DemoMedia::blackBelt($index) }}" alt="{{ $member->name }}">
                        @endif
                    </div>
                    <div class="mt-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-crimson">{{ $member->rank }}</div>
                    <h3 class="font-display mt-1 text-xl uppercase text-white">{{ $member->name }}</h3>
                    @if ($member->promoted_on)
                        <p class="mt-1 text-sm text-white/55">Promoted {{ $member->promoted_on->format('Y') }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Coaches --}}
<section class="bg-white py-20 lg:py-24">
    <div class="site-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-label">Meet your instructors</p>
                <h2 class="section-title mt-3">Led by experience</h2>
            </div>
            <a href="{{ route('about.team') }}" class="btn-outline">Meet the team</a>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($team as $index => $member)
                <article class="group">
                    <div class="media-frame aspect-[4/5]">
                        @if ($member->photo_path)
                            <img src="{{ asset('storage/'.$member->photo_path) }}" alt="{{ $member->name }}">
                        @else
                            <img src="{{ DemoMedia::team($index) }}" alt="{{ $member->name }}">
                        @endif
                    </div>
                    <h3 class="font-display mt-4 text-xl uppercase text-ink group-hover:text-crimson">{{ $member->name }}</h3>
                    <p class="mt-1 text-sm font-medium text-crimson">{{ $member->designation }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Contact CTA --}}
<section class="relative overflow-hidden bg-crimson py-16 text-white">
    <div class="site-container relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
        <div>
            <p class="font-display text-xs uppercase tracking-[0.22em] text-white/70">Start this week</p>
            <h2 class="font-display mt-2 text-3xl uppercase sm:text-4xl">Ready for your first class?</h2>
            <p class="mt-2 max-w-xl text-white/85">Tell us your age group and preferred program — we’ll suggest a trial slot that fits your schedule.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-ink">Contact the academy</a>
            <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone')) }}" class="btn-ghost">Call {{ config('academy.phone') }}</a>
        </div>
    </div>
</section>
@endsection
