@extends('layouts.app')

@php
    $brandName = $page->display_name;
    $logoPath = $page->display_logo_path;
    $seoTitle = $page->seo_title ?: $brandName.' | GRHS';
    $seoDescription = $page->seo_description;
    $catalogues = $page->catalogues;
    $hasCatalogues = $catalogues->isNotEmpty();
    $hasMultipleCatalogues = $catalogues->count() > 1;
    $catalogueDownloadUrl = $hasCatalogues
        ? route('brand-catalogues.download', ['publicPath' => ltrim($page->public_path, '/')])
        : null;
    $ogImage = $page->og_image_path
        ? url(\Illuminate\Support\Facades\Storage::disk('public')->url($page->og_image_path))
        : asset('images/home/main-poster.jpg');
@endphp

@section('title', $seoTitle)
@section('description', $seoDescription)
@section('canonical', $page->public_path)
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
            @if ($hasCatalogues)
                @if ($hasMultipleCatalogues)
                    <button type="button" data-catalogue-dialog-open="brand-catalogues-dialog" class="inline-flex min-h-12 min-w-[15rem] items-center justify-center border border-white bg-transparent px-7 text-sm font-semibold tracking-[0.12em] text-white uppercase transition-colors hover:bg-white hover:text-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Download the catalogs</button>
                @else
                    <a href="{{ $catalogueDownloadUrl }}" class="inline-flex min-h-12 min-w-[15rem] items-center justify-center border border-white bg-transparent px-7 text-sm font-semibold tracking-[0.12em] text-white uppercase transition-colors hover:bg-white hover:text-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Download the catalog</a>
                @endif
            @endif
        </div>
    </section>

    @foreach ($page->content_blocks ?? [] as $block)
        <x-brand-content-block :block="$block" />
    @endforeach

    @if ($hasCatalogues)
        <section class="flex justify-center px-5 py-12 sm:py-20" aria-label="{{ $brandName }} catalogue">
            @if ($hasMultipleCatalogues)
                <button type="button" data-catalogue-dialog-open="brand-catalogues-dialog" class="inline-flex min-h-12 w-full max-w-[19rem] items-center justify-center bg-grhs-olive px-6 text-center text-sm font-semibold tracking-wide text-white uppercase transition-colors hover:bg-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-grhs-ink">Download the catalogs</button>
            @else
                <a href="{{ $catalogueDownloadUrl }}" class="inline-flex min-h-12 w-full max-w-[19rem] items-center justify-center bg-grhs-olive px-6 text-center text-sm font-semibold tracking-wide text-white uppercase transition-colors hover:bg-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-grhs-ink">Download the catalog</a>
            @endif
        </section>
    @endif

    @if ($hasMultipleCatalogues)
        <dialog id="brand-catalogues-dialog" aria-labelledby="brand-catalogues-dialog-title" class="m-auto w-[calc(100%-2rem)] max-w-lg border-0 bg-transparent p-0 text-grhs-ink backdrop:bg-black/70">
            <div class="bg-grhs-paper p-6 shadow-2xl sm:p-8">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <h2 id="brand-catalogues-dialog-title" class="text-xl font-semibold tracking-wide uppercase">Download the catalogs</h2>
                    <button type="button" data-catalogue-dialog-close aria-label="Close catalogue selection" class="inline-flex size-10 shrink-0 items-center justify-center text-2xl leading-none hover:bg-black/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-ink">&times;</button>
                </div>
                <div class="flex flex-col gap-3">
                    @foreach ($catalogues as $catalogue)
                        <a href="{{ route('brand-catalogues.catalogue-download', ['publicPath' => ltrim($page->public_path, '/'), 'catalogue' => $catalogue->id]) }}" class="inline-flex min-h-12 items-center justify-center bg-grhs-olive px-6 text-center text-sm font-semibold tracking-wide text-white uppercase transition-colors hover:bg-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-grhs-ink">{{ $catalogue->button_label ?: 'Download the catalog' }}</a>
                    @endforeach
                </div>
            </div>
        </dialog>

        <script>
            document.querySelectorAll('[data-catalogue-dialog-open="brand-catalogues-dialog"]').forEach((button) => {
                button.addEventListener('click', () => document.getElementById('brand-catalogues-dialog')?.showModal());
            });

            const catalogueDialog = document.getElementById('brand-catalogues-dialog');
            catalogueDialog?.querySelector('[data-catalogue-dialog-close]')?.addEventListener('click', () => catalogueDialog.close());
            catalogueDialog?.addEventListener('click', (event) => {
                if (event.target === catalogueDialog) {
                    catalogueDialog.close();
                }
            });
        </script>
    @endif
@endsection
