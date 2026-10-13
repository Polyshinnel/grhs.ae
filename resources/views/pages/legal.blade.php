@extends('layouts.app')

@section('title', $title.' | GRHS')
@section('description', $title.' for Golden Ratio Hospitality Supplies Trading LLC.')
@section('canonical', $canonical)
@section('header_black', 'true')

@section('content')
    <section class="px-5 py-20 sm:px-8 sm:py-28 lg:px-16" aria-labelledby="legal-page-title">
        <div class="mx-auto w-full max-w-4xl">
            <h1 id="legal-page-title" class="text-3xl font-semibold tracking-wide text-grhs-ink sm:text-5xl">{{ $title }}</h1>
            <p class="mt-8 max-w-3xl text-lg leading-8 text-grhs-ink/75">
                This page is being prepared. The full text will be published soon.
            </p>
        </div>
    </section>
@endsection
