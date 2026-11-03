@extends('layouts.site')

@section('title', $title)
@section('description', $description)

@section('content')

    <section class="border-b border-black/5 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-16 text-center sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-maroon">{{ $hero['eyebrow'] }}</p>
            <h1 class="mt-4 font-display text-4xl font-semibold sm:text-5xl">{{ $hero['title'] }}</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink/60">{{ $hero['description'] }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald">{{ $story['eyebrow'] }}</p>
                <h2 class="mt-2 font-display text-2xl font-semibold">{{ $story['title'] }}</h2>
            </div>
            <p class="leading-relaxed text-ink/70">{{ $story['description'] }}</p>
        </div>
    </section>

    <section class="bg-cream-dark py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h2 class="mb-10 text-center font-display text-3xl font-semibold">{{ $values_title }}</h2>
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ($values as $value)
                    <div class="rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-black/5">
                        <p class="font-display text-lg font-semibold">{{ $value['title'] }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $value['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-site.cta-banner :heading="$cta['heading']" :description="$cta['description']" />
    </section>

@endsection
