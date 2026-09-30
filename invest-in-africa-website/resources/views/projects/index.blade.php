@extends('layouts.site')

@php
    $select = function (string $name, string $label, array $options) use ($filters) {
        return ['name' => $name, 'label' => $label, 'options' => $options, 'value' => $filters[$name] ?? ''];
    };
    $filterFields = [
        $select('country', __('projects.filters.country'), $countries),
        $select('region', __('projects.filters.region'), \App\Enums\Region::options()),
        $select('sector', __('projects.filters.sector'), $sectors->mapWithKeys(fn ($s) => [$s->code => $s->tr('name')])->all()),
        $select('stage', __('projects.filters.stage'), \App\Enums\ProjectStage::options()),
        $select('range', __('projects.filters.range'), collect($ranges)->mapWithKeys(fn ($r) => [$r => __("projects.filters.ranges.$r")])->all()),
        $select('funding', __('projects.filters.funding'), \App\Enums\FundingType::options()),
    ];
@endphp

@section('content')
    <x-page-hero :eyebrow="__('projects.hero.eyebrow')" :title="__('projects.hero.title')" :lead="__('projects.hero.lead')" />

    <section class="section-y !pt-12 lg:!pt-16" x-data="projectFilters">
        <div class="container-site">
            <form x-ref="form" method="GET" action="{{ lroute('invest') }}" x-on:submit.prevent="submit()" role="search">
                {{-- Search + sort + mobile filter button --}}
                <div class="flex flex-col gap-4 md:flex-row md:items-end">
                    <div class="flex-1">
                        <label for="q" class="field-label">{{ __('projects.filters.search') }}</label>
                        <div class="relative">
                            <x-glyph name="search" :size="20" class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-ink-400" />
                            <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('projects.filters.search_placeholder') }}" class="field-input !pl-12" enterkeyhint="search">
                        </div>
                    </div>
                    <div class="md:w-64">
                        <label for="sort" class="field-label">{{ __('projects.filters.sort') }}</label>
                        <select id="sort" name="sort" class="field-input" x-on:change="submit()">
                            @foreach (['recent', 'amount_desc', 'amount_asc'] as $sort)
                                <option value="{{ $sort === 'recent' ? '' : $sort }}" @selected(($filters['sort'] ?? 'recent') === $sort)>{{ __("projects.filters.sort_$sort") }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="btn btn-outline lg:hidden" x-on:click="panelOpen = true" aria-controls="filters-panel" :aria-expanded="panelOpen">
                        <x-glyph name="filter" :size="18" /> {{ __('projects.filters.open') }}
                        @if ($activeFilters)<span class="flex size-6 items-center justify-center bg-brand-green text-xs text-paper">{{ $activeFilters }}</span>@endif
                    </button>
                    <button type="submit" class="btn btn-invest hidden lg:inline-flex">{{ __('projects.filters.apply') }}</button>
                </div>

                {{-- Filters: inline on desktop, dedicated panel on mobile (§6.2) --}}
                <div id="filters-panel"
                     class="fixed inset-0 z-[60] flex-col bg-paper lg:static lg:z-auto lg:mt-6 lg:flex lg:bg-transparent"
                     :class="panelOpen ? 'flex' : 'hidden'"
                     x-on:keydown.escape="panelOpen = false"
                     x-trap.noscroll="panelOpen && window.innerWidth < 1024">
                    <div class="flex h-16 items-center justify-between border-b border-ink-200 px-5 lg:hidden">
                        <p class="font-display text-lg font-bold">{{ __('projects.filters.title') }}</p>
                        <button type="button" class="-mr-2 inline-flex size-12 items-center justify-center" x-on:click="panelOpen = false" aria-label="{{ __('site.a11y.close') }}"><x-glyph name="close" :size="24" /></button>
                    </div>
                    <div class="grid flex-1 content-start gap-5 overflow-y-auto p-5 sm:grid-cols-2 lg:grid-cols-6 lg:gap-4 lg:overflow-visible lg:p-0">
                        @foreach ($filterFields as $field)
                            <div>
                                <label for="filter-{{ $field['name'] }}" class="field-label !text-sm">{{ $field['label'] }}</label>
                                <select id="filter-{{ $field['name'] }}" name="{{ $field['name'] }}" class="field-input !min-h-11 !py-2 !text-[0.9375rem]" x-on:change="window.innerWidth >= 1024 && submit()">
                                    <option value="">{{ __('projects.filters.all') }}</option>
                                    @foreach ($field['options'] as $value => $label)
                                        <option value="{{ $value }}" @selected((string) $field['value'] === (string) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-2 gap-3 border-t border-ink-200 p-5 lg:hidden">
                        <a href="{{ lroute('invest') }}" class="btn btn-outline">{{ __('projects.filters.reset') }}</a>
                        <button type="submit" class="btn btn-invest">{{ __('projects.filters.apply') }}</button>
                    </div>
                </div>
            </form>

            <div class="mt-10 flex flex-wrap items-center justify-between gap-4 border-b border-ink-200 pb-5">
                <p class="font-semibold" role="status">{{ trans_choice('projects.results', $projects->total()) }}</p>
                @if ($activeFilters)
                    <a href="{{ lroute('invest') }}" class="link text-sm">{{ __('projects.filters.reset') }}</a>
                @endif
            </div>

            @if ($projects->isNotEmpty())
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-project-card :project="$project" headingLevel="h2" class="reveal" style="--reveal-delay: {{ ($loop->index % 3) * 60 }}ms" />
                    @endforeach
                </div>
                <div class="mt-14">{{ $projects->onEachSide(1)->links('partials.pagination') }}</div>
            @else
                <div class="mt-10 flex flex-col gap-8 bg-ink-50 p-8 md:flex-row md:items-center md:justify-between md:p-12">
                    <div class="max-w-xl">
                        <h2 class="text-h3">{{ __('projects.empty_title') }}</h2>
                        <p class="mt-3 text-ink-500">{{ __('projects.empty_text') }}</p>
                    </div>
                    <a href="{{ lroute('contact', ['subject' => 'investment']) }}" class="btn btn-dark shrink-0">{{ __('site.cta.contact') }}</a>
                </div>
            @endif

            <p class="mt-16 max-w-4xl border-t border-ink-200 pt-6 text-sm text-ink-500">{{ __('projects.disclaimer') }}</p>
        </div>
    </section>
@endsection
