@extends('layouts.app')

@php
    $brandName = $page->display_name;
    $logoPath = $page->display_logo_path;
    $seoTitle = $page->seo_title ?: $brandName.' | GRHS';
    $seoDescription = $page->seo_description;
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
    <section class="relative isolate flex h-svh min-h-svh w-full items-center justify-center overflow-hidden bg-grhs-ink text-white" aria-label="{{ $brandName }}">
        @if ($page->hero_image_path)
            <img class="absolute inset-0 -z-20 h-full w-full object-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($page->hero_image_path) }}" alt="{{ $page->hero_image_alt ?? '' }}" fetchpriority="high">
        @endif
        <div class="absolute inset-0 -z-10 bg-black/20" aria-hidden="true"></div>
        <div class="mx-auto flex w-full max-w-[90rem] flex-col items-center gap-8 px-5 pt-[90px] text-center sm:gap-10 sm:px-10">
            @if ($logoPath)
                <img class="max-h-56 w-auto max-w-[min(88vw,48rem)] object-contain sm:max-h-72 lg:max-h-80" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath) }}" alt="{{ $page->logo_alt ?? '' }}" fetchpriority="high">
            @endif
            <h1 class="max-w-5xl text-4xl leading-tight font-light tracking-[0.1em] uppercase break-words sm:text-6xl lg:text-7xl">{{ $page->h1_title ?: $brandName }}</h1>
            @if ($page->catalogue_file_path)
                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($page->catalogue_file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-12 min-w-[15rem] items-center justify-center border border-white bg-transparent px-7 text-sm font-semibold tracking-[0.12em] text-white uppercase transition-colors hover:bg-white hover:text-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Download the catalog</a>
            @endif
        </div>
    </section>

    @foreach ($page->content_blocks ?? [] as $block)
        <x-brand-content-block :block="$block" />
    @endforeach

    @if ($page->catalogue_file_path)
        <section class="flex justify-center px-5 py-12 sm:py-20" aria-label="{{ $brandName }} catalogue">
            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($page->catalogue_file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-12 w-full max-w-[19rem] items-center justify-center bg-grhs-olive px-6 text-center text-sm font-semibold tracking-wide text-white uppercase transition-colors hover:bg-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-grhs-ink">Download the catalog</a>
        </section>
    @endif
@endsection
