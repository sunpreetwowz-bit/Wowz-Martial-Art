@php use App\Support\DemoMedia; @endphp
@extends('layouts.public')

@section('title', 'Programs — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="section-label">Programs</p>
        <h1 class="font-display mt-3 text-5xl uppercase">Choose your path</h1>
        <p class="mt-4 max-w-2xl text-white/75">From kids martial arts to competition prep — every program has a clear syllabus and coaching plan.</p>
    </div>
</section>

<section class="bg-paper py-16">
    <div class="site-container grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
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
                    <h2 class="font-display text-2xl uppercase">{{ $service->name }}</h2>
                    <p class="mt-2 text-sm text-white/75">{{ \Illuminate\Support\Str::limit($service->description, 90) }}</p>
                    <span class="mt-4 inline-block text-xs font-semibold uppercase tracking-[0.16em] text-crimson">View program →</span>
                </div>
            </a>
        @empty
            <p class="text-stoneish">No programs published yet.</p>
        @endforelse
    </div>
</section>
@endsection
