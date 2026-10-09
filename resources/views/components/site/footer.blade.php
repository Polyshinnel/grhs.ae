<footer class="w-full bg-grhs-paper px-5 py-10 sm:px-8 sm:py-[70px]">
    <div class="w-full">
        <div class="flex flex-col gap-8 border-b border-black/10 pb-8 nav:flex-row nav:items-center nav:justify-between">
            <address class="flex flex-col gap-4 not-italic text-sm leading-relaxed sm:grid sm:grid-cols-2 sm:gap-x-8">
                @if (filled($contactSettings?->address))
                    <div class="flex items-center gap-3">
                        <img class="h-[18px] w-[18px] shrink-0 object-contain" src="{{ asset('images/site/geo.svg') }}" alt="">
                        <span>{{ $contactSettings->address }}</span>
                    </div>
                @endif
                @if (filled($contactSettings?->phone))
                    <div class="flex items-center gap-3">
                        <img class="h-[18px] w-[18px] object-contain" src="{{ asset('images/site/phone.svg') }}" alt="">
                        <a class="hover:text-grhs-olive" href="tel:{{ preg_replace('/[^+0-9]/', '', $contactSettings->phone) }}">{{ $contactSettings->phone }}</a>
                    </div>
                @endif
                @if (filled($contactSettings?->email))
                    <div class="flex items-center gap-3">
                        <img class="h-[18px] w-[18px] shrink-0 object-contain" src="{{ asset('images/site/mail.svg') }}" alt="">
                        <a class="break-all hover:text-grhs-olive" href="mailto:{{ $contactSettings->email }}">{{ $contactSettings->email }}</a>
                    </div>
                @endif
                @foreach ($contactSettings?->social_links ?? [] as $socialLink)
                    @if (is_array($socialLink) && filled($socialLink['platform'] ?? null) && filled($socialLink['text'] ?? null) && filled($socialLink['url'] ?? null))
                        <div class="flex items-center gap-3">
                            @if ($socialLink['platform'] === 'instagram')
                                <img class="h-[18px] w-[18px] shrink-0 object-contain" src="{{ asset('images/site/insta.svg') }}" alt="">
                            @elseif ($socialLink['platform'] === 'whatsapp')
                                <img class="h-[18px] w-[18px] shrink-0 object-contain" src="{{ asset('images/site/whatsapp.svg') }}" alt="">
                            @else
                                <span class="flex h-[18px] w-[18px] shrink-0 items-center justify-center rounded-full border border-grhs-ink/50 text-[9px] font-bold leading-none text-grhs-ink" aria-hidden="true">
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
                            <a class="break-words hover:text-grhs-olive" href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer">{{ $socialLink['text'] }}</a>
                        </div>
                    @endif
                @endforeach
            </address>
            <a class="self-start nav:self-center" href="/" aria-label="GRHS home">
                <img class="w-[140px]" src="{{ asset('images/site/logo.svg') }}" alt="Golden Ratio Hospitality Solutions">
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 items-center gap-4 text-sm text-black/70 lg:grid-cols-5">
            <p class="lg:col-span-2">Copyright © {{ date('Y') }}</p>
            <div class="justify-self-center lg:col-start-3">
                <script data-b24-form="click/10/2lqj1v" data-skip-moving="true">
                    (function(w, d, u) {
                        var s = d.createElement('script');
                        s.async = true;
                        s.src = u + '?' + (Date.now() / 180000 | 0);
                        var h = d.getElementsByTagName('script')[0];
                        h.parentNode.insertBefore(s, h);
                    })(window, document, 'https://cdn-ru.bitrix24.ru/b24247626/crm/form/loader_10.js');
                </script>
            </div>
            <ul class="flex flex-wrap gap-x-5 gap-y-2 lg:col-start-4 lg:col-span-2 lg:justify-self-end">
                <li><a href="#" class="hover:text-black">Legal notice</a></li>
                <li><a href="#" class="hover:text-black">Terms &amp; conditions</a></li>
                <li><a href="#" class="hover:text-black">Privace policy</a></li>
                <li><a href="#" class="hover:text-black">Cookies policy</a></li>
            </ul>
        </div>
    </div>
</footer>
