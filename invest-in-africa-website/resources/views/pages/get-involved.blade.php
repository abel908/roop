@extends('layouts.site')

@section('content')
    <x-page-hero :eyebrow="__('involved.hero.eyebrow')" :title="__('involved.hero.title')" :lead="__('involved.hero.lead')" />

    {{-- The two pathways, side by side --}}
    <section class="grid lg:grid-cols-2" aria-label="{{ __('involved.compare.title') }}">
        <div class="relative overflow-hidden bg-brand-green px-5 py-16 text-paper md:px-12 lg:py-24">
            <x-rays class="absolute -right-80 -bottom-96 size-[48rem] opacity-20" :count="18" :palette="['#ffffff', '#08703A']" :animate="false" />
            <div class="reveal relative ml-auto max-w-xl">
                <p class="text-label opacity-90">{{ __('involved.invest.label') }}</p>
                <h2 class="mt-4 font-display text-3xl font-extrabold md:text-4xl">{{ __('involved.invest.promise') }}</h2>
                <p class="mt-4 text-lg font-semibold">{{ trans_choice('involved.invest.count', $projectCount) }}</p>
                <a href="{{ lroute('invest') }}" class="btn mt-10 bg-paper text-ink hover:bg-ink hover:text-paper" data-track="cta_invest_click" data-track-location="get_involved">
                    {{ __('site.cta.invest') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
                </a>
            </div>
        </div>
        <div class="on-dark relative overflow-hidden bg-ink px-5 py-16 text-paper md:px-12 lg:py-24">
            <x-rays class="absolute -right-80 -bottom-96 size-[48rem] opacity-25" :count="18" :palette="['#FEC43F', '#262626']" :animate="false" />
            <div class="reveal relative max-w-xl">
                <p class="text-label text-brand-gold">{{ __('involved.submit.label') }}</p>
                <h2 class="mt-4 font-display text-3xl font-extrabold text-brand-gold md:text-4xl">{{ __('involved.submit.promise') }}</h2>
                <p class="mt-4 text-lg text-ink-200">{{ __('involved.submit.journey') }}</p>
                <a href="{{ lroute('submit') }}" class="btn btn-gold mt-10" data-track="cta_submit_click" data-track-location="get_involved">
                    {{ __('site.cta.submit') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
                </a>
            </div>
        </div>
    </section>

    {{-- Comparison table (§6.1) --}}
    <section class="section-y" aria-labelledby="compare-title">
        <div class="container-site">
            <x-section-heading id="compare-title" :title="__('involved.compare.title')" />
            <div class="reveal mt-12 overflow-x-auto">
                <table class="w-full min-w-[40rem] border-collapse text-left text-base">
                    <thead>
                        <tr>
                            <th scope="col" class="w-1/5 bg-ink p-5 text-sm font-semibold text-paper"><span class="sr-only">—</span></th>
                            <th scope="col" class="w-2/5 bg-brand-green p-5 font-display font-bold text-paper">{{ __('involved.invest.label') }}</th>
                            <th scope="col" class="w-2/5 bg-ink p-5 font-display font-bold text-brand-gold">{{ __('involved.submit.label') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['audience', 'promise', 'journey', 'final', 'handling'] as $row)
                            <tr class="border-b border-ink-200">
                                <th scope="row" class="p-5 text-sm font-bold text-ink-500">{{ __("involved.compare.$row") }}</th>
                                <td class="p-5">{{ __("involved.invest.$row") }}</td>
                                <td class="p-5">{{ __("involved.submit.$row") }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="section-y bg-ink-50" aria-labelledby="faq-title">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-section-heading id="faq-title" :title="__('involved.faq.title')" />
            </div>
            <div class="divide-y divide-ink-200 border-y border-ink-200 lg:col-span-8">
                @foreach (__('involved.faq.items') as $item)
                    <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                        <h3>
                            <button type="button" x-on:click="open = !open" :aria-expanded="open" class="flex min-h-16 w-full items-center justify-between gap-6 py-5 text-left font-display text-lg font-bold">
                                {{ $item['q'] }}
                                <x-glyph name="chevron-down" :size="22" class="transition-transform" x-bind:class="open && 'rotate-180'" />
                            </button>
                        </h3>
                        <div x-show="open" x-collapse>
                            <p class="pb-6 text-ink-600">{{ $item['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
