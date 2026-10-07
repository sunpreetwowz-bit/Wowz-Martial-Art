@php use App\Support\DemoMedia; @endphp
@extends('layouts.public')

@section('title', 'Gallery — Wowz Martial Art')

@section('content')
<section class="page-hero">
    <div class="site-container">
        <p class="font-display text-sm uppercase tracking-[0.25em] text-crimson">Gallery</p>
        <h1 class="font-display mt-3 text-5xl uppercase">Life on the mat</h1>
        <p class="mt-4 max-w-2xl text-white/75">Classes, grading days, and competition floors from Wowz Martial Art.</p>
    </div>
</section>

<section class="bg-paper py-16">
    <div class="site-container">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('gallery.index') }}" class="px-3 py-1 text-sm {{ ! request('category') ? 'bg-crimson text-white' : 'bg-white text-ink' }}">All</a>
            @foreach ($categories as $category)
                <a href="{{ route('gallery.index', ['category' => $category->slug]) }}" class="px-3 py-1 text-sm {{ request('category') === $category->slug ? 'bg-crimson text-white' : 'bg-white text-ink' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($images as $image)
                <figure>
                    <img src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $image->title }}" class="aspect-[4/3] w-full object-cover">
                    @if ($image->caption || $image->title)
                        <figcaption class="mt-2 text-sm text-stoneish">{{ $image->caption ?: $image->title }}</figcaption>
                    @endif
                </figure>
            @empty
                @foreach (range(0, 5) as $index)
                    <figure>
                        <img src="{{ DemoMedia::gallery($index) }}" alt="Academy training" class="aspect-[4/3] w-full object-cover">
                        <figcaption class="mt-2 text-sm text-stoneish">Demo training photo</figcaption>
                    </figure>
                @endforeach
            @endforelse
        </div>
        <div class="mt-8">{{ $images->links() }}</div>
    </div>
</section>
@endsection
