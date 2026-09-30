@extends('layouts.site')

@section('content')
    <x-page-hero :eyebrow="__('mission.hero.eyebrow')" :title="__('mission.hero.title')" :lead="__('mission.hero.lead')" />

    {{-- Vision — large manifesto statement --}}
    <section class="section-y" aria-labelledby="vision-title">
        <div class="container-site">
            <h2 id="vision-title" class="eyebrow reveal"><x-tricolor class="!w-8" />{{ __('mission.vision.eyebrow') }}</h2>
            <p class="reveal mt-8 max-w-5xl font-display text-3xl leading-tight font-extrabold tracking-tight md:text-5xl lg:text-6xl">{{ __('mission.vision.text') }}</p>
        </div>
    </section>

    {{-- Mission — editorial text + highlight --}}
    <section class="section-y border-t border-ink-200" aria-labelledby="mission-role-title">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-section-heading id="mission-role-title" :eyebrow="__('mission.mission.eyebrow')" :title="__('mission.mission.title')" />
            </div>
            <div class="reveal lg:col-span-7 lg:col-start-6">
                <p class="text-lead text-ink-600">{{ __('mission.mission.text') }}</p>
                <blockquote class="mt-10 border-l-4 border-brand-gold bg-ink px-8 py-7 font-display text-xl leading-snug font-bold text-paper md:text-2xl">
                    {{ __('mission.mission.highlight') }}
                </blockquote>
            </div>
        </div>
    </section>

    {{-- Objectives — numbered list with pictograms --}}
    <section class="section-y bg-ink-50" aria-labelledby="objectives-title">
        <div class="container-site">
            <x-section-heading id="objectives-title" :eyebrow="__('mission.objectives.eyebrow')" :title="__('mission.objectives.title')" />
            <ol class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach (__('mission.objectives.items') as $objective)
                    <li class="card reveal p-8" style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms">
                        <div class="flex items-center justify-between">
                            <x-glyph :name="$objective['icon']" :size="32" class="text-brand-green" />
                            <span class="font-display text-4xl font-extrabold text-ink-200">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-h3 mt-8">{{ $objective['title'] }}</h3>
                        <p class="mt-3 text-base text-ink-500">{{ $objective['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Values — grid --}}
    <section class="section-y" aria-labelledby="mission-values-title">
        <div class="container-site">
            <x-section-heading id="mission-values-title" :eyebrow="__('mission.values.eyebrow')" :title="__('mission.values.title')" />
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

    {{-- Impact — indicators --}}
    <section class="on-dark grain relative overflow-hidden bg-ink text-paper" aria-labelledby="mission-impact-title">
        <x-rays class="absolute -top-[36rem] -left-[36rem] size-[72rem] opacity-20" :count="22" :from="0" :to="90" />
        <div class="container-site section-y relative">
            <x-section-heading id="mission-impact-title" dark :eyebrow="__('mission.impact.eyebrow')" :title="__('mission.impact.title')" :lead="__('mission.impact.text')" />
            @if ($figures)
                <div class="mt-14 grid gap-10 border-t border-ink-700 pt-12 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($figures as $figure)
                        <x-stat dark :value="$figure['value'] ?? 0" :suffix="$figure['suffix'] ?? ''" :label="data_get($figure, 'label.'.$currentLocale) ?: data_get($figure, 'label.en')" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('partials.cta-band')
@endsection
