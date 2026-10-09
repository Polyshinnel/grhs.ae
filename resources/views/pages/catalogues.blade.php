@extends('layouts.app')

@php
    $seoTitle = $settings?->seo_title ?: 'Product catalogues | GRHS';
    $seoDescription = $settings?->seo_description ?: 'Browse product catalogues from the renowned brands represented by Golden Ratio Hospitality Solutions.';
    $ogImage = $settings?->og_image_path
        ? url(\Illuminate\Support\Facades\Storage::disk('public')->url($settings->og_image_path))
        : asset('images/home/main-poster.jpg');
@endphp

@section('title', $seoTitle)
@section('description', $seoDescription)
@section('canonical', url('/catalogues'))
@section('og_image', $ogImage)
@section('header_black', $settings?->header_black ? 'true' : 'false')

@section('content')
    <section class="mx-auto w-full max-w-[90rem] px-5 pt-[120px] pb-16 sm:px-8 lg:px-16" aria-labelledby="catalogues-title" data-catalogue-library>
        <header class="mx-auto max-w-3xl text-center">
            <h1 id="catalogues-title" class="text-2xl font-semibold tracking-[0.08em] text-grhs-ink uppercase sm:text-3xl">{{ $settings?->heading ?: 'Catalogue Library' }}</h1>
            @if (filled($settings?->intro_text))
                <p class="mt-4 text-sm leading-relaxed text-grhs-ink/75 sm:text-base">{{ $settings->intro_text }}</p>
            @endif
        </header>

        <div class="mt-8 grid grid-cols-1 items-end gap-4 border-y border-black/10 py-6 sm:mt-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-5">
            <label class="flex min-w-0 flex-col gap-2 text-sm font-semibold tracking-wide text-grhs-ink uppercase">
                Brand
                <select data-catalogue-filter="brand" class="h-12 w-full min-w-0 border border-black/20 bg-white px-4 text-base font-normal normal-case focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive sm:h-14">
                    <option value="">All brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex min-w-0 flex-col gap-2 text-sm font-semibold tracking-wide text-grhs-ink uppercase">
                Category
                <select data-catalogue-filter="category" class="h-12 w-full min-w-0 border border-black/20 bg-white px-4 text-base font-normal normal-case focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive sm:h-14">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex min-w-0 flex-col gap-2 text-sm font-semibold tracking-wide text-grhs-ink uppercase">
                Concept
                <select data-catalogue-filter="concept" class="h-12 w-full min-w-0 border border-black/20 bg-white px-4 text-base font-normal normal-case focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive sm:h-14">
                    <option value="">All concepts</option>
                    @foreach ($concepts as $concept)
                        <option value="{{ $concept->id }}">{{ $concept->name }}</option>
                    @endforeach
                </select>
            </label>
            <button type="button" data-catalogue-reset class="inline-flex h-12 w-full items-center justify-center border border-grhs-ink px-5 text-sm font-semibold tracking-wide text-grhs-ink uppercase transition-colors hover:bg-grhs-ink hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-ink sm:h-14">Reset filters</button>
        </div>

        <p class="mt-5 text-sm text-grhs-ink/70" aria-live="polite" data-catalogue-count>{{ $catalogues->count() }} catalogues</p>

        @foreach ($categories as $category)
            <section class="mt-10 first:mt-6" data-catalogue-category-group data-category-group-id="{{ $category->id }}" aria-labelledby="catalogue-category-{{ $category->id }}">
                <h2 id="catalogue-category-{{ $category->id }}" class="mb-5 text-xl font-semibold tracking-wide text-grhs-ink uppercase sm:text-2xl">{{ $category->name }}</h2>
                <div class="grid grid-cols-2 gap-x-3 gap-y-8 sm:grid-cols-3 sm:gap-x-5 lg:grid-cols-4 xl:grid-cols-5" data-catalogue-grid>
                    @foreach ($cataloguesByCategory->get($category->id, collect()) as $catalogue)
                <article class="flex min-w-0 flex-col" data-catalogue-card data-catalogue-id="{{ $catalogue->id }}" data-brand-id="{{ $catalogue->brand_id }}" data-category-id="{{ $catalogue->category_id }}" data-concept-ids="{{ $catalogue->concepts->modelKeys() ? implode(' ', $catalogue->concepts->modelKeys()) : '' }}">
                    <div class="overflow-hidden border border-black/15 bg-white">
                        <div class="aspect-[182/182] bg-grhs-paper">
                            @if ($catalogue->image_url)
                                <img class="h-full w-full object-cover" src="{{ $catalogue->image_url }}" alt="{{ $catalogue->image_alt ?? '' }}" loading="lazy">
                            @else
                                <div class="flex h-full w-full items-center justify-center px-3 text-center text-xs tracking-[0.12em] text-grhs-ink/45 uppercase" aria-label="No catalogue image">Catalogue</div>
                            @endif
                        </div>
                        <div class="flex min-h-16 min-w-0 items-center justify-center border-t border-black/10 px-2 py-3 text-center">
                            <h2 class="break-words text-sm font-semibold text-grhs-ink uppercase sm:text-base">{{ $catalogue->brand->name }}</h2>
                        </div>
                    </div>

                    <dl class="mt-3 grid min-w-0 gap-1 text-xs leading-relaxed text-grhs-ink/70">
                        <div class="min-w-0"><dt class="inline font-semibold">Category: </dt><dd class="inline break-words">{{ $catalogue->category->name }}</dd></div>
                        @if ($catalogue->concepts->isNotEmpty())
                            <div class="min-w-0"><dt class="inline font-semibold">Concept: </dt><dd class="inline break-words">{{ $catalogue->concepts->pluck('name')->join(', ') }}</dd></div>
                        @endif
                    </dl>

                    @if ($catalogue->view_url || $catalogue->download_url)
                        <div class="mt-auto flex flex-wrap gap-2 pt-4">
                            @if ($catalogue->view_url)
                                <a class="inline-flex min-h-10 flex-1 items-center justify-center rounded-full border border-grhs-ink px-3 text-xs font-semibold tracking-wide text-grhs-ink uppercase transition-colors hover:bg-grhs-ink hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-ink" href="{{ $catalogue->view_url }}" target="_blank" rel="noopener noreferrer">View</a>
                            @endif
                            @if ($catalogue->download_url)
                                <a class="inline-flex min-h-10 flex-1 items-center justify-center rounded-full bg-grhs-olive px-3 text-xs font-semibold tracking-wide text-white uppercase transition-colors hover:bg-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-ink" href="{{ $catalogue->download_url }}">Download</a>
                            @endif
                        </div>
                    @endif
                </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <p class="mt-12 text-center text-base text-grhs-ink/70" data-catalogue-empty @if ($catalogues->isNotEmpty()) hidden @endif>No catalogues found.</p>
    </section>
@endsection
