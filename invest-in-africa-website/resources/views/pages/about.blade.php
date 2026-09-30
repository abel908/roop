@extends('layouts.site')

@section('content')
    <x-page-hero :eyebrow="__('about.hero.eyebrow')" :title="__('about.hero.title')" :lead="__('about.hero.lead')" />

    {{-- Organisation — large editorial introduction --}}
    <section class="section-y" aria-labelledby="organisation-title">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-section-heading id="organisation-title" :eyebrow="__('about.organisation.eyebrow')" :title="__('about.organisation.title')" />
            </div>
            <div class="reveal space-y-6 lg:col-span-7 lg:col-start-6">
                <p class="font-display text-2xl leading-snug font-bold text-ink md:text-[1.75rem]">{{ __('about.organisation.text') }}</p>
                <p class="text-lead text-ink-600">{{ __('about.organisation.text_2') }}</p>
            </div>
        </div>
    </section>

    {{-- History — interactive timeline (content to be supplied by the institution) --}}
    @if ($timeline)
        <section class="section-y bg-ink-50" aria-labelledby="history-title" x-data="{ current: 0 }">
            <div class="container-site">
                <x-section-heading id="history-title" :eyebrow="__('about.history.eyebrow')" :title="__('about.history.title')" />
                <div class="mt-12 overflow-x-auto pb-2">
                    <ol class="flex min-w-max border-b border-ink-300" role="tablist">
                        @foreach ($timeline as $i => $event)
                            <li role="presentation">
                                <button type="button" role="tab" x-on:click="current = {{ $i }}" :aria-selected="current === {{ $i }}"
                                        class="relative -mb-px min-h-14 px-6 font-display text-xl font-extrabold transition-colors"
                                        :class="current === {{ $i }} ? 'text-brand-green-deep border-b-4 border-brand-green' : 'text-ink-400 hover:text-ink'">
                                    {{ $event['year'] ?? '' }}
                                </button>
                            </li>
                        @endforeach
                    </ol>
                </div>
                @foreach ($timeline as $i => $event)
                    <div x-show="current === {{ $i }}" @if($i) x-cloak @endif role="tabpanel" class="mt-10 max-w-3xl">
                        <h3 class="text-h3">{{ data_get($event, 'title.'.$currentLocale) ?: data_get($event, 'title.en') }}</h3>
                        <p class="text-lead mt-4 text-ink-600">{{ data_get($event, 'text.'.$currentLocale) ?: data_get($event, 'text.en') }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Vision — manifesto block --}}
    <section class="on-dark grain relative overflow-hidden bg-ink text-paper" aria-labelledby="vision-title">
        <x-rays class="absolute -right-[30rem] -bottom-[30rem] size-[60rem] opacity-25" :count="22" />
        <div class="container-site section-y relative">
            <h2 id="vision-title" class="eyebrow eyebrow-on-dark reveal"><x-tricolor class="!w-8" />{{ __('about.vision.eyebrow') }}</h2>
            <p class="reveal mt-8 max-w-5xl font-display text-3xl leading-tight font-extrabold md:text-5xl">{{ __('about.vision.text') }}</p>
        </div>
    </section>

    {{-- Mission — summary and link --}}
    <section class="section-y" aria-labelledby="about-mission-title">
        <div class="container-site grid items-end gap-10 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <x-section-heading id="about-mission-title" :eyebrow="__('about.mission.eyebrow')" :title="__('about.mission.title')" :lead="__('about.mission.text')" />
            </div>
            <div class="reveal lg:col-span-4 lg:col-start-9 lg:justify-self-end">
                <a href="{{ lroute('mission') }}" class="btn btn-outline">{{ __('about.mission.link') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" /></a>
            </div>
        </div>
    </section>

    {{-- Values — iconographic grid --}}
    <section class="section-y border-t border-ink-200" aria-labelledby="values-title">
        <div class="container-site">
            <x-section-heading id="values-title" :eyebrow="__('about.values.eyebrow')" :title="__('about.values.title')" />
            <ul class="mt-14 grid gap-px bg-ink-200 shadow-[0_0_0_1px_var(--color-ink-200)] sm:grid-cols-2 lg:grid-cols-4">
                @foreach (__('about.values.items') as $value)
                    <li class="reveal bg-paper p-8" style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <x-glyph :name="$value['icon']" :size="36" class="text-brand-green" />
                        <h3 class="text-h3 mt-8">{{ $value['title'] }}</h3>
                        <p class="mt-3 text-base text-ink-500">{{ $value['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Approach — step diagram --}}
    <section class="section-y bg-ink-50" aria-labelledby="approach-title">
        <div class="container-site">
            <x-section-heading id="approach-title" :eyebrow="__('about.approach.eyebrow')" :title="__('about.approach.title')" />
            <ol class="mt-14 grid gap-10 md:grid-cols-2 lg:grid-cols-4 lg:gap-0">
                @foreach (__('about.approach.steps') as $step)
                    <li class="reveal relative lg:pr-10" style="--reveal-delay: {{ $loop->index * 100 }}ms">
                        <div class="flex items-center gap-4">
                            <span class="flex size-14 shrink-0 items-center justify-center bg-ink font-display text-lg font-extrabold text-brand-gold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            @unless ($loop->last)<span class="hidden h-px flex-1 bg-ink-300 lg:block" aria-hidden="true"></span>@endunless
                        </div>
                        <h3 class="text-h3 mt-6">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-base text-ink-500">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Impact — key figures --}}
    @if ($figures)
        <section class="section-y" aria-labelledby="about-impact-title">
            <div class="container-site">
                <x-section-heading id="about-impact-title" :eyebrow="__('about.impact.eyebrow')" :title="__('about.impact.title')" />
                <div class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($figures as $figure)
                        <x-stat :value="$figure['value'] ?? 0" :suffix="$figure['suffix'] ?? ''" :label="data_get($figure, 'label.'.$currentLocale) ?: data_get($figure, 'label.en')" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Leadership — portraits, when the contents are supplied --}}
    @if ($leaders)
        <section class="section-y border-t border-ink-200" aria-labelledby="leadership-title">
            <div class="container-site">
                <x-section-heading id="leadership-title" :eyebrow="__('about.leadership.eyebrow')" :title="__('about.leadership.title')" />
                <ul class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($leaders as $leader)
                        <li class="reveal">
                            <div class="aspect-[4/5] overflow-hidden bg-ink-100">
                                @if (! empty($leader['photo']))
                                    <img src="{{ asset('storage/'.$leader['photo']) }}" alt="{{ $leader['name'] ?? '' }}" loading="lazy" class="size-full object-cover grayscale-0">
                                @endif
                            </div>
                            <h3 class="text-h3 mt-5">{{ $leader['name'] ?? '' }}</h3>
                            <p class="mt-1 font-semibold text-brand-green-deep">{{ data_get($leader, 'role.'.$currentLocale) ?: data_get($leader, 'role.en') }}</p>
                            <p class="mt-3 text-base text-ink-500">{{ data_get($leader, 'bio.'.$currentLocale) ?: data_get($leader, 'bio.en') }}</p>
                            @if (! empty($leader['linkedin']))
                                <a href="{{ $leader['linkedin'] }}" target="_blank" rel="noopener" class="link-arrow mt-4 text-sm"><x-glyph name="linkedin" :size="16" /> {{ __('about.leadership.profile') }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @include('partials.cta-band')
@endsection
