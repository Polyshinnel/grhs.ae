<div data-floating-actions data-cross-icon="{{ asset('images/site/cross.svg') }}" class="fixed bottom-6 right-4 z-[300] flex flex-col items-center gap-2.5 sm:bottom-[50px] sm:right-[15px]">
    <section id="contact-panel" data-floating-panel data-open="false" aria-hidden="true" inert aria-labelledby="contact-heading" class="absolute bottom-[70px] right-0 w-[min(280px,calc(100vw-2rem))] rounded bg-white p-6 shadow-lg ring-1 ring-black/15">
        <h2 id="contact-heading" class="mb-3 text-center text-xl font-bold uppercase">Contact us</h2>
        <form action="#" method="post" aria-describedby="contact-form-status" class="grid gap-2.5">
            <label class="sr-only" for="contact-name">Name</label>
            <input id="contact-name" name="name" autocomplete="name" placeholder="Name" class="h-9 border border-black/20 px-2.5 text-sm focus:border-grhs-olive focus:outline-none" required>
            <label class="sr-only" for="contact-email">Email</label>
            <input id="contact-email" name="email" type="email" autocomplete="email" placeholder="E-mail" class="h-9 border border-black/20 px-2.5 text-sm focus:border-grhs-olive focus:outline-none" required>
            <label class="sr-only" for="contact-phone">Phone</label>
            <input id="contact-phone" name="phone" type="tel" autocomplete="tel" placeholder="Phone" class="h-9 border border-black/20 px-2.5 text-sm focus:border-grhs-olive focus:outline-none">
            <p id="contact-form-status" class="mt-1 text-xs text-black/65">Form submission is not connected yet.</p>
            <button type="submit" disabled class="h-9 cursor-not-allowed bg-grhs-olive px-3 text-sm font-bold uppercase text-white/80" aria-describedby="contact-form-status">Send (not connected)</button>
        </form>
    </section>

    <a href="https://wa.me/971569421220" aria-label="Chat with GRHS on WhatsApp" class="flex h-[60px] w-[60px] items-center justify-center rounded-full border border-black/20 bg-white shadow-md transition-shadow hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive">
        <img class="h-[30px] w-[30px]" src="{{ asset('images/site/whatsapp.svg') }}" alt="">
    </a>
    <button type="button" data-action-toggle data-icon="{{ asset('images/site/chat2.svg') }}" aria-controls="contact-panel" aria-expanded="false" aria-label="Open contact form" class="flex h-[60px] w-[60px] cursor-pointer items-center justify-center rounded-full border border-black/20 bg-white shadow-md transition-shadow hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-grhs-olive">
        <img class="h-7 w-7" src="{{ asset('images/site/chat2.svg') }}" alt="">
    </button>
</div>
