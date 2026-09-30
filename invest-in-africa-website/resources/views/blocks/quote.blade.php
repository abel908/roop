<section class="on-dark grain relative overflow-hidden bg-ink text-paper">
    <x-rays class="absolute -top-[36rem] -left-[36rem] size-[72rem] opacity-15" :count="22" :from="0" :to="90" :animate="false" />
    <div class="container-site section-y relative">
        <figure class="reveal max-w-5xl">
            <blockquote class="font-display text-3xl leading-tight font-extrabold md:text-5xl">
                <span class="text-brand-gold" aria-hidden="true">“</span>{{ tv($data['text'] ?? null) }}<span class="text-brand-gold" aria-hidden="true">”</span>
            </blockquote>
            @if ($author = tv($data['author'] ?? null))
                <figcaption class="mt-8 flex items-center gap-4 text-ink-300"><x-tricolor class="!w-8" />{{ $author }}</figcaption>
            @endif
        </figure>
    </div>
</section>
