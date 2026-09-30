@extends('layouts.site')

@section('content')
    <x-page-hero :eyebrow="__('partners.hero.eyebrow')" :title="__('partners.hero.title')" :lead="__('partners.hero.lead')" />

    <section class="section-y" x-data="{ category: 'all' }">
        <div class="container-site">
            @if ($partners->isNotEmpty())
                @if ($categories->count() > 1)
                    <div class="mb-12 flex flex-wrap gap-2" role="group" aria-label="{{ __('projects.filters.title') }}">
                        <button type="button" x-on:click="category = 'all'" :aria-pressed="category === 'all'" class="btn btn-sm" :class="category === 'all' ? 'btn-dark' : 'btn-outline'">{{ __('partners.all') }}</button>
                        @foreach ($categories as $category)
                            <button type="button" x-on:click="category = '{{ $category->value }}'" :aria-pressed="category === '{{ $category->value }}'" class="btn btn-sm" :class="category === '{{ $category->value }}' ? 'btn-dark' : 'btn-outline'">{{ $category->getLabel() }}</button>
                        @endforeach
                    </div>
                @endif
                {{-- Airy editorial grid: identical boxes, centred logos, generous margins (§5.5) --}}
                <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($partners as $partner)
                        <li x-show="category === 'all' || category === '{{ $partner->category->value }}'" x-transition.opacity class="card reveal">
                            <div class="flex h-44 items-center justify-center border-b border-ink-200 px-10 py-8">
                                @include('partials.partner-logo', ['partner' => $partner])
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <p class="text-label !text-xs text-brand-green-deep">{{ $partner->category->getLabel() }}</p>
                                <h2 class="text-h3 mt-2">{{ $partner->name }}</h2>
                                @if ($partner->tr('description'))
                                    <p class="mt-3 flex-1 text-base text-ink-500">{{ $partner->tr('description') }}</p>
                                @endif
                                @if ($partner->website_url)
                                    <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" class="link-arrow mt-5" data-track="partner_click" data-track-partner="{{ $partner->name }}">{{ __('partners.visit') }} <x-glyph name="arrow-up-right" :size="16" /></a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="reveal max-w-2xl">
                    <h2 class="text-h2">{{ __('partners.empty_title') }}</h2>
                    <p class="text-lead mt-4 text-ink-500">{{ __('partners.empty_text') }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="section-y bg-ink-50">
        <div class="container-site grid gap-8 lg:grid-cols-12 lg:items-center">
            <div class="reveal lg:col-span-8">
                <x-tricolor />
                <h2 class="text-h2 mt-6">{{ __('partners.become.title') }}</h2>
                <p class="text-lead mt-4 text-ink-500">{{ __('partners.become.text') }}</p>
            </div>
            <div class="reveal lg:col-span-4 lg:justify-self-end">
                <a href="{{ lroute('contact', ['subject' => 'partnership']) }}" class="btn btn-dark">{{ __('partners.become.cta') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" /></a>
            </div>
        </div>
    </section>
@endsection
