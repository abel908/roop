{{-- Immersive closing band: strong message, main actions, Contact Us link (§5.1 section 9) --}}
<section class="on-dark grain relative isolate overflow-hidden bg-ink text-paper" aria-labelledby="cta-band-title">
    <x-rays class="absolute -right-[30rem] -bottom-[36rem] -z-10 size-[66rem] opacity-60 md:-right-[18rem]" :count="26" :from="180" :to="270" />
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-ink via-ink/90 to-ink/30"></div>
    <div class="container-site py-24 lg:py-32">
        <div class="reveal max-w-3xl">
            <x-tricolor />
            <h2 id="cta-band-title" class="text-h1 mt-8">{{ __('home.cta.title') }}</h2>
            <p class="text-lead mt-6 text-ink-300">{{ __('home.cta.text') }}</p>
            <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center">
                <x-cta-buttons location="cta_band" />
                <a href="{{ lroute('contact') }}" class="link-arrow sm:ml-4">{{ __('site.cta.contact') }} <x-glyph name="arrow-right" :size="18" /></a>
            </div>
        </div>
    </div>
</section>
