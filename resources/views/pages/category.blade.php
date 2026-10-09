@extends('layouts.app')

@php
    $brands = $page->category->brandPages;
    $gridVariant = match (true) {
        $brands->count() === 1 => 'single',
        $brands->count() === 2 => 'double',
        default => 'grid',
    };
    $seoTitle = $page->seo_title ?: $page->hero_heading.' | GRHS';
    $seoDescription = $page->seo_description ?: $page->hero_text;
    $ogImage = $page->og_image_path
        ? url(\Illuminate\Support\Facades\Storage::disk('public')->url($page->og_image_path))
        : asset('images/home/main-poster.jpg');
@endphp

@section('title', $seoTitle)
@section('description', $seoDescription)
@section('canonical', url($page->public_path))
@section('og_image', $ogImage)
@section('header_black', $page->header_black ? 'true' : 'false')

@section('content')
    <section class="relative isolate flex h-svh min-h-svh w-full items-center justify-center overflow-hidden bg-grhs-ink text-center text-white" aria-labelledby="category-hero-title">
        @if ($page->hero_image_path)
            <img class="absolute inset-0 -z-20 h-full w-full object-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($page->hero_image_path) }}" alt="{{ $page->hero_image_alt ?? '' }}" fetchpriority="high">
        @endif
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-black/70 via-black/20 to-black/10" aria-hidden="true"></div>
        <div class="mx-auto w-full max-w-[90rem] px-5 sm:px-10 lg:px-16">
            <h1 id="category-hero-title" class="mx-auto max-w-5xl text-4xl leading-tight font-light tracking-[0.1em] uppercase break-words sm:text-6xl lg:text-7xl">{{ $page->hero_heading }}</h1>
            @if (filled($page->hero_text))
                <p class="mx-auto mt-4 max-w-3xl text-base leading-relaxed text-white/90 sm:mt-6 sm:text-xl lg:text-2xl">{{ $page->hero_text }}</p>
            @endif
        </div>
    </section>

    <section class="w-full px-5 pt-0 pb-8 sm:px-8 sm:pb-12 {{ $brands->isNotEmpty() ? 'lg:pt-12' : '' }}" aria-label="{{ $page->category->name }} brands">
        @if ($brands->isNotEmpty())
            <h2 class="mt-6 mb-4 text-xl font-semibold tracking-wide text-grhs-ink uppercase lg:mt-0 lg:mb-10 lg:text-3xl">Brands</h2>
        @endif
        <div data-brand-grid="{{ $gridVariant }}" data-brand-count="{{ $brands->count() }}" class="grid {{ $gridVariant === 'single' ? 'grid-cols-1' : ($gridVariant === 'double' ? 'grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6 sm:gap-y-0' : 'grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3') }}">
            @foreach ($brands as $brandPage)
                <a href="{{ $brandPage->public_path }}" aria-label="{{ $brandPage->display_name }}" class="group relative isolate block w-full min-w-0 overflow-hidden bg-grhs-ink focus-visible:z-10 focus-visible:outline-2 focus-visible:outline-offset-[-4px] focus-visible:outline-white {{ $gridVariant === 'single' ? 'aspect-square sm:aspect-[16/8] max-h-[42rem]' : ($gridVariant === 'double' ? 'aspect-square sm:aspect-[4/3]' : 'aspect-square') }}">
                    @if ($brandPage->category_image_path)
                        <img class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($brandPage->category_image_path) }}" alt="{{ $brandPage->category_image_alt ?? '' }}" loading="lazy">
                    @endif
                    <span class="absolute inset-0 bg-linear-to-t from-black/75 via-black/10 to-black/10" aria-hidden="true"></span>
                    @if ($brandPage->display_logo_path)
                        <img class="absolute inset-x-5 top-1/2 mx-auto max-h-36 w-auto max-w-[80%] -translate-y-1/2 object-contain brightness-110 sm:max-h-48" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($brandPage->display_logo_path) }}" alt="{{ $brandPage->logo_alt ?? '' }}" loading="lazy">
                    @else
                        <span class="absolute inset-0 flex items-center justify-center px-5 text-center text-2xl font-semibold tracking-wide text-white uppercase sm:text-3xl lg:text-4xl">{{ $brandPage->display_name }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    @foreach ($page->content_blocks ?? [] as $contentBlock)
        @if (filled($contentBlock['text'] ?? null))
            <section class="w-full px-5 sm:px-8 {{ $loop->first ? 'pt-4 pb-6 sm:pt-6 sm:pb-8' : 'py-6 sm:py-8' }}">
                <div class="w-full">
                    @if (filled($contentBlock['heading'] ?? null))
                        <h2 class="mb-6 text-xl font-semibold tracking-wide text-grhs-ink uppercase sm:mb-8 sm:text-2xl lg:text-3xl">{{ $contentBlock['heading'] }}</h2>
                    @endif
                    <p class="whitespace-pre-line text-base leading-relaxed text-grhs-ink/80 sm:text-lg">{{ $contentBlock['text'] }}</p>
                </div>
            </section>
        @endif
    @endforeach
@endsection
