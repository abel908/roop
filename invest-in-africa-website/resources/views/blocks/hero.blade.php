@php($dark = $data['dark'] ?? true)
<section @class(['relative overflow-hidden', 'on-dark grain bg-ink text-paper' => $dark, 'bg-paper text-ink border-b border-ink-200' => ! $dark])>
    @if (! empty($data['image']))
        <x-picture :src="$data['image']" class="absolute inset-0" img-class="size-full object-cover {{ $dark ? 'opacity-45' : 'opacity-100' }}" :eager="$first" />
        @if ($dark)<div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/85 to-ink/20"></div>@endif
    @elseif ($dark)
        <x-rays class="absolute -right-[30rem] -bottom-[36rem] size-[66rem] opacity-40" :count="24" />
    @endif
    <div class="container-site relative py-16 md:py-24">
        @if ($first)<x-breadcrumbs :dark="$dark" />@endif
        <div class="mt-10 max-w-4xl">
            @if ($eyebrow = tv($data['eyebrow'] ?? null))
                <p @class(['eyebrow mb-6', 'eyebrow-on-dark' => $dark])><x-tricolor class="!w-8" />{{ $eyebrow }}</p>
            @endif
            <{{ $first ? 'h1' : 'h2' }} class="{{ $first ? 'text-h1' : 'text-h2' }}">{{ tv($data['title'] ?? null) }}</{{ $first ? 'h1' : 'h2' }}>
            @if ($lead = tv($data['lead'] ?? null))
                <p @class(['text-lead mt-6 max-w-3xl', 'text-ink-300' => $dark, 'text-ink-500' => ! $dark])>{{ $lead }}</p>
            @endif
            @if (! empty($data['show_ctas']))
                <x-cta-buttons class="mt-10" location="page_hero" />
            @endif
        </div>
    </div>
</section>
