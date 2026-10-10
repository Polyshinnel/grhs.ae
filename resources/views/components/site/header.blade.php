<header data-header-theme="{{ $headerBlack ? 'black' : 'white' }}" class="absolute inset-x-0 top-0 z-[300] h-[90px] px-4 {{ $headerBlack ? 'text-grhs-ink' : 'text-white' }} sm:px-[30px]">
    <div class="relative flex h-full items-center justify-center">
        <a href="/" class="absolute left-2 top-5 z-10 sm:left-10" aria-label="GRHS home">
            <img class="h-[50px] w-[50px] object-contain {{ $headerBlack ? 'brightness-0' : '' }}" src="{{ asset('images/site/logo-header.svg') }}" alt="GRHS">
        </a>

        <nav class="hidden nav:block" aria-label="Main navigation">
            <ul class="flex items-center gap-6 xl:gap-10">
                @foreach ([['Main', '/'], ['Tableware', '/tableware'], ['Glassware', '/glassware'], ['Barware', '/barware'], ['Kitchenware', '/kitchenware'], ['Poolware', '/poolware'], ['Cutlery', '/cutlery'], ['Wood', '/wood'], ['Coffee Shop', '/coffee-shop'], ['Contacts', '/contacts']] as [$label, $href])
                    <li><a class="relative text-[13px] font-normal uppercase tracking-wide transition-colors {{ $headerBlack ? 'hover:text-black/60' : 'hover:text-white/70' }} xl:text-[18px]" href="{{ $href }}">{{ $label }}</a></li>
                @endforeach
            </ul>
        </nav>

        <button type="button" data-menu-toggle data-icon="{{ asset('images/site/menu.svg') }}" data-close-icon="{{ asset('images/site/cross.svg') }}" aria-controls="mobile-navigation" aria-expanded="false" aria-label="Open navigation menu" class="absolute right-1 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full border-2 {{ $headerBlack ? 'border-black/30 bg-white/40 focus-visible:outline-grhs-ink' : 'border-white/50 bg-black/10 focus-visible:outline-white' }} nav:hidden focus-visible:outline-2 focus-visible:outline-offset-4">
            <img class="h-6 w-6 object-contain {{ $headerBlack ? 'brightness-0' : '' }}" src="{{ asset('images/site/menu.svg') }}" alt="">
        </button>

        <nav id="mobile-navigation" data-mobile-menu hidden aria-label="Mobile navigation" class="absolute inset-x-0 top-[76px] z-20 max-h-[calc(100vh-92px)] overflow-y-auto rounded-sm bg-white px-7 py-6 text-grhs-ink shadow-xl nav:hidden">
            <img class="mx-auto mb-5 h-12 w-full object-contain object-center" src="{{ asset('images/site/menu-img.svg') }}" alt="">
            <ul class="grid grid-cols-2 gap-x-5 gap-y-4">
                @foreach ([['Main', '/'], ['Catalogues', '/catalogues'], ['Tableware', '/tableware'], ['Glassware', '/glassware'], ['Barware', '/barware'], ['Kitchenware', '/kitchenware'], ['Poolware', '/poolware'], ['Cutlery', '/cutlery'], ['Wood', '/wood'], ['Coffee Shop', '/coffee-shop'], ['Contacts', '/contacts']] as [$label, $href])
                    <li><a class="inline-flex min-h-10 items-center text-sm uppercase tracking-wide hover:text-grhs-olive focus-visible:outline-2 focus-visible:outline-grhs-olive" href="{{ $href }}">{{ $label }}</a></li>
                @endforeach
            </ul>
        </nav>
    </div>
</header>
