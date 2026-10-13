@php
    $whatsappLink = collect($contactSettings?->social_links ?? [])->firstWhere('platform', 'whatsapp');
@endphp

<div class="fixed bottom-6 right-4 z-[300] flex flex-col items-center gap-2.5 sm:bottom-[50px] sm:right-[15px]">
    @if (filled($whatsappLink['url'] ?? null))
        <a href="{{ $whatsappLink['url'] }}" aria-label="Chat with GRHS on WhatsApp" class="flex h-[60px] w-[60px] items-center justify-center rounded-full border border-black/20 bg-white shadow-md transition-shadow hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive">
            <img class="h-[30px] w-[30px]" src="{{ asset('images/site/whatsapp.svg') }}" alt="">
        </a>
    @endif
    <a href="/catalogues/" aria-label="Browse catalogues" class="flex h-[60px] w-[60px] items-center justify-center rounded-full border border-black/20 bg-white shadow-md transition-shadow hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive">
        <img class="h-11 w-11" src="{{ asset('images/site/catalogues.svg') }}" alt="">
    </a>
</div>
