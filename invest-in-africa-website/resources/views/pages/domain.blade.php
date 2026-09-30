@extends('layouts.site')

@section('content')
    {{-- Header: official title, tagline, dedicated visual --}}
    <section class="on-dark grain relative overflow-hidden bg-ink text-paper">
        @if ($domain->cover_image)
            <x-picture :src="$domain->cover_image" eager class="absolute inset-0" img-class="size-full object-cover opacity-40" />
            <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/85 to-ink/30"></div>
        @else
            <x-rays class="absolute -right-[30rem] -bottom-[36rem] size-[66rem] opacity-30" :count="24" />
        @endif
        <div class="container-site relative pt-10 pb-16 md:pt-14 md:pb-24">
            <x-breadcrumbs />
            <div class="mt-12 grid gap-10 lg:grid-cols-12 lg:items-end">
                <div class="lg:col-span-8">
                    <p class="eyebrow eyebrow-on-dark mb-6"><x-tricolor class="!w-8" />{{ __('whatwedo.domain.area') }} {{ $domain->numberLabel() }}</p>
                    <h1 class="text-h1">{{ $domain->tr('title') }}</h1>
                    <p class="text-lead mt-6 max-w-3xl text-ink-300">{{ $domain->tr('tagline') }}</p>
                </div>
                <div class="hidden justify-self-end lg:col-span-4 lg:block">
                    <div class="flex size-44 items-center justify-center bg-paper text-ink">
                        <x-domain-icon :icon="$domain->icon" :size="96" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Presentation and stakes --}}
    <section class="section-y">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="reveal lg:col-span-7">
                <p class="font-display text-2xl leading-snug font-bold md:text-[1.75rem]">{{ $domain->tr('intro') }}</p>
            </div>
            @if ($domain->tr('challenges'))
                <aside class="reveal border-l-4 border-brand-gold bg-ink-50 p-8 lg:col-span-4 lg:col-start-9" style="--reveal-delay: 100ms">
                    <h2 class="text-label text-ink">{{ __('whatwedo.domain.challenges') }}</h2>
                    <p class="mt-4 text-base text-ink-600">{{ $domain->tr('challenges') }}</p>
                </aside>
            @endif
        </div>
    </section>

    {{-- Services --}}
    @if ($services = $domain->tr('services'))
        <section class="section-y border-t border-ink-200" aria-labelledby="services-title">
            <div class="container-site grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <x-section-heading id="services-title" :title="__('whatwedo.domain.services')" />
                </div>
                <ul class="grid gap-px bg-ink-200 shadow-[0_0_0_1px_var(--color-ink-200)] sm:grid-cols-2 lg:col-span-8">
                    @foreach ($services as $service)
                        <li class="reveal flex gap-4 bg-paper p-7" style="--reveal-delay: {{ $loop->index * 60 }}ms">
                            <x-glyph name="check" :size="22" class="mt-0.5 text-brand-green" />
                            <span class="font-semibold">{{ $service }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Audience and benefits --}}
    <section class="section-y bg-ink-50">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="reveal lg:col-span-4">
                <h2 class="text-h3">{{ __('whatwedo.domain.audience') }}</h2>
                <p class="mt-5 inline-flex items-center gap-3 bg-ink px-5 py-3 font-display font-bold text-brand-gold">
                    <x-glyph name="users" :size="20" />{{ $domain->tr('audience') }}
                </p>
            </div>
            @if ($benefits = $domain->tr('benefits'))
                <div class="reveal lg:col-span-7 lg:col-start-6">
                    <h2 class="text-h3">{{ __('whatwedo.domain.benefits') }}</h2>
                    <ul class="mt-6 grid gap-4 sm:grid-cols-3">
                        @foreach ($benefits as $benefit)
                            <li class="border-t-4 border-brand-green bg-paper p-5 font-semibold">{{ $benefit }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>

    {{-- Process in steps --}}
    @if ($steps = $domain->tr('steps'))
        <section class="section-y" aria-labelledby="process-title">
            <div class="container-site">
                <x-section-heading id="process-title" :title="__('whatwedo.domain.process')" />
                <ol class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($steps as $step)
                        <li class="reveal" style="--reveal-delay: {{ $loop->index * 80 }}ms">
                            <div class="flex items-center gap-4">
                                <span class="flex size-12 shrink-0 items-center justify-center bg-ink font-display font-extrabold text-brand-gold">{{ $loop->iteration }}</span>
                                @unless ($loop->last)<span class="hidden h-px flex-1 bg-ink-300 lg:block" aria-hidden="true"></span>@endunless
                            </div>
                            <p class="mt-5 font-display text-lg font-bold">{{ $step }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- Related projects --}}
    @if ($projects->isNotEmpty())
        <section class="section-y bg-ink-50" aria-labelledby="related-title">
            <div class="container-site">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <x-section-heading id="related-title" :title="__('whatwedo.domain.projects')" />
                    <a href="{{ lroute('invest') }}" class="link-arrow">{{ __('site.cta.view_all_projects') }} <x-glyph name="arrow-right" :size="18" /></a>
                </div>
                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-project-card :project="$project" class="reveal" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Downloadable documents --}}
    @if ($documents->isNotEmpty())
        <section class="section-y" aria-labelledby="documents-title">
            <div class="container-site">
                <x-section-heading id="documents-title" :title="__('whatwedo.domain.documents')" />
                <ul class="mt-10 grid gap-4 md:grid-cols-2">
                    @foreach ($documents as $document)
                        <li>
                            <a href="{{ asset('storage/'.$document->file) }}" download class="card card-hover flex items-center gap-5 p-6" data-track="document_download" data-track-document="{{ $document->tr('title') }}">
                                <span class="flex size-12 items-center justify-center bg-ink text-xs font-bold text-brand-gold">{{ $document->extension() }}</span>
                                <span class="flex-1 font-semibold">{{ $document->tr('title') }}</span>
                                <x-glyph name="download" :size="22" class="text-brand-green-deep" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Contextual call to action --}}
    <section class="section-y border-t border-ink-200">
        <div class="container-site grid gap-10 lg:grid-cols-12 lg:items-center">
            <div class="reveal lg:col-span-6">
                <x-tricolor />
                <h2 class="text-h2 mt-6">{{ __('whatwedo.domain.cta_title') }}</h2>
                <p class="text-lead mt-4 text-ink-500">{{ __('whatwedo.domain.cta_text') }}</p>
            </div>
            <div class="reveal flex flex-col gap-4 lg:col-span-6 lg:items-end">
                <x-cta-buttons location="domain_{{ $domain->number }}" />
                <a href="{{ lroute('contact') }}" class="link-arrow">{{ __('site.cta.contact') }} <x-glyph name="arrow-right" :size="18" /></a>
            </div>
        </div>
    </section>

    {{-- Navigation to the other areas --}}
    <nav class="bg-ink text-paper on-dark" aria-label="{{ __('whatwedo.domain.other_domains') }}">
        <div class="container-site grid md:grid-cols-2">
            @if ($previous)
                <a href="{{ $previous->url() }}" class="group flex items-center gap-5 border-ink-700 py-10 md:border-r md:pr-8">
                    <x-glyph name="arrow-left" :size="24" class="text-brand-gold transition group-hover:-translate-x-1" />
                    <span><span class="text-label block text-ink-400 !text-xs">{{ __('whatwedo.domain.previous') }}</span><span class="mt-1 block font-display text-lg font-bold">{{ $previous->tr('title') }}</span></span>
                </a>
            @else
                <span class="hidden md:block"></span>
            @endif
            @if ($next)
                <a href="{{ $next->url() }}" class="group flex items-center justify-end gap-5 border-t border-ink-700 py-10 text-right md:border-t-0 md:pl-8">
                    <span><span class="text-label block text-ink-400 !text-xs">{{ __('whatwedo.domain.next') }}</span><span class="mt-1 block font-display text-lg font-bold">{{ $next->tr('title') }}</span></span>
                    <x-glyph name="arrow-right" :size="24" class="text-brand-gold transition group-hover:translate-x-1" />
                </a>
            @endif
        </div>
    </nav>
@endsection
