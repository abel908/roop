@extends('layouts.site')

@php
    $facts = array_filter([
        __('projects.fields.country') => $project->countryName().' · '.$project->region?->getLabel(),
        __('projects.fields.sector') => $project->sector?->tr('name'),
        __('projects.fields.stage') => $project->stage->getLabel(),
        __('projects.fields.funding') => $project->funding_type->getLabel(),
        __('projects.fields.jobs') => $project->jobs_expected ? \App\Support\Money::number($project->jobs_expected) : null,
        __('projects.fields.domain') => $project->domain?->tr('title'),
    ]);
    $useOfFunds = $project->tr('use_of_funds');
    $hasInterestErrors = $errors->getBag('interest')->any();
@endphp

@section('content')
    <article data-track-view="project_view" data-track-reference="{{ $project->reference }}">
        {{-- Header --}}
        <header class="on-dark grain relative overflow-hidden bg-ink text-paper">
            @if ($project->coverUrl())
                <x-picture :src="$project->cover_image" eager class="absolute inset-0" img-class="size-full object-cover opacity-45" />
                <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/85 to-ink/30"></div>
            @else
                <x-rays class="absolute -right-[30rem] -bottom-[36rem] size-[66rem] opacity-35" :count="24" />
            @endif
            <div class="container-site relative pt-10 pb-14 md:pt-14 md:pb-20">
                <x-breadcrumbs />
                <div class="mt-10 max-w-4xl md:mt-14">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-mono text-sm tracking-wider text-brand-gold">{{ $project->reference }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold tracking-wide {{ $project->status->value === 'open' ? 'bg-brand-green text-paper' : 'bg-brand-gold text-ink' }}">{{ $project->status->getLabel() }}</span>
                    </div>
                    <h1 class="text-h1 mt-5">{{ $project->tr('title') }}</h1>
                    <p class="mt-5 flex items-center gap-2 text-lg text-ink-300"><x-glyph name="map-pin" :size="20" class="text-brand-gold" />{{ $project->countryName() }} · {{ $project->sector?->tr('name') }}</p>
                </div>
            </div>
        </header>

        <div class="container-site grid gap-12 py-14 lg:grid-cols-12 lg:gap-16 lg:py-20">
            {{-- Main content --}}
            <div class="space-y-14 lg:col-span-7">
                <section aria-labelledby="summary-title">
                    <h2 id="summary-title" class="eyebrow"><x-tricolor class="!w-8" />{{ __('projects.sheet.summary') }}</h2>
                    <p class="mt-5 font-display text-xl leading-snug font-bold md:text-2xl">{{ $project->tr('summary') }}</p>
                </section>

                <section aria-labelledby="description-title">
                    <h2 id="description-title" class="text-h3">{{ __('projects.sheet.description') }}</h2>
                    @if ($project->description_on_request)
                        <x-alert class="mt-5"><span class="flex items-center gap-2"><x-glyph name="lock" :size="18" />{{ __('projects.sheet.on_request') }}</span></x-alert>
                    @else
                        <div class="prose-site mt-5">{!! \App\Support\Html::clean($project->tr('description')) !!}</div>
                    @endif
                </section>

                @if ($useOfFunds)
                    <section aria-labelledby="funds-title">
                        <h2 id="funds-title" class="text-h3">{{ __('projects.sheet.use_of_funds') }}</h2>
                        <ul class="mt-5 divide-y divide-ink-200 border-y border-ink-200">
                            @foreach ((array) $useOfFunds as $line)
                                <li class="flex gap-3 py-4"><x-glyph name="check" :size="20" class="mt-0.5 text-brand-green" />{{ $line }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <div class="grid gap-8 md:grid-cols-2">
                    @if ($project->tr('impact'))
                        <section class="bg-ink-50 p-7" aria-labelledby="impact-title">
                            <h2 id="impact-title" class="flex items-center gap-2 font-display text-lg font-bold"><x-glyph name="leaf" :size="20" class="text-brand-green" />{{ __('projects.sheet.impact') }}</h2>
                            <p class="mt-3 text-base text-ink-600">{{ $project->tr('impact') }}</p>
                        </section>
                    @endif
                    @if ($project->tr('timeline'))
                        <section class="bg-ink-50 p-7" aria-labelledby="timeline-title">
                            <h2 id="timeline-title" class="flex items-center gap-2 font-display text-lg font-bold"><x-glyph name="calendar" :size="20" class="text-brand-green" />{{ __('projects.sheet.timeline') }}</h2>
                            <p class="mt-3 text-base text-ink-600">{{ $project->tr('timeline') }}</p>
                        </section>
                    @endif
                </div>

                @if ($gallery = $project->galleryUrls())
                    <section aria-labelledby="gallery-title">
                        <h2 id="gallery-title" class="text-h3">{{ __('projects.sheet.gallery') }}</h2>
                        <ul class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-3">
                            @foreach ($gallery as $image)
                                <li class="aspect-[4/3] overflow-hidden bg-ink-100"><x-picture :src="$image" :alt="$project->tr('title')" sizes="(min-width: 768px) 20vw, 50vw" class="block size-full" img-class="size-full object-cover" /></li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section aria-labelledby="documents-title">
                    <h2 id="documents-title" class="text-h3">{{ __('projects.sheet.documents') }}</h2>
                    <ul class="mt-5 space-y-3">
                        @if ($project->public_document)
                            <li>
                                <a href="{{ asset('storage/'.$project->public_document) }}" download class="card card-hover flex items-center gap-4 p-5" data-track="document_download" data-track-document="{{ $project->reference }}">
                                    <x-glyph name="file" :size="24" class="text-brand-green-deep" />
                                    <span class="flex-1 font-semibold">{{ __('projects.sheet.public_summary') }}</span>
                                    <x-glyph name="download" :size="20" />
                                </a>
                            </li>
                        @endif
                        <li class="flex items-center gap-4 bg-ink-50 p-5 text-ink-600">
                            <x-glyph name="lock" :size="22" />
                            <span>{{ __('projects.sheet.full_file') }}</span>
                        </li>
                    </ul>
                </section>

                <p class="border-t border-ink-200 pt-6 text-sm text-ink-500">{{ __('projects.disclaimer') }}</p>
            </div>

            {{-- Sidebar: investment need + expression of interest --}}
            <aside class="lg:col-span-5">
                <div class="lg:sticky lg:top-28">
                    <section class="on-dark bg-ink p-8 text-paper" aria-labelledby="need-title">
                        <h2 id="need-title" class="text-label !text-xs text-ink-400">{{ __('projects.sheet.need') }}</h2>
                        <p class="mt-2 font-display text-4xl font-extrabold text-brand-gold">{{ $project->amountLabel() }}</p>
                        <dl class="mt-8 divide-y divide-ink-700 border-t border-ink-700 text-[0.9375rem]">
                            @foreach ($facts as $label => $value)
                                <div class="flex justify-between gap-6 py-3.5">
                                    <dt class="text-ink-400">{{ $label }}</dt>
                                    <dd class="text-right font-semibold">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    {{-- Expression of interest — linked automatically to the project reference (§6.2) --}}
                    <section id="interest" class="mt-6 p-8 shadow-[inset_0_0_0_1px_var(--color-ink-200)]" aria-labelledby="interest-title">
                        @if ($isOpen)
                            <h2 id="interest-title" class="text-h3">{{ __('projects.interest.title') }}</h2>
                            <p class="mt-2 text-base text-ink-500">{{ __('projects.interest.lead', ['reference' => $project->reference]) }}</p>

                            <form method="POST" action="{{ lroute('interest.store', ['project' => $project->slugKey()]) }}" class="relative mt-8 space-y-5" novalidate
                                  x-data="validatedForm(@js(__('forms.js')))" x-on:submit="submit($event)">
                                @csrf
                                <x-form.antispam bag="interest" />
                                @if ($hasInterestErrors)
                                    <x-alert type="error">{{ __('forms.errors_title') }}</x-alert>
                                @endif
                                <x-form.input bag="interest" name="full_name" :label="__('forms.labels.full_name')" autocomplete="name" required />
                                <x-form.input bag="interest" name="organization" :label="__('forms.labels.organization')" autocomplete="organization" />
                                <x-form.select bag="interest" name="investor_type" :label="__('forms.labels.investor_type')" :options="$investorTypes" required />
                                <x-form.input bag="interest" name="email" type="email" :label="__('forms.labels.email')" autocomplete="email" inputmode="email" required />
                                <x-form.input bag="interest" name="phone" type="tel" :label="__('forms.labels.phone')" autocomplete="tel" inputmode="tel" :help="__('forms.help.phone')" required />
                                <x-form.select bag="interest" name="country" :label="__('forms.labels.country')" :options="$countries" autocomplete="country" required />
                                <x-form.input bag="interest" name="amount" :label="__('forms.labels.amount').' ('.$project->currency.')'" inputmode="numeric" :help="__('forms.help.amount')" />
                                <x-form.textarea bag="interest" name="message" :label="__('forms.labels.message')" rows="4" :maxlength="3000" />
                                <x-form.checkbox bag="interest" name="consent" required>
                                    {!! __('forms.consent', ['policy' => '<a href="'.lroute('privacy').'" class="link" target="_blank">'.__('forms.consent_policy').'</a>']) !!}
                                </x-form.checkbox>
                                <button type="submit" class="btn btn-invest w-full" :disabled="submitting">
                                    <span x-show="!submitting">{{ __('projects.interest.submit') }}</span>
                                    <span x-show="submitting" x-cloak>{{ __('forms.sending') }}</span>
                                </button>
                            </form>
                        @else
                            <h2 id="interest-title" class="text-h3">{{ __('projects.interest.title') }}</h2>
                            <p class="mt-3 text-ink-500">{{ __('projects.sheet.closed') }}</p>
                            <a href="{{ lroute('invest') }}" class="btn btn-invest mt-6 w-full">{{ __('site.cta.view_all_projects') }}</a>
                        @endif
                    </section>
                </div>
            </aside>
        </div>

        @if ($related->isNotEmpty())
            <section class="section-y bg-ink-50" aria-labelledby="related-title">
                <div class="container-site">
                    <h2 id="related-title" class="text-h2">{{ __('projects.sheet.related') }}</h2>
                    <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($related as $item)
                            <x-project-card :project="$item" class="reveal" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </article>

    {{-- Mobile contextual action bar (§12.2) --}}
    @if ($isOpen)
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-ink-200 bg-paper p-3 lg:hidden">
            <a href="#interest" class="btn btn-invest w-full" data-track="cta_interest_mobile" data-track-reference="{{ $project->reference }}">{{ __('projects.interest.mobile_cta') }}</a>
        </div>
        <div class="h-20 lg:hidden" aria-hidden="true"></div>
    @endif

    @if ($hasInterestErrors)
        @push('scripts')
            <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">document.getElementById('interest')?.scrollIntoView();</script>
        @endpush
    @endif
@endsection
