@props(['eyebrow' => null, 'title', 'lead' => null, 'dark' => true, 'rays' => true])
{{-- Inner page header: immersive black band, one H1 per page (§10.1) --}}
<section @class([
    'relative overflow-hidden',
    'bg-ink text-paper grain on-dark' => $dark,
    'bg-paper text-ink border-b border-ink-200' => ! $dark,
])>
    @if ($rays && $dark)
        <x-rays class="absolute -right-[22rem] -bottom-[30rem] h-[64rem] w-[64rem] opacity-[0.22] md:-right-40 lg:-bottom-[26rem]" :count="24" :from="180" :to="270" :fill="0.5" />
    @endif
    <div class="container-site relative pt-10 pb-16 md:pt-14 md:pb-24 lg:pb-28">
        <x-breadcrumbs :dark="$dark" />
        <div class="mt-10 max-w-4xl md:mt-16">
            @if ($eyebrow)
                <p @class(['eyebrow mb-6', 'eyebrow-on-dark' => $dark])><x-tricolor class="!w-8" />{{ $eyebrow }}</p>
            @endif
            <h1 class="text-h1">{!! $title !!}</h1>
            @if ($lead)
                <p @class(['text-lead mt-6 max-w-3xl', 'text-ink-300' => $dark, 'text-ink-500' => ! $dark])>{{ $lead }}</p>
            @endif
            {{ $slot }}
        </div>
    </div>
</section>
