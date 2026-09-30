@extends('layouts.site')

@php($hasMap = is_numeric($contact['lat'] ?? null) && is_numeric($contact['lng'] ?? null))

@if ($hasMap)
    @push('scripts')
        @vite('resources/js/map.js')
    @endpush
@endif

@section('content')
    <x-page-hero :eyebrow="__('contact.hero.eyebrow')" :title="__('contact.hero.title')" :lead="__('contact.hero.lead')" />

    <section class="section-y">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            {{-- Form: routed to the relevant team according to the subject (§5.6) --}}
            <div class="lg:col-span-7">
                <h2 class="text-h2">{{ __('contact.form.title') }}</h2>
                <p class="mt-3 text-sm text-ink-500">{{ __('forms.required_note') }}</p>

                <form method="POST" action="{{ lroute('contact.store') }}" class="relative mt-10 space-y-6" novalidate
                      x-data="validatedForm(@js(__('forms.js')))" x-on:submit="submit($event)">
                    @csrf
                    <x-form.antispam />
                    @if ($errors->any())
                        <x-alert type="error">{{ __('forms.errors_title') }}</x-alert>
                    @endif

                    <x-form.select name="subject" :label="__('forms.labels.subject')" :options="$subjects" :value="$selectedSubject" required />
                    <div class="grid gap-6 md:grid-cols-2">
                        <x-form.input name="full_name" :label="__('forms.labels.full_name')" autocomplete="name" required />
                        <x-form.input name="organization" :label="__('forms.labels.organization')" autocomplete="organization" />
                        <x-form.input name="email" type="email" :label="__('forms.labels.email')" autocomplete="email" inputmode="email" required />
                        <x-form.input name="phone" type="tel" :label="__('forms.labels.phone')" autocomplete="tel" inputmode="tel" :help="__('forms.help.phone')" />
                    </div>
                    <x-form.select name="country" :label="__('forms.labels.country')" :options="$countries" autocomplete="country" />
                    <x-form.textarea name="message" :label="__('forms.labels.message')" minlength="10" :maxlength="5000" required />
                    <x-form.checkbox name="consent" required>
                        {!! __('forms.consent', ['policy' => '<a href="'.lroute('privacy').'" class="link" target="_blank">'.__('forms.consent_policy').'</a>']) !!}
                    </x-form.checkbox>

                    <button type="submit" class="btn btn-dark w-full sm:w-auto" :disabled="submitting">
                        <span x-show="!submitting">{{ __('contact.form.submit') }}</span>
                        <span x-show="submitting" x-cloak>{{ __('forms.sending') }}</span>
                        <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
                    </button>
                </form>
            </div>

            {{-- Official details (to be validated by the institution) --}}
            <aside class="lg:col-span-4 lg:col-start-9">
                <div class="on-dark bg-ink p-8 text-paper md:p-10">
                    <x-tricolor />
                    <h2 class="mt-6 font-display text-2xl font-extrabold">{{ __('contact.details.title') }}</h2>
                    @if (empty($contact['email']) && empty($contact['phone']) && empty($contact['address']))
                        <p class="mt-5 text-ink-300">{{ __('contact.details.pending') }}</p>
                    @endif
                    <dl class="mt-6 space-y-6">
                        @if (! empty($contact['email']))
                            <div>
                                <dt class="text-label !text-xs text-ink-400">{{ __('contact.details.email') }}</dt>
                                <dd class="mt-1"><a href="mailto:{{ $contact['email'] }}" class="link text-lg">{{ $contact['email'] }}</a></dd>
                            </div>
                        @endif
                        @if (! empty($contact['phone']))
                            <div>
                                <dt class="text-label !text-xs text-ink-400">{{ __('contact.details.phone') }}</dt>
                                <dd class="mt-1"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['phone']) }}" class="link text-lg">{{ $contact['phone'] }}</a></dd>
                            </div>
                        @endif
                        @if (! empty($contact['address']))
                            <div>
                                <dt class="text-label !text-xs text-ink-400">{{ __('contact.details.address') }}</dt>
                                <dd class="mt-1 whitespace-pre-line text-ink-200">{{ $contact['address'] }}</dd>
                            </div>
                        @endif
                        @if (! empty($contact['hours']))
                            <div>
                                <dt class="text-label !text-xs text-ink-400">{{ __('contact.details.hours') }}</dt>
                                <dd class="mt-1 whitespace-pre-line text-ink-200">{{ $contact['hours'] }}</dd>
                            </div>
                        @endif
                    </dl>
                    @if ($socials)
                        <p class="text-label mt-8 !text-xs text-ink-400">{{ __('contact.details.socials') }}</p>
                        <ul class="mt-3 flex flex-wrap gap-2">
                            @foreach ($socials as $network => $url)
                                <li><a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}" class="inline-flex size-11 items-center justify-center shadow-[inset_0_0_0_1px_var(--color-ink-600)] hover:bg-brand-gold hover:text-ink"><x-glyph :name="$network" :size="18" /></a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @if ($hasMap)
                    <div class="mt-6 aspect-square w-full bg-ink-100" data-osm-map data-lat="{{ $contact['lat'] }}" data-lng="{{ $contact['lng'] }}" data-label="{{ config('site.name') }}" role="region" aria-label="{{ __('contact.map') }}"></div>
                @endif
            </aside>
        </div>
    </section>
@endsection
