<footer class="w-full bg-grhs-paper px-5 py-10 sm:px-8 sm:py-[70px]">
    <div class="w-full">
        <div class="flex flex-col gap-8 border-b border-black/10 pb-8 nav:flex-row nav:items-center nav:justify-between">
            <address class="flex flex-col gap-4 not-italic text-sm leading-relaxed sm:grid sm:grid-cols-2 sm:gap-x-8">
                <div class="flex items-center gap-3">
                    <img class="h-[18px] w-[18px] object-contain" src="{{ asset('images/site/geo.svg') }}" alt="">
                    <span>Office 2203, 22th floor, Ontario Tower, Business Bay, Dubai, UAE</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="tel:+971569421220" aria-label="Call GRHS using the phone icon number">
                        <img class="h-[18px] w-[18px] object-contain" src="{{ asset('images/site/phone.svg') }}" alt="">
                    </a>
                    <a class="hover:text-grhs-olive" href="tel:+971585338524">+971 58 533 8524</a>
                </div>
                <div class="flex items-center gap-3">
                    <img class="h-[18px] w-[18px] object-contain" src="{{ asset('images/site/mail.svg') }}" alt="">
                    <a class="hover:text-grhs-olive" href="mailto:sales@grhs.ae">sales@grhs.ae</a>
                </div>
                <div class="flex items-center gap-3">
                    <img class="h-[18px] w-[18px] object-contain" src="{{ asset('images/site/insta.svg') }}" alt="">
                    <a class="hover:text-grhs-olive" href="https://www.instagram.com/grhs_uae">grhs_uae</a>
                </div>
            </address>
            <a class="self-start nav:self-center" href="/" aria-label="GRHS home">
                <img class="w-[140px]" src="{{ asset('images/site/logo.svg') }}" alt="Golden Ratio Hospitality Solutions">
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 items-center gap-4 text-sm text-black/70 lg:grid-cols-5">
            <p class="lg:col-span-2">Copyright © {{ date('Y') }}</p>
            <button type="button" disabled aria-describedby="subscribe-status" class="min-h-9 cursor-not-allowed justify-self-center bg-grhs-ink px-6 text-sm font-bold uppercase text-white/75 lg:col-start-3">
                Subscribe to the news (not connected)
            </button>
            <span id="subscribe-status" class="sr-only">Newsletter subscription is not connected yet.</span>
            <ul class="flex flex-wrap gap-x-5 gap-y-2 lg:col-start-4 lg:col-span-2 lg:justify-self-end">
                <li><a href="#" class="hover:text-black">Legal notice</a></li>
                <li><a href="#" class="hover:text-black">Terms &amp; conditions</a></li>
                <li><a href="#" class="hover:text-black">Privace policy</a></li>
                <li><a href="#" class="hover:text-black">Cookies policy</a></li>
            </ul>
        </div>
    </div>
</footer>
