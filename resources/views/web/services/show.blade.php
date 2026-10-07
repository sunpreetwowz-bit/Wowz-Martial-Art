@php
    use App\Support\DemoMedia;
    use App\Support\ProgramDetails;
    $image = $service->image_path
        ? asset('storage/'.$service->image_path)
        : DemoMedia::service($service->slug);
    $details = ProgramDetails::for($service->slug);
@endphp
@extends('layouts.public')

@section('title', $service->name.' — Wowz Martial Art')

@section('content')
<section class="relative min-h-[70svh] overflow-hidden bg-ink text-white">
    <img src="{{ $image }}" alt="{{ $service->name }}" class="kenburns absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/80 to-ink/35"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-transparent to-ink/40"></div>

    <div class="site-container relative flex min-h-[70svh] flex-col justify-end pb-16 pt-32">
        <a href="{{ route('services.index') }}" class="text-sm text-white/70 hover:text-white">← All programs</a>
        <p class="section-label mt-6 !text-crimson">Program</p>
        <h1 class="font-display mt-3 max-w-3xl text-5xl uppercase leading-[0.95] sm:text-6xl">{{ $service->name }}</h1>
        <p class="mt-5 max-w-2xl text-lg text-white/80">{{ $details['tagline'] }}</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('contact.create') }}" class="btn-primary">{{ $details['cta'] }}</a>
            <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone')) }}" class="btn-ghost">Call {{ config('academy.phone') }}</a>
        </div>
    </div>
</section>

{{-- Quick facts --}}
<section class="border-b border-line bg-white">
    <div class="site-container grid gap-6 py-10 sm:grid-cols-3">
        <div>
            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-stoneish">Who it’s for</div>
            <p class="mt-2 text-sm leading-relaxed text-ink">{{ $details['who_for'] }}</p>
        </div>
        <div>
            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-stoneish">Class length</div>
            <p class="mt-2 font-display text-xl uppercase text-ink">{{ $details['duration'] }}</p>
        </div>
        <div>
            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-stoneish">Level</div>
            <p class="mt-2 font-display text-xl uppercase text-ink">{{ $details['level'] }}</p>
        </div>
    </div>
</section>

{{-- Overview --}}
<section class="bg-paper py-20 lg:py-24">
    <div class="site-container grid gap-12 lg:grid-cols-12 lg:items-start">
        <div class="lg:col-span-7">
            <p class="section-label">Overview</p>
            <h2 class="section-title mt-3">What this program covers</h2>
            <p class="section-copy whitespace-pre-line">{{ $service->description }}</p>
            <p class="mt-6 text-base leading-relaxed text-stoneish">
                At Wowz Martial Art, every class is coach-led with a clear plan: warm-up, technique, partner or pad work, then cool-down. Progress is tracked through attendance, feedback, and belt grading when you are ready — at Sector 46, Sector 27, or Zirakpur.
            </p>
        </div>
        <div class="lg:col-span-5">
            <div class="media-frame aspect-[4/3]">
                <img src="{{ $image }}" alt="{{ $service->name }} training">
            </div>
            <div class="mt-6 border border-line bg-white p-6">
                <h3 class="font-display text-lg uppercase text-ink">Ready to start?</h3>
                <p class="mt-2 text-sm text-stoneish">Tell us your age group and preferred batch — we’ll suggest a trial slot.</p>
                <a href="{{ route('contact.create') }}" class="btn-primary mt-5 w-full sm:w-auto">{{ $details['cta'] }}</a>
            </div>
        </div>
    </div>
</section>

{{-- What you'll learn + benefits --}}
<section class="bg-white py-20 lg:py-24">
    <div class="site-container grid gap-10 lg:grid-cols-2">
        <div>
            <p class="section-label">Curriculum</p>
            <h2 class="font-display mt-3 text-3xl uppercase text-ink">What you’ll learn</h2>
            <ul class="mt-8 space-y-4">
                @foreach ($details['learn'] as $item)
                    <li class="flex gap-3 border-b border-line pb-4 text-sm leading-relaxed text-ink">
                        <span class="mt-0.5 font-display text-crimson">{{ $loop->iteration < 10 ? '0'.$loop->iteration : $loop->iteration }}</span>
                        <span>{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>
            <p class="section-label">Outcomes</p>
            <h2 class="font-display mt-3 text-3xl uppercase text-ink">Benefits</h2>
            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                @foreach ($details['benefits'] as $benefit)
                    <div class="border border-line bg-paper p-5">
                        <div class="h-1 w-8 bg-crimson"></div>
                        <p class="mt-4 text-sm leading-relaxed text-ink">{{ $benefit }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Sample schedule --}}
<section class="bg-ink py-20 text-white lg:py-24">
    <div class="site-container">
        <p class="section-label">Timetable</p>
        <h2 class="section-title mt-3 !text-white">Sample batches</h2>
        <p class="mt-4 max-w-2xl text-white/65">Demo schedule — ask reception for the current sheet and open trial slots.</p>
        <div class="mt-10 overflow-hidden border border-white/10">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-[11px] uppercase tracking-[0.16em] text-white/45">
                    <tr>
                        <th class="px-5 py-4 font-display">Batch</th>
                        <th class="px-5 py-4 font-display">Days</th>
                        <th class="px-5 py-4 font-display">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($details['schedule'] as $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-5 py-4 font-medium text-white">{{ $row['batch'] }}</td>
                            <td class="px-5 py-4 text-white/70">{{ $row['days'] }}</td>
                            <td class="px-5 py-4 text-white/70">{{ $row['time'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Related programs --}}
@if ($related->isNotEmpty())
<section class="bg-paper py-20 lg:py-24">
    <div class="site-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-label">More programs</p>
                <h2 class="section-title mt-3">You may also like</h2>
            </div>
            <a href="{{ route('services.index') }}" class="btn-outline">All programs</a>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($related as $item)
                @php
                    $relatedImage = $item->image_path
                        ? asset('storage/'.$item->image_path)
                        : DemoMedia::service($item->slug);
                @endphp
                <a href="{{ route('services.show', $item) }}" class="program-card aspect-[4/5]">
                    <img src="{{ $relatedImage }}" alt="{{ $item->name }}">
                    <div class="program-card-overlay"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                        <h3 class="font-display text-2xl uppercase">{{ $item->name }}</h3>
                        <p class="mt-2 text-sm text-white/75">{{ \Illuminate\Support\Str::limit($item->description, 70) }}</p>
                        <span class="mt-4 inline-block text-xs font-semibold uppercase tracking-[0.16em] text-crimson">View program →</span>
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
            <h2 class="font-display text-3xl uppercase sm:text-4xl">Start {{ $service->name }} this week</h2>
            <p class="mt-2 max-w-xl text-white/85">Visit Sector 46, Sector 27, or Zirakpur for a trial — bring comfortable clothes; we provide pads and guidance on the floor.</p>
        </div>
        <a href="{{ route('contact.create') }}" class="btn-ink">{{ $details['cta'] }}</a>
    </div>
</section>
@endsection
