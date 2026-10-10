@extends('layouts.app')

@section('title', 'Restaurant & Hotel Tableware Supplier in Dubai, UAE | GRHS')
@section('description', 'HoReCa supplier in Dubai: premium tableware, glassware, cutlery, barware and kitchenware for hotels and restaurants across the UAE.')
@section('canonical', url('/'))

@section('content')
    @php
        $partners = [
            1 => 'Ossiano', 2 => 'C2', 4 => 'Kraken', 5 => 'Amaya', 6 => 'Naan',
            7 => 'Scalini Dubai', 8 => 'Tattu', 9 => 'Cullinan', 10 => 'Addmind Hospitality',
            11 => 'Q7 Management', 12 => 'Fundamental Hospitality', 13 => 'Gastronaut',
            14 => 'Independent Food Company', 15 => 'FoodFund International', 16 => 'Mine & Yours',
            17 => 'Q Food & Beverage', 18 => 'Chic Nonna', 19 => 'Nahate Dubai', 20 => 'Krasota Dubai',
            21 => 'Ula', 22 => 'Illustrated hospitality partner', 23 => 'Amazonico', 24 => 'Sucre', 25 => 'B&B',
            26 => 'Tresind', 27 => 'Avatara', 28 => '99 Sushi Bar', 29 => 'African Queen',
            30 => 'Blue illustrated hospitality partner', 31 => 'February 30', 32 => 'Red monogram hospitality partner',
            33 => 'Loona Moscow', 34 => 'Kira', 35 => 'Bar des Prés', 36 => 'Sirene', 37 => 'Five',
            38 => 'Hilton', 39 => 'Accor', 40 => 'Atlantis The Palm', 41 => 'Marriott',
            42 => 'Rixos Hotels', 43 => 'Rotana',
        ];
    @endphp

    <section class="relative isolate flex min-h-[42rem] h-[100svh] max-h-[70rem] w-full items-center overflow-hidden bg-grhs-ink text-white md:min-h-[46rem]" aria-labelledby="home-hero-title" data-home-hero>
        <video
            class="absolute inset-0 -z-20 h-full w-full object-cover"
            autoplay
            muted
            loop
            playsinline
            preload="metadata"
            poster="{{ asset('images/home/main-poster.jpg') }}"
            aria-hidden="true"
            data-home-hero-video
        >
            <source src="{{ asset('videos/main-video-mobile.mp4') }}" type="video/mp4" media="(max-width: 767px)">
            <source src="{{ asset('videos/main-video-desktop.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-black/65 via-black/15 to-black/25"></div>

        <div class="mx-auto flex w-full max-w-[90rem] flex-col items-center gap-8 px-6 text-center sm:px-10 md:gap-10 md:px-16 lg:px-24">
            <div>
                <h1 id="home-hero-title" class="max-w-5xl text-center text-3xl font-light tracking-[0.12em] uppercase sm:text-5xl">
                    Hotel &amp; Restaurant Tableware Supplier in Dubai
                </h1>
                <p class="mx-auto mt-4 max-w-3xl text-center text-base leading-relaxed text-white/90 sm:text-lg">
                    Premium tableware, glassware, cutlery and barware for hotels, restaurants and cafés across the UAE.
                </p>
            </div>
            <a href="/catalogues" class="inline-flex min-h-12 items-center border border-white/80 px-7 text-xs tracking-[0.2em] text-white uppercase transition-colors hover:bg-white hover:text-grhs-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white sm:text-sm">
                Catalogues
            </a>
        </div>
    </section>

    <section class="mt-8 flex h-[100px] items-center overflow-hidden bg-[#FAF8F3]" aria-label="Our partners">
        <div class="swiper h-full w-full" data-partners-slider>
            <div class="swiper-wrapper h-full items-center">
                @foreach ($partners as $partner => $partnerName)
                    <div class="swiper-slide flex h-full items-center justify-center px-2 sm:px-3">
                        <img
                            class="max-h-full max-w-full object-contain"
                            src="{{ asset("images/home/partners/{$partner}.png") }}"
                            alt="{{ $partnerName }} logo"
                            width="180"
                            height="96"
                            loading="lazy"
                        >
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @php
        $bestsellers = [
            ['name' => 'Kenai Ceramics', 'description' => 'Minimalist porcelain and stoneware, handcrafted from for the HoReCa sector.', 'image' => '1.jpg', 'alt' => 'Minimalist stoneware plates and bowls in natural tones'],
            ['name' => 'Uccelo', 'description' => 'Our private label: durable pure white porcelain for hotels and restaurants at an affordable price.', 'image' => '2.jpg', 'alt' => 'White porcelain cup and saucer arranged on a table'],
            ['name' => 'Le Coq Porcelain', 'description' => 'Elegant, high-performance porcelain for chefs and banqueting professionals.', 'image' => '3.jpg', 'alt' => 'White porcelain plate with a delicate floral pattern'],
            ['name' => 'Gien', 'description' => 'A prestigious French manufacture founded in 1821, combining traditional craftsmanship with classic French decors.', 'image' => '4.jpg', 'alt' => 'Decorative porcelain plates with a blue and red pattern'],
        ];

        $categories = [
            ['name' => 'Tableware', 'url' => '/tableware/', 'image' => '1.jpg', 'alt' => 'Hand placing white porcelain bowls and plates on a table'],
            ['name' => 'Glassware', 'url' => '/glassware/', 'image' => '2.jpg', 'alt' => 'Clear glassware displayed on a dark surface'],
            ['name' => 'Bar glass', 'url' => '/barware/', 'image' => '3.jpg', 'alt' => 'Stemmed wine glasses arranged on a bar'],
            ['name' => 'Bar tools', 'url' => '/barware/', 'image' => '4.jpg', 'alt' => 'Bread presented on metal stands at a buffet'],
            ['name' => 'Cutlery', 'url' => '/cutlery/', 'image' => '5.jpg', 'alt' => 'Fork and knife beside a ceramic plate'],
            ['name' => 'Steak knives', 'url' => '/kitchenware/', 'image' => '6.jpg', 'alt' => 'Set of steak knives with patterned handles'],
            ['name' => 'Wood', 'url' => '/wood/', 'image' => '7.jpg', 'alt' => 'Round wooden serving bowl on a table'],
            ['name' => 'Kitchen accessories', 'url' => '/kitchenware/', 'image' => '8.jpg', 'alt' => 'Stainless steel gastronorm pans in a buffet counter'],
            ['name' => 'Asian concepts', 'url' => '/glassware/', 'image' => '9.jpg', 'alt' => 'Asian-inspired restaurant table setting'],
            ['name' => 'Buffet & hotel supplies', 'url' => '/kitchenware/', 'image' => '10.jpg', 'alt' => 'Black bowls and plates arranged on a dining table'],
            ['name' => 'Metal & copperware', 'url' => '/kitchenware/', 'image' => '11.jpg', 'alt' => 'Elegant table setting with metal serving pieces'],
            ['name' => 'Poolware', 'url' => '/poolware/', 'image' => '12.jpg', 'alt' => 'Bread displayed on black metal serving stands'],
        ];

        $brands = [
            ['name' => 'Kenai', 'image' => '1.png', 'width' => 109, 'height' => 37],
            ['name' => 'Uccello', 'image' => '2.png', 'width' => 112, 'height' => 72],
            ['name' => 'Bitossi Home', 'image' => '3.png', 'width' => 198, 'height' => 39],
            ['name' => 'Le Coq Porcelaine', 'image' => '4.png', 'width' => 91, 'height' => 84],
            ['name' => 'Miyama', 'image' => '5.png', 'width' => 192, 'height' => 39],
            ['name' => 'Gien', 'image' => '6.png', 'width' => 118, 'height' => 63],
            ['name' => 'Pura Sangre', 'image' => '7.png', 'width' => 177, 'height' => 57],
            ['name' => 'Arita Plus', 'image' => '8.png', 'width' => 214, 'height' => 61],
        ];
    @endphp

    <section class="overflow-hidden px-5 py-16 sm:px-8 sm:py-20 lg:py-28" aria-labelledby="bestsellers-title">
        <div class="mb-8 flex items-end justify-between gap-6 sm:mb-12">
            <h2 id="bestsellers-title" class="text-2xl font-semibold tracking-[0.12em] uppercase sm:text-3xl">BESTSELLERS</h2>
            <a href="/catalogues" class="shrink-0 text-sm tracking-[0.12em] text-grhs-ink underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 sm:text-base">all catalogues</a>
        </div>

        <div class="w-full">
            <div class="swiper w-full touch-pan-y" data-bestsellers-slider aria-label="Bestsellers">
                <div class="swiper-wrapper items-stretch">
                    @foreach (array_merge($bestsellers, $bestsellers) as $slideIndex => $bestseller)
                        <article class="swiper-slide !h-auto" @if ($slideIndex >= count($bestsellers)) aria-hidden="true" @endif>
                            <img class="aspect-[420/572] w-full object-cover" src="{{ asset("images/home/bestsellers/{$bestseller['image']}") }}" alt="{{ $bestseller['alt'] }}" width="420" height="572" loading="lazy" draggable="false">
                            <h3 class="mt-5 text-lg font-semibold text-grhs-ink sm:mt-7 sm:text-xl">{{ $bestseller['name'] }}</h3>
                            <p class="mt-3 text-base leading-relaxed text-grhs-ink sm:mt-5 sm:text-lg">{{ $bestseller['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-14 px-5 py-12 sm:space-y-20 sm:px-8 sm:py-20 lg:space-y-28 lg:py-28" aria-labelledby="about-title">
        <div class="grid items-center gap-7 sm:gap-10 lg:grid-cols-2 lg:gap-20">
            <img class="aspect-[4/3] w-full object-cover" src="{{ asset('images/home/about-1.jpg') }}" alt="GRHS showroom displaying tableware, glassware and serving pieces" width="900" height="675" loading="lazy">
            <div class="text-grhs-ink">
                <h2 id="about-title" class="mb-6 text-2xl font-semibold sm:mb-8 sm:text-3xl">About GRHS</h2>
                <p class="text-base leading-relaxed sm:text-lg">GRHS – Golden Ratio Hospitality Supplies – is a Dubai-based supplier of premium tableware, glassware, cutlery, barware and kitchenware for the hospitality industry. We work with hotels, restaurants, cafés, bars, beach clubs and catering companies in Dubai, Abu Dhabi and across the UAE.</p>
                <p class="mt-5 text-base leading-relaxed sm:mt-7 sm:text-lg">Our mission is to curate exceptional collections created by talented manufacturers and designers from around the world. We bring together renowned European and Japanese brands, independent handcraft studios and our own private label Uccello, so every venue can find pieces that match its concept, cuisine and budget.</p>
            </div>
        </div>

        <div class="grid items-center gap-7 sm:gap-10 lg:grid-cols-2 lg:gap-20">
            <div class="order-2 text-grhs-ink lg:order-1">
                <p class="text-base leading-relaxed sm:text-lg">We believe that the right plate, glass and cutlery shape the way guests remember a place. Handcrafted pieces made from natural materials, with distinctive shapes and patterns, give every table its own character and turn a meal into an experience guests want to repeat.</p>
                <p class="mt-5 text-base leading-relaxed sm:mt-7 sm:text-lg">Our team supports projects from the first idea to repeat orders: we help with product selection for new openings and refurbishments, prepare samples and presentations for owners, chefs and interior designers, and deliver to venues across all Emirates. Visit our office in Business Bay, Dubai, to see samples, or contact us for catalogues and prices.</p>
            </div>
            <img class="order-1 aspect-[4/3] w-full object-cover lg:order-2" src="{{ asset('images/home/about-2.jpg') }}" alt="Shelves displaying porcelain plates, bowls and serving dishes" width="900" height="675" loading="lazy">
        </div>
    </section>

    <section class="px-5 pt-12 pb-0 sm:px-8 sm:pt-16 sm:pb-0 lg:pt-20 lg:pb-0" aria-labelledby="categories-title">
        <div class="mb-8 flex items-end justify-between gap-6 sm:mb-12">
            <h2 id="categories-title" class="text-2xl font-semibold tracking-[0.12em] uppercase sm:text-3xl">CATEGORIES</h2>
            <a href="/catalogues" class="shrink-0 text-sm tracking-[0.12em] text-grhs-ink underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 sm:text-base">all catalogues</a>
        </div>
        <div class="grid grid-cols-2 gap-x-3 gap-y-4 sm:grid-cols-3 sm:gap-x-4 sm:gap-y-6 lg:grid-cols-6 lg:gap-x-5 lg:gap-y-8">
            @foreach ($categories as $category)
                <a href="{{ $category['url'] }}" class="group relative aspect-square overflow-hidden bg-grhs-ink focus-visible:z-10 focus-visible:outline-2 focus-visible:outline-offset-[-4px] focus-visible:outline-white">
                    <img class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none" src="{{ asset("images/home/categories/{$category['image']}") }}" alt="{{ $category['alt'] }}" width="600" height="600" loading="lazy">
                    <span class="absolute inset-0 bg-linear-to-t from-black/70 via-black/10 to-transparent" aria-hidden="true"></span>
                    <h3 class="absolute right-3 bottom-3 left-3 text-base leading-tight font-semibold text-white sm:right-4 sm:bottom-4 sm:left-4 sm:text-lg lg:right-6 lg:bottom-6 lg:left-6 lg:text-xl">{{ $category['name'] }}</h3>
                </a>
            @endforeach
        </div>
    </section>

    <section class="px-5 pt-12 pb-12 text-center sm:px-8 sm:pt-16 sm:pb-16" aria-labelledby="appointment-title">
        <div class="flex min-h-[420px] w-full flex-col items-center justify-center gap-3 bg-grhs-sand px-5 py-12 sm:px-8 sm:py-16">
            <h2 id="appointment-title" class="text-3xl leading-tight font-normal sm:text-4xl lg:text-[2.2rem]">Make an appointment.</h2>
            <p class="max-w-3xl text-lg leading-relaxed sm:text-2xl">Send us a message and we’ll get back to you shortly.</p>
            <a href="https://wa.me/971585338524" target="_blank" rel="noopener noreferrer" aria-label="Write on WhatsApp, number +971 58 533 8524" class="mt-6 inline-flex min-h-15 w-full max-w-[370px] items-center justify-center gap-3 border border-grhs-ink bg-grhs-paper px-5 text-lg font-semibold text-grhs-ink transition-colors hover:bg-[#f3f0e8] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-grhs-ink motion-reduce:transition-none sm:mt-8 sm:min-h-[77px] sm:text-xl">
                <img src="{{ asset('images/site/whatsapp.svg') }}" alt="" aria-hidden="true" class="h-6 w-6 shrink-0" width="28" height="28" loading="lazy">
                <span>Write on WhatsApp</span>
            </a>
        </div>
    </section>

    <section class="overflow-hidden py-12 sm:py-16" aria-labelledby="our-brands-title">
        <div class="mb-12 px-5 sm:mb-16 sm:px-8">
            <h2 id="our-brands-title" class="text-2xl font-semibold tracking-[0.12em] uppercase sm:text-3xl">OUR BRANDS</h2>
        </div>
        <div class="swiper h-[105px] w-full" data-brands-slider aria-label="Our brands">
            <div class="swiper-wrapper items-center">
                @foreach ($brands as $brand)
                    <div class="swiper-slide !flex h-full items-center justify-center px-4 sm:px-6">
                        <img src="{{ asset("images/home/brands/{$brand['image']}") }}" alt="{{ $brand['name'] }} logo" width="{{ $brand['width'] }}" height="{{ $brand['height'] }}" class="h-auto max-h-[72px] w-auto max-w-full object-contain sm:max-h-[90px]" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
