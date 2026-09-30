<section class="on-dark grain relative isolate overflow-hidden bg-ink text-paper">
    <x-rays class="absolute -right-[30rem] -bottom-[36rem] -z-10 size-[66rem] opacity-60 md:-right-[18rem]" :count="26" />
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-ink via-ink/90 to-ink/30"></div>
    <div class="container-site py-20 lg:py-28">
        <div class="reveal max-w-3xl">
            <x-tricolor />
            <h2 class="text-h1 mt-8">{{ tv($data['title'] ?? null) }}</h2>
            @if ($text = tv($data['text'] ?? null))<p class="text-lead mt-6 text-ink-300">{{ $text }}</p>@endif
            <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center">
                @if (! empty($data['show_ctas']))<x-cta-buttons location="page_cta" />@endif
                @if (($label = tv($data['button_label'] ?? null)) && ! empty($data['button_url']))
                    <a href="{{ $data['button_url'] }}" class="btn btn-outline">{{ $label }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" /></a>
                @endif
            </div>
        </div>
    </div>
</section>
