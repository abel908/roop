@extends('layouts.site')

@php
    $heroVideo = data_get($settings, 'hero.video');
    $heroPoster = data_get($settings, 'hero.poster');
    $map = json_decode(file_get_contents(resource_path('data/africa-map.json')), true);
    $countryNames = \App\Support\Countries::african();
@endphp

@push('head')
    @if ($heroPoster)
        <link rel="preload" as="image" href="{{ asset('storage/'.$heroPoster) }}" fetchpriority="high">
    @endif
@endpush

@section('content')

    {{-- 1 · HERO — institutional impression from the first second --}}
    <section class="on-dark grain relative isolate flex min-h-[calc(100svh-5rem)] flex-col overflow-hidden bg-ink text-paper lg:min-h-[calc(100svh-6rem)]" aria-labelledby="hero-title">
        @if ($heroVideo)
            <video class="absolute inset-0 -z-10 size-full object-cover opacity-55" autoplay muted loop playsinline preload="none"
                   @if($heroPoster) poster="{{ asset('storage/'.$heroPoster) }}" @endif aria-hidden="true">
                <source src="{{ asset('storage/'.$heroVideo) }}" type="video/mp4" media="(min-width: 768px) and (prefers-reduced-motion: no-preference)">
            </video>
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-ink via-ink/80 to-ink/20"></div>
        @elseif ($heroPoster)
            <x-picture :src="$heroPoster" eager class="absolute inset-0 -z-10" img-class="size-full object-cover opacity-55" />
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-ink via-ink/80 to-ink/20"></div>
        @else
            <x-rays class="absolute -right-[32rem] -bottom-[32rem] -z-10 size-[64rem] opacity-90 lg:-right-[45rem] lg:-bottom-[45rem] lg:size-[90rem]" :count="30" :from="180" :to="270" :fill="0.52" />
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_20%_40%,rgba(0,0,0,.92)_0%,rgba(0,0,0,.55)_55%,rgba(0,0,0,0)_100%)]"></div>
        @endif

        <div class="container-site flex flex-1 flex-col justify-center py-16 md:py-24">
            <div class="max-w-4xl">
                <p class="eyebrow eyebrow-on-dark reveal mb-7"><x-tricolor class="!w-10" />{{ __('home.hero.eyebrow') }}</p>
                <h1 id="hero-title" class="text-display reveal" style="--reveal-delay: 80ms">{!! __('home.hero.title') !!}</h1>
                <p class="text-lead reveal mt-7 max-w-2xl text-ink-200" style="--reveal-delay: 160ms">{{ __('home.hero.subtitle') }}</p>
                <x-cta-buttons location="hero" class="reveal mt-10" style="--reveal-delay: 240ms" />
            </div>
        </div>

        <div class="container-site relative">
            <dl class="grid grid-cols-3 border-t border-ink-700 py-6 text-sm md:py-8">
                @foreach ([['6', __('home.hero.fact_domains')], ['2', __('home.hero.fact_pathways')], [__('home.hero.fact_languages'), __('home.hero.fact_languages_label')]] as [$value, $label])
                    <div @class(['flex flex-col-reverse gap-1', 'border-l border-ink-700 pl-4' => ! $loop->first, 'pr-4' => ! $loop->last])>
                        <dt class="text-ink-300">{{ $label }}</dt>
                        <dd @class(['font-display font-extrabold text-brand-gold', 'text-2xl md:text-3xl' => $loop->index < 2, 'text-base whitespace-nowrap sm:text-xl md:text-3xl' => $loop->last])>{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 2 · INTRODUCTION — positioning + animated key figures --}}
    <section class="section-y bg-paper" aria-labelledby="intro-title">
        <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <x-section-heading :eyebrow="__('home.intro.eyebrow')" :title="__('home.intro.title')" />
            </div>
            <div class="reveal lg:col-span-6 lg:col-start-7" style="--reveal-delay: 120ms">
                <p class="text-lead text-ink-600">{{ __('home.intro.text') }}</p>
                <a href="{{ lroute('about') }}" class="link-arrow mt-8">{{ __('home.intro.link') }} <x-glyph name="arrow-right" :size="18" /></a>
            </div>
        </div>
        @if ($figures)
            <div class="container-site mt-16 lg:mt-24">
                <div class="grid gap-10 border-t border-ink-200 pt-12 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($figures as $i => $figure)
                        <x-stat :value="$figure['value'] ?? 0" :suffix="$figure['suffix'] ?? ''" :label="data_get($figure, 'label.'.$currentLocale) ?: data_get($figure, 'label.en')" style="--reveal-delay: {{ $i * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- 3 · OUR MISSION — manifesto --}}
    <section class="on-dark grain relative overflow-hidden bg-ink text-paper" aria-labelledby="mission-title">
        <x-rays class="absolute -top-[40rem] -left-[40rem] size-[72rem] opacity-[0.16]" :count="22" :from="0" :to="90" :fill="0.5" />
        <div class="container-site section-y relative grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-3">
                <h2 id="mission-title" class="eyebrow eyebrow-on-dark reveal"><x-tricolor class="!w-8" />{{ __('home.mission.eyebrow') }}</h2>
            </div>
            <div class="lg:col-span-9">
                <blockquote class="reveal">
                    <p class="font-display text-[1.75rem] leading-[1.2] font-extrabold tracking-tight md:text-[2.75rem] lg:text-5xl">
                        <span class="text-brand-gold" aria-hidden="true">“</span>{{ __('home.mission.quote') }}<span class="text-brand-gold" aria-hidden="true">”</span>
                    </p>
                </blockquote>
                <div class="reveal mt-10 grid gap-8 md:grid-cols-2" style="--reveal-delay: 120ms">
                    <p class="text-lead text-ink-300">{{ __('home.mission.text') }}</p>
                    <div class="md:self-end md:justify-self-end">
                        <a href="{{ lroute('mission') }}" class="btn btn-outline">{{ __('home.mission.link') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" /></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4 · WHAT WE DO — the six official areas --}}
    <section class="section-y bg-paper" aria-labelledby="domains-title">
        <div class="container-site">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <x-section-heading id="domains-title" :eyebrow="__('home.domains.eyebrow')" :title="__('home.domains.title')" :lead="__('home.domains.lead')" />
                <a href="{{ lroute('what-we-do') }}" class="link-arrow reveal shrink-0">{{ __('site.mega.domains_all') }} <x-glyph name="arrow-right" :size="18" /></a>
            </div>
            <ul class="mt-14 grid gap-px bg-ink-200 shadow-[0_0_0_1px_var(--color-ink-200)] sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($domains as $domain)
                    <li class="reveal bg-paper" style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms">
                        @include('partials.domain-card', ['domain' => $domain])
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- 5 · INVESTMENT / PROJECTS — opportunities made tangible --}}
    <section class="section-y bg-ink-50" aria-labelledby="projects-title">
        <div class="container-site">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <x-section-heading id="projects-title" :eyebrow="__('home.projects.eyebrow')" :title="__('home.projects.title')" :lead="__('home.projects.lead')" />
                @if ($featuredProjects->isNotEmpty())
                    <a href="{{ lroute('invest') }}" class="btn btn-invest reveal shrink-0" data-track="cta_invest_click" data-track-location="home_projects">
                        {{ __('site.cta.view_all_projects') }} @if($projectCount)<span class="opacity-80">({{ $projectCount }})</span>@endif <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
                    </a>
                @endif
            </div>

            @if ($featuredProjects->isNotEmpty())
                <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featuredProjects as $project)
                        <x-project-card :project="$project" class="reveal" style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms" />
                    @endforeach
                </div>
            @else
                <div class="reveal mt-14 flex flex-col gap-8 bg-paper p-8 shadow-[inset_0_0_0_1px_var(--color-ink-200)] md:flex-row md:items-center md:justify-between md:p-12">
                    <div class="max-w-2xl">
                        <p class="text-h3 font-display font-extrabold">{{ __('home.projects.empty_title') }}</p>
                        <p class="mt-3 text-ink-500">{{ __('home.projects.empty_text') }}</p>
                    </div>
                    <x-cta-buttons location="home_projects_empty" class="shrink-0" />
                </div>
            @endif
        </div>
    </section>

    {{-- 6 · GET INVOLVED — split screen between the two pathways --}}
    <section aria-labelledby="involved-title" class="bg-paper">
        <div class="container-site pt-20 pb-12 lg:pt-28">
            <x-section-heading id="involved-title" :eyebrow="__('home.involved.eyebrow')" :title="__('home.involved.title')" align="center" class="mx-auto" />
        </div>
        <div class="grid lg:grid-cols-2">
            <div class="relative overflow-hidden bg-brand-green px-5 py-16 text-paper md:px-12 lg:px-16 lg:py-24">
                <x-rays class="absolute -right-80 -bottom-96 size-[48rem] opacity-20" :count="18" :palette="['#ffffff', '#08703A']" :animate="false" />
                <div class="reveal relative ml-auto max-w-xl">
                    <p class="text-label opacity-90">A · {{ __('site.mega.for_investors') }}</p>
                    <h3 class="mt-4 font-display text-3xl font-extrabold md:text-4xl">{{ __('home.involved.invest_title') }}</h3>
                    <p class="mt-5 text-lg font-medium">{{ __('home.involved.invest_text') }}</p>
                    <ul class="mt-8 space-y-3 text-lg font-semibold">
                        @foreach (__('home.involved.invest_points') as $point)
                            <li class="flex gap-3"><x-glyph name="check" :size="22" class="mt-0.5 shrink-0" />{{ $point }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ lroute('invest') }}" class="btn mt-10 bg-paper text-ink hover:bg-ink hover:text-paper" data-track="cta_invest_click" data-track-location="home_split">
                        {{ __('site.cta.invest') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
                    </a>
                </div>
            </div>
            <div class="on-dark relative overflow-hidden bg-ink px-5 py-16 text-paper md:px-12 lg:px-16 lg:py-24">
                <x-rays class="absolute -right-80 -bottom-96 size-[48rem] opacity-25" :count="18" :palette="['#FEC43F', '#262626']" :animate="false" />
                <div class="reveal relative max-w-xl" style="--reveal-delay: 100ms">
                    <p class="text-label text-brand-gold">B · {{ __('site.mega.for_holders') }}</p>
                    <h3 class="mt-4 font-display text-3xl font-extrabold text-brand-gold md:text-4xl">{{ __('home.involved.submit_title') }}</h3>
                    <p class="mt-5 text-lg text-ink-200">{{ __('home.involved.submit_text') }}</p>
                    <ul class="mt-8 space-y-3 text-lg font-semibold">
                        @foreach (__('home.involved.submit_points') as $point)
                            <li class="flex gap-3"><x-glyph name="check" :size="22" class="mt-0.5 shrink-0 text-brand-gold" />{{ $point }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ lroute('submit') }}" class="btn btn-gold mt-10" data-track="cta_submit_click" data-track-location="home_split">
                        {{ __('site.cta.submit') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 7 · IMPACT / AFRICA — interactive map of the continent --}}
    <section class="on-dark grain relative overflow-hidden bg-ink text-paper" aria-labelledby="impact-title"
             x-data="africaMap(@js((object) $mapCountries))">
        <div class="container-site section-y grid items-center gap-14 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <x-section-heading id="impact-title" dark :eyebrow="__('home.impact.eyebrow')" :title="__('home.impact.title')" :lead="__('home.impact.lead')" />

                <div class="reveal mt-10 min-h-36 border-l-2 border-brand-gold pl-6" aria-live="polite">
                    <template x-if="active">
                        <div>
                            <p class="font-display text-3xl font-extrabold" x-text="$refs['name-' + active]?.textContent"></p>
                            <p class="mt-2 text-ink-300" x-text="$refs['count-' + active]?.textContent"></p>
                            <a :href="'{{ lroute('invest') }}?country=' + active" class="link-arrow mt-5" data-track="map_country_click">{{ __('home.impact.map_view') }} <x-glyph name="arrow-right" :size="18" /></a>
                        </div>
                    </template>
                    <p x-show="!active" class="text-ink-300">{{ __('home.impact.map_hint') }}</p>
                </div>

                <ul class="mt-10 flex flex-wrap gap-6 text-sm text-ink-300">
                    <li class="flex items-center gap-2"><span class="size-3 bg-brand-green"></span>{{ __('home.impact.map_legend_projects') }}</li>
                    <li class="flex items-center gap-2"><span class="size-3 bg-[#2a2a2a] shadow-[inset_0_0_0_1px_#444]"></span>{{ __('home.impact.map_legend_other') }}</li>
                </ul>

                {{-- Accessible alternative to the map --}}
                @if ($mapCountries)
                    <ul class="mt-8 flex flex-wrap gap-2">
                        @foreach ($mapCountries as $code => $total)
                            <li>
                                <a href="{{ lroute('invest', ['country' => $code]) }}" class="inline-flex min-h-10 items-center gap-2 px-3 text-sm shadow-[inset_0_0_0_1px_var(--color-ink-600)] hover:bg-paper hover:text-ink"
                                   x-on:mouseenter="select('{{ $code }}')" x-on:focus="select('{{ $code }}')">
                                    <span x-ref="name-{{ $code }}">{{ \App\Support\Countries::name($code) }}</span>
                                    <span class="font-bold text-brand-gold">{{ $total }}</span>
                                    <span class="sr-only" x-ref="count-{{ $code }}">{{ trans_choice('home.impact.map_projects', $total) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="reveal lg:col-span-7" style="--reveal-delay: 120ms">
                <svg viewBox="{{ $map['viewBox'] }}" class="mx-auto h-auto w-full max-w-2xl" role="img" aria-labelledby="map-title">
                    <title id="map-title">{{ __('home.impact.title') }}</title>
                    @foreach ($map['countries'] as $country)
                        @php($code = strtoupper($country['id']))
                        @php($has = isset($mapCountries[$code]))
                        <path d="{{ $country['path'] }}"
                              @class(['africa-country', 'has-projects' => $has])
                              :class="active === '{{ $code }}' && 'is-active'"
                              @if($has)
                                  x-on:mouseenter="select('{{ $code }}')" x-on:click="window.location.href = '{{ lroute('invest', ['country' => $code]) }}'"
                              @endif>
                            <title>{{ $countryNames[$code] ?? $country['name'] }}</title>
                        </path>
                    @endforeach
                </svg>
            </div>
        </div>

        @if ($figures)
            <div class="container-site relative pb-20 lg:pb-28">
                <div class="grid gap-10 border-t border-ink-700 pt-12 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($figures as $i => $figure)
                        <x-stat dark :value="$figure['value'] ?? 0" :suffix="$figure['suffix'] ?? ''" :label="data_get($figure, 'label.'.$currentLocale) ?: data_get($figure, 'label.en')" style="--reveal-delay: {{ $i * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        @endif
        @if (data_get($settings, 'impact.stories'))
            <div class="container-site relative pb-20 lg:pb-28">
                @include('partials.stories', ['dark' => true])
            </div>
        @endif
    </section>

    {{-- 8 · PARTNERS — logo band, proportions respected --}}
    <section class="section-y bg-paper" aria-labelledby="partners-title">
        <div class="container-site">
            @if ($partners->isNotEmpty())
                <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                    <x-section-heading id="partners-title" :eyebrow="__('home.partners.eyebrow')" :title="__('home.partners.title')" />
                    <a href="{{ lroute('partners') }}" class="link-arrow reveal shrink-0">{{ __('home.partners.link') }} <x-glyph name="arrow-right" :size="18" /></a>
                </div>
                <div class="marquee reveal relative mt-14 overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_8%,#000_92%,transparent)]">
                    <ul class="{{ $partners->count() > 4 ? 'marquee-track flex w-max' : 'grid grid-cols-2 md:grid-cols-4' }}">
                        @foreach ($partners->count() > 4 ? $partners->concat($partners) : $partners as $partner)
                            <li class="flex h-32 w-56 shrink-0 items-center justify-center px-8 md:w-64" @if($loop->index >= $partners->count()) aria-hidden="true" @endif>
                                @include('partials.partner-logo', ['partner' => $partner, 'tabbable' => $loop->index < $partners->count()])
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="reveal grid items-center gap-8 lg:grid-cols-12">
                    <div class="lg:col-span-8">
                        <p class="eyebrow mb-5"><x-tricolor class="!w-8" />{{ __('home.partners.eyebrow') }}</p>
                        <h2 id="partners-title" class="text-h2">{{ __('home.partners.empty_title') }}</h2>
                        <p class="text-lead mt-4 text-ink-500">{{ __('home.partners.empty_text') }}</p>
                    </div>
                    <div class="lg:col-span-4 lg:justify-self-end">
                        <a href="{{ lroute('contact', ['subject' => 'partnership']) }}" class="btn btn-dark">{{ __('site.cta.contact') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" /></a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- 9 · CALL TO ACTION --}}
    @include('partials.cta-band')

@endsection
