@extends('layouts.app')

@section('title', 'Contact GRHS – HoReCa Supplier in Business Bay, Dubai')
@section('description', 'GRHS, Office 2203, Ontario Tower, Business Bay, Dubai. Call +971 58 533 8524, WhatsApp or email sales@grhs.ae for catalogues and prices.')
@section('canonical', 'https://grhs.ae/contacts')
@section('og_image', asset('images/home/main-poster.jpg'))
@section('header_black', 'true')

@section('content')
    <section class="relative isolate flex h-svh min-h-svh w-full items-center overflow-hidden bg-grhs-paper text-grhs-ink" aria-labelledby="contacts-title">
        <img class="absolute inset-0 -z-20 h-full w-full object-cover" src="{{ asset('images/home/contacts-hero.jpg') }}" alt="" fetchpriority="high">
        <div class="mx-auto flex h-full w-full items-center justify-center px-5 sm:px-10 lg:px-16">
            <div class="w-fit max-w-2xl bg-grhs-paper/80 px-6 py-7 text-center backdrop-blur-sm sm:px-10 sm:py-10">
                <p class="text-sm tracking-[0.24em] text-grhs-ink/75 uppercase sm:text-base">Golden Ratio Hospitality Supplies</p>
                <h1 id="contacts-title" class="mt-4 text-4xl leading-tight font-light tracking-[0.1em] uppercase sm:text-6xl lg:text-7xl">Contact GRHS</h1>
            </div>
        </div>
    </section>

    <div class="grid w-full gap-12 px-5 py-14 sm:px-8 sm:py-20 lg:grid-cols-[1.1fr_0.9fr] lg:gap-20 lg:px-16 lg:py-24">
        <div class="grid content-start gap-12 sm:gap-16">
            <section aria-labelledby="visit-office-title">
                <p class="text-sm tracking-[0.2em] text-grhs-olive uppercase">Based in Dubai</p>
                <h2 id="visit-office-title" class="mt-3 text-3xl font-semibold tracking-wide text-grhs-ink sm:text-4xl">Visit Our Office in Business Bay, Dubai</h2>
                <p class="mt-6 text-lg leading-8 text-grhs-ink/75 sm:text-xl sm:leading-9">
                    GRHS – Golden Ratio Hospitality Supplies – is a Dubai-based supplier of tableware, glassware, cutlery, barware and kitchenware for hotels, restaurants and cafes across the UAE. Visit our office in Ontario Tower, Business Bay, to see samples from our collections and discuss your project with our team.
                </p>
            </section>

            <section aria-labelledby="how-we-help-title">
                <p class="text-sm tracking-[0.2em] text-grhs-olive uppercase">For your next project</p>
                <h2 id="how-we-help-title" class="mt-3 text-3xl font-semibold tracking-wide text-grhs-ink sm:text-4xl">How We Can Help</h2>
                <p class="mt-6 text-lg leading-8 text-grhs-ink/75 sm:text-xl sm:leading-9">
                    Contact us to request brand catalogues and prices, order samples, get a proposal for a new opening or refurbishment, or place a repeat order. We work with owners, F&amp;B managers, chefs, purchasing teams and interior designers, and deliver to venues in Dubai, Abu Dhabi and all other Emirates.
                </p>
            </section>
        </div>

        <section class="h-fit border border-black/10 bg-white p-6 shadow-sm sm:p-10 lg:p-12" aria-labelledby="contact-form-title">
            <p class="text-sm tracking-[0.2em] text-grhs-olive uppercase">We are here to help</p>
            <h2 id="contact-form-title" class="mt-3 text-3xl font-semibold tracking-wide text-grhs-ink sm:text-4xl">Send an Enquiry</h2>
            <form class="mt-7 grid gap-4" action="{{ route('contacts.enquiry') }}" method="post" data-contact-form aria-describedby="contacts-form-status">
                @csrf
                <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
                    <label for="contacts-form-website">Website</label>
                    <input id="contacts-form-website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <label for="contacts-form-name" class="text-base font-semibold text-grhs-ink">Name</label>
                        <input id="contacts-form-name" name="name" type="text" autocomplete="name" maxlength="120" required class="min-h-14 w-full border border-black/20 bg-grhs-paper px-4 text-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive">
                    </div>
                    <div class="grid gap-2">
                        <label for="contacts-form-email" class="text-base font-semibold text-grhs-ink">Email</label>
                        <input id="contacts-form-email" name="email" type="email" autocomplete="email" maxlength="254" required class="min-h-14 w-full border border-black/20 bg-grhs-paper px-4 text-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive">
                    </div>
                </div>
                <div class="grid gap-2">
                    <label for="contacts-form-phone" class="text-base font-semibold text-grhs-ink">Phone</label>
                    <input id="contacts-form-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" pattern="[+0-9().\-\s]{5,40}" class="min-h-14 w-full border border-black/20 bg-grhs-paper px-4 text-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive">
                </div>
                <div class="grid gap-2">
                    <label for="contacts-form-message" class="text-base font-semibold text-grhs-ink">Message</label>
                    <textarea id="contacts-form-message" name="message" rows="5" maxlength="3000" class="w-full resize-y border border-black/20 bg-grhs-paper px-4 py-3 text-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive"></textarea>
                </div>
                <p id="contacts-form-status" data-form-status class="text-sm leading-relaxed text-grhs-ink/70" role="status" aria-live="polite">Please complete the form and we will get back to you.</p>
                <button type="submit" data-submit-button data-idle-label="Send enquiry" class="inline-flex min-h-12 w-full items-center justify-center bg-grhs-olive px-6 text-sm font-semibold tracking-[0.12em] text-white uppercase sm:w-fit">Send enquiry</button>
            </form>
        </section>
    </div>

    <section class="bg-grhs-sand/50 px-5 py-14 sm:px-8 sm:py-20 lg:px-16 lg:py-24" aria-labelledby="get-in-touch-title">
        <div class="grid w-full gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16">
            <header>
                <p class="text-sm tracking-[0.2em] text-grhs-olive uppercase">Talk to our team</p>
                <h2 id="get-in-touch-title" class="mt-3 text-3xl font-semibold tracking-wide text-grhs-ink sm:text-4xl">Get in Touch</h2>
            </header>

            <address class="grid gap-5 not-italic sm:grid-cols-2">
                @if (filled($settings?->address))
                    <div class="flex min-w-0 items-start gap-5 border-b border-black/10 pb-6">
                        <img class="mt-1 h-6 w-6 shrink-0 object-contain" src="{{ asset('images/site/geo.svg') }}" alt="">
                        <p class="break-words text-lg leading-8 text-grhs-ink sm:text-xl">{{ $settings->address }}</p>
                    </div>
                @endif

                @if (filled($settings?->phone))
                    <div class="flex min-w-0 items-start gap-5 border-b border-black/10 pb-6">
                        <img class="mt-1 h-6 w-6 shrink-0 object-contain" src="{{ asset('images/site/phone.svg') }}" alt="">
                        <a class="break-words text-lg leading-8 text-grhs-ink underline-offset-4 hover:text-grhs-olive hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive sm:text-xl" href="tel:{{ preg_replace('/[^+0-9]/', '', $settings->phone) }}">{{ $settings->phone }}</a>
                    </div>
                @endif

                @if (filled($settings?->email))
                    <div class="flex min-w-0 items-start gap-5 border-b border-black/10 pb-6">
                        <img class="mt-1 h-6 w-6 shrink-0 object-contain" src="{{ asset('images/site/mail.svg') }}" alt="">
                        <a class="break-all text-lg leading-8 text-grhs-ink underline-offset-4 hover:text-grhs-olive hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive sm:text-xl" href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>
                    </div>
                @endif

                @foreach ($settings?->social_links ?? [] as $socialLink)
                    @if (filled($socialLink['platform'] ?? null) && filled($socialLink['text'] ?? null) && filled($socialLink['url'] ?? null))
                        <div class="flex min-w-0 items-start gap-5 border-b border-black/10 pb-6">
                            @if ($socialLink['platform'] === 'instagram')
                                <img class="mt-1 h-6 w-6 shrink-0 object-contain" src="{{ asset('images/site/insta.svg') }}" alt="">
                            @elseif ($socialLink['platform'] === 'whatsapp')
                                <img class="mt-1 h-6 w-6 shrink-0 object-contain" src="{{ asset('images/site/whatsapp.svg') }}" alt="">
                            @else
                                <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-grhs-ink/50 text-xs font-bold leading-none text-grhs-ink" aria-hidden="true">
                                    @switch($socialLink['platform'])
                                        @case('telegram') ↗ @break
                                        @case('facebook') f @break
                                        @case('linkedin') in @break
                                        @case('youtube') ▶ @break
                                        @case('tiktok') ♪ @break
                                        @case('x') X @break
                                        @default ↗
                                    @endswitch
                                </span>
                            @endif
                            <a class="break-words text-lg leading-8 text-grhs-ink underline-offset-4 hover:text-grhs-olive hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive sm:text-xl" href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer">{{ $socialLink['text'] }}</a>
                        </div>
                    @endif
                @endforeach
            </address>
        </div>
    </section>

    <section class="px-5 py-14 sm:px-8 sm:py-20 lg:px-16 lg:py-24" aria-labelledby="office-map-title">
        <div class="w-full">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm tracking-[0.2em] text-grhs-olive uppercase">Find us</p>
                    <h2 id="office-map-title" class="mt-2 text-3xl font-semibold tracking-wide text-grhs-ink sm:text-4xl">Our Office</h2>
                </div>
                @if (filled($settings?->address))
                    <p class="max-w-2xl text-base leading-relaxed text-grhs-ink/70 sm:text-lg">{{ $settings->address }}</p>
                @endif
            </div>

            @if ($settings?->map_latitude !== null && $settings?->map_longitude !== null && filled($mapboxPublicToken))
                <div
                    class="h-[22rem] w-full overflow-hidden border border-black/10 bg-grhs-sand sm:h-[28rem] lg:h-[34rem]"
                    data-contact-map
                    data-latitude="{{ $settings->map_latitude }}"
                    data-longitude="{{ $settings->map_longitude }}"
                    data-mapbox-token="{{ $mapboxPublicToken }}"
                    data-marker-image="{{ asset('images/site/map-pin.png') }}"
                    role="region"
                    aria-label="Map showing the GRHS office in Business Bay, Dubai"
                ></div>
            @else
                <div class="flex min-h-40 items-center border border-black/10 bg-grhs-sand/50 px-6 py-8 text-sm leading-relaxed text-grhs-ink/70 sm:min-h-48 sm:px-10">
                    The office map is currently unavailable. Please contact us for directions.
                </div>
            @endif
        </div>
    </section>
@endsection
