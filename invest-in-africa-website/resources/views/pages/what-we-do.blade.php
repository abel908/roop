@extends('layouts.site')

@section('content')
    <x-page-hero :eyebrow="__('whatwedo.hero.eyebrow')" :title="__('whatwedo.hero.title')" :lead="__('whatwedo.hero.lead')" />

    <section class="section-y" aria-label="{{ __('site.nav.what_we_do') }}">
        <div class="container-site">
            <ol class="divide-y divide-ink-200 border-y border-ink-200">
                @foreach ($domains as $domain)
                    <li class="reveal">
                        <a href="{{ $domain->url() }}" class="group grid gap-6 py-10 transition-colors hover:bg-ink-50 md:grid-cols-12 md:items-center md:px-6 lg:py-12">
                            <span class="font-display text-5xl font-extrabold text-ink-200 transition-colors group-hover:text-brand-green md:col-span-1">{{ $domain->numberLabel() }}</span>
                            <x-domain-icon :icon="$domain->icon" :size="56" class="text-ink md:col-span-1" />
                            <div class="md:col-span-6 md:pl-4">
                                <h2 class="text-h3 lg:text-[1.75rem]">{{ $domain->tr('title') }}</h2>
                                <p class="mt-3 text-base text-ink-500">{{ $domain->tr('tagline') }}</p>
                            </div>
                            <div class="md:col-span-3 md:col-start-9">
                                <p class="text-label text-ink-400 !text-xs">{{ __('whatwedo.audience') }}</p>
                                <p class="mt-1 font-semibold">{{ $domain->tr('audience') }}</p>
                            </div>
                            <span class="hidden size-12 items-center justify-center justify-self-end shadow-[inset_0_0_0_1px_var(--color-ink-300)] transition group-hover:bg-ink group-hover:text-paper md:col-span-1 md:flex">
                                <x-glyph name="arrow-right" :size="20" />
                            </span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    @include('partials.cta-band')
@endsection
