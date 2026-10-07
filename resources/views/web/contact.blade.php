@extends('layouts.public')

@section('title', 'Contact — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="section-label">Contact</p>
        <h1 class="font-display mt-3 text-5xl uppercase sm:text-6xl">Book a trial class</h1>
        <p class="mt-4 max-w-2xl text-lg text-white/75">
            Three training locations in Chandigarh & Zirakpur. Tell us your age group, preferred program, and branch — we usually reply within 1–2 business days.
        </p>
        <div class="mt-8">
            <a href="#branch-maps" class="btn-ghost">View map locations</a>
        </div>
    </div>
</section>

{{-- Branches --}}
<section class="bg-white py-16 lg:py-20">
    <div class="site-container">
        <div class="max-w-2xl">
            <p class="section-label">Our branches</p>
            <h2 class="section-title mt-3">Train where it suits you</h2>
            <p class="section-copy">Same coaching standards across all three floors — pick the location closest to home or school.</p>
        </div>

        <div class="mt-12 grid gap-6 lg:grid-cols-3">
            @foreach (config('academy.branches') as $index => $branch)
                <article class="border border-line bg-paper p-6 transition hover:border-crimson">
                    <div class="font-display text-sm uppercase tracking-[0.18em] text-crimson">Branch {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                    <h3 class="font-display mt-3 text-2xl uppercase text-ink">{{ $branch['name'] }}</h3>
                    <p class="mt-2 text-sm font-medium text-ink">{{ $branch['area'] }}</p>
                    <p class="mt-4 text-sm leading-relaxed text-stoneish">{{ $branch['address'] }}</p>
                    @if (! empty($branch['note']))
                        <p class="mt-4 border-t border-line pt-4 text-xs font-semibold uppercase tracking-[0.14em] text-crimson">{{ $branch['note'] }}</p>
                    @endif
                    <a href="#map-{{ $branch['key'] }}" class="mt-5 inline-block text-xs font-semibold uppercase tracking-[0.14em] text-crimson hover:text-crimson-dark">
                        View on map →
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Maps --}}
<section id="branch-maps" class="bg-paper py-20 lg:py-24">
    <div class="site-container">
        <div class="max-w-2xl">
            <p class="section-label">Find us</p>
            <h2 class="section-title mt-3">Map locations</h2>
            <p class="section-copy">Open directions in Google Maps for the gate / entry notes before your first visit.</p>
        </div>

        <div class="mt-12 space-y-10">
            @foreach (config('academy.branches') as $index => $branch)
                @php
                    $query = urlencode($branch['map_query'] ?? $branch['address']);
                    $embed = 'https://maps.google.com/maps?q='.$query.'&z=16&output=embed';
                    $directions = 'https://www.google.com/maps/dir/?api=1&destination='.$query;
                @endphp
                <article id="map-{{ $branch['key'] }}" class="grid gap-0 overflow-hidden border border-line bg-white lg:grid-cols-12">
                    <div class="flex flex-col justify-center p-6 sm:p-8 lg:col-span-4">
                        <div class="font-display text-sm uppercase tracking-[0.18em] text-crimson">
                            0{{ $index + 1 }} · {{ $branch['city'] }}
                        </div>
                        <h3 class="font-display mt-3 text-2xl uppercase text-ink sm:text-3xl">{{ $branch['name'] }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-stoneish">{{ $branch['address'] }}</p>
                        @if (! empty($branch['note']))
                            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.14em] text-crimson">{{ $branch['note'] }}</p>
                        @endif
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ $directions }}" target="_blank" rel="noopener noreferrer" class="btn-primary !px-5 !py-3">
                                Get directions
                            </a>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $query }}" target="_blank" rel="noopener noreferrer" class="btn-outline !px-5 !py-3">
                                Open in Maps
                            </a>
                        </div>
                    </div>
                    <div class="branch-map lg:col-span-8">
                        <iframe
                            title="Map — {{ $branch['name'] }}"
                            src="{{ $embed }}"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="site-container grid gap-12 lg:grid-cols-12">
        <div class="space-y-8 lg:col-span-4">
            <div>
                <div class="font-display text-xs uppercase tracking-[0.2em] text-crimson">Hours</div>
                <p class="mt-2 text-base text-ink">{{ config('academy.hours') }}</p>
                <p class="mt-1 text-sm text-stoneish">Sunday · Private sessions / events only</p>
            </div>
            <div>
                <div class="font-display text-xs uppercase tracking-[0.2em] text-crimson">Phone</div>
                <p class="mt-2 text-base text-ink">
                    <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone')) }}" class="hover:text-crimson">{{ config('academy.phone') }}</a>
                    @if (config('academy.phone_alt'))
                        <br>
                        <a href="tel:{{ preg_replace('/\s+/', '', config('academy.phone_alt')) }}" class="hover:text-crimson">{{ config('academy.phone_alt') }}</a>
                    @endif
                </p>
            </div>
            <div>
                <div class="font-display text-xs uppercase tracking-[0.2em] text-crimson">Email</div>
                <p class="mt-2 text-base text-ink">
                    <a href="mailto:{{ config('academy.email') }}" class="hover:text-crimson">{{ config('academy.email') }}</a>
                </p>
            </div>
            <div class="border border-line bg-paper p-5">
                <h3 class="font-display text-lg uppercase text-ink">Trial tip</h3>
                <p class="mt-2 text-sm leading-relaxed text-stoneish">
                    Mention age, program (Taekwondo / Kids / Kickboxing), and which branch you prefer so we can place you in the right batch.
                </p>
            </div>
        </div>

        <div class="lg:col-span-8">
            @if (session('success'))
                <div class="mb-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-4 border border-line bg-paper p-6 sm:p-8">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium">Name</label>
                    <input name="name" value="{{ old('name') }}" required class="w-full border-line focus:border-crimson focus:ring-crimson">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-line focus:border-crimson focus:ring-crimson">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Phone</label>
                        <input name="phone" value="{{ old('phone') }}" class="w-full border-line focus:border-crimson focus:ring-crimson">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Preferred branch</label>
                    <select name="preferred_branch" class="w-full border-line focus:border-crimson focus:ring-crimson">
                        <option value="">Select a branch (optional)</option>
                        @foreach (config('academy.branches') as $branch)
                            <option value="{{ $branch['key'] }}" @selected(old('preferred_branch') === $branch['key'])>
                                {{ $branch['name'] }} — {{ $branch['area'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Subject</label>
                    <input name="subject" value="{{ old('subject', 'Trial class enquiry') }}" required class="w-full border-line focus:border-crimson focus:ring-crimson">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Message</label>
                    <textarea name="message" rows="5" required class="w-full border-line focus:border-crimson focus:ring-crimson" placeholder="Age, preferred program (Taekwondo / Kickboxing / Kids), and convenient time…">{{ old('message') }}</textarea>
                </div>
                <button class="btn-primary">Send message</button>
            </form>
        </div>
    </div>
</section>
@endsection
