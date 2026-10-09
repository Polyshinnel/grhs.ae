@props(['block'])

@php
    $hasImage = filled($block['image_path'] ?? null);
    $isReverse = ($block['direction'] ?? 'normal') === 'reverse';
@endphp

<section data-content-direction="{{ $isReverse ? 'reverse' : 'normal' }}" class="grid min-w-0 px-5 sm:px-8 {{ $hasImage ? 'lg:grid-cols-2' : 'grid-cols-1' }}" aria-label="{{ filled($block['heading'] ?? null) ? $block['heading'] : 'Brand information' }}">
    <div class="flex min-w-0 flex-col justify-center py-10 sm:py-14 lg:py-20 {{ $isReverse && $hasImage ? 'lg:order-2 lg:pl-8' : ($hasImage ? 'lg:pr-8' : '') }}">
        @if (filled($block['heading'] ?? null))
            <h2 class="mb-5 text-xl font-semibold tracking-wide text-grhs-ink uppercase sm:mb-7 sm:text-2xl">{{ $block['heading'] }}</h2>
        @endif
        <p class="whitespace-pre-line text-base leading-relaxed text-grhs-ink sm:text-lg">{{ $block['text'] ?? '' }}</p>
    </div>

    @if ($hasImage)
        <div class="min-w-0 {{ $isReverse ? 'lg:order-1' : '' }}">
            <img class="aspect-[4/3] h-full max-h-[42rem] w-full object-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($block['image_path']) }}" alt="{{ $block['image_alt'] ?? '' }}" loading="lazy">
        </div>
    @endif
</section>
