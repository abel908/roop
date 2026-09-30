@extends('layouts.site')

@php
    $errorFields = array_keys($errors->getMessages());
    $stepOf = function (string $field) {
        $field = explode('.', $field)[0];
        return match (true) {
            in_array($field, ['full_name', 'organization', 'email', 'phone', 'country', 'position'], true) => 1,
            in_array($field, ['project_name', 'project_country', 'sector_id', 'description', 'stage', 'investment_amount', 'currency', 'funding_type', 'timeline'], true) => 2,
            default => 3,
        };
    };
    $initialStep = $errorFields ? min(array_map($stepOf, $errorFields)) : 1;
    $js = __('forms.js');
    $config = [
        'initialStep' => $initialStep,
        'maxFiles' => $uploads['max_files'],
        'maxKb' => $uploads['max_kb'],
        'extensions' => $uploads['extensions'],
        'descriptionMax' => $uploads['description_max'],
        'messages' => [
            'required' => $js['required'], 'email' => $js['email'], 'phone' => $js['phone'], 'min' => $js['min'],
            'accepted' => $js['accepted'], 'fileType' => $js['file_type'], 'fileSize' => $js['file_size'], 'fileCount' => $js['file_count'],
        ],
    ];
    $steps = __('submit.steps');
    $maxMb = round($uploads['max_kb'] / 1024);
@endphp

@section('content')
    <x-page-hero :eyebrow="__('submit.hero.eyebrow')" :title="__('submit.hero.title')" :lead="__('submit.hero.lead')" />

    <section class="section-y !pt-12 lg:!pt-16" x-data="submissionForm(@js($config))">
        <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-8" x-ref="top">
                {{-- Progress bar --}}
                <div class="mb-10">
                    <div class="flex items-center justify-between text-sm font-semibold">
                        <p aria-live="polite">
                            <span x-text="@js(__('submit.progress', ['current' => '__C__', 'total' => 3])).replace('__C__', step)">{{ __('submit.progress', ['current' => $initialStep, 'total' => 3]) }}</span>
                        </p>
                        <p class="text-ink-500" x-text="progress + '%'"></p>
                    </div>
                    <div class="mt-3 h-1.5 bg-ink-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="progress">
                        <div class="h-full bg-ink transition-[width] duration-500 ease-out" :style="`width: ${progress}%`"></div>
                    </div>
                    <ol class="mt-6 grid grid-cols-3 gap-3">
                        @foreach ($steps as $number => $item)
                            <li>
                                <button type="button" class="flex w-full items-start gap-3 text-left disabled:cursor-default"
                                        x-on:click="if ({{ $number }} < step) step = {{ $number }}" :disabled="{{ $number }} >= step"
                                        :aria-current="step === {{ $number }} ? 'step' : null">
                                    <span class="flex size-8 shrink-0 items-center justify-center font-display text-sm font-extrabold transition-colors"
                                          :class="step > {{ $number }} ? 'bg-brand-green text-paper' : (step === {{ $number }} ? 'bg-ink text-brand-gold' : 'bg-ink-100 text-ink-500')">
                                        <span x-show="step <= {{ $number }}">{{ $number }}</span>
                                        <x-glyph name="check" :size="16" x-show="step > {{ $number }}" x-cloak />
                                    </span>
                                    <span class="hidden pt-1 text-sm font-semibold sm:block" :class="step === {{ $number }} ? 'text-ink' : 'text-ink-500'">{{ $item['title'] }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <form method="POST" action="{{ lroute('submit.store') }}" enctype="multipart/form-data" novalidate class="relative" x-on:submit="submit($event)">
                    @csrf
                    <x-form.antispam />

                    @if ($errors->any())
                        <x-alert type="error" class="mb-8">
                            {{ __('forms.errors_title') }}
                            @if (old('full_name'))<span class="mt-1 block text-sm">{{ __('submit.reupload') }}</span>@endif
                        </x-alert>
                    @endif

                    {{-- STEP 1 — Project holder --}}
                    <fieldset data-step="1" x-show="step === 1" @if($initialStep !== 1) x-cloak @endif class="space-y-6">
                        <legend class="mb-8" tabindex="-1" x-ref="stepHeading">
                            <span class="text-h3 block font-display font-extrabold">1. {{ $steps[1]['title'] }}</span>
                            <span class="mt-1 block text-base text-ink-500">{{ $steps[1]['text'] }} {{ __('forms.required_note') }}</span>
                        </legend>
                        <div class="grid gap-6 md:grid-cols-2">
                            <x-form.input name="full_name" :label="__('forms.labels.full_name')" autocomplete="name" required />
                            <x-form.input name="organization" :label="__('forms.labels.organization')" autocomplete="organization" required />
                            <x-form.input name="email" type="email" :label="__('forms.labels.email')" autocomplete="email" inputmode="email" required />
                            <x-form.input name="phone" type="tel" :label="__('forms.labels.phone')" autocomplete="tel" inputmode="tel" :help="__('forms.help.phone')" required />
                            <x-form.select name="country" :label="__('forms.labels.country')" :options="$countries" autocomplete="country" required />
                            <x-form.input name="position" :label="__('forms.labels.position')" autocomplete="organization-title" required />
                        </div>
                    </fieldset>

                    {{-- STEP 2 — Project --}}
                    <fieldset data-step="2" x-show="step === 2" @if($initialStep !== 2) x-cloak @endif class="space-y-6">
                        <legend class="mb-8" tabindex="-1">
                            <span class="text-h3 block font-display font-extrabold">2. {{ $steps[2]['title'] }}</span>
                            <span class="mt-1 block text-base text-ink-500">{{ $steps[2]['text'] }}</span>
                        </legend>
                        <x-form.input name="project_name" :label="__('submit.fields.project_name')" required />
                        <div class="grid gap-6 md:grid-cols-2">
                            <x-form.select name="project_country" :label="__('submit.fields.project_country')" :options="$africanCountries" required>
                            </x-form.select>
                            <x-form.select name="sector_id" :label="__('submit.fields.sector')" :options="$sectors" required />
                        </div>
                        <x-form.textarea name="description" :label="__('submit.fields.description')" :help="__('submit.fields.description_help')" minlength="50" :maxlength="$uploads['description_max']" rows="8" required />
                        <div class="grid gap-6 md:grid-cols-2">
                            <x-form.select name="stage" :label="__('submit.fields.stage')" :options="$stages" required />
                            <x-form.select name="funding_type" :label="__('submit.fields.funding')" :options="$fundingTypes" required />
                        </div>
                        <div class="grid gap-6 md:grid-cols-[1fr_12rem]">
                            <x-form.input name="investment_amount" :label="__('submit.fields.investment')" inputmode="numeric" placeholder="1 500 000" required />
                            <x-form.select name="currency" :label="__('submit.fields.currency')" :options="array_combine($currencies, $currencies)" value="USD" required />
                        </div>
                        <x-form.input name="timeline" :label="__('submit.fields.timeline')" :help="__('submit.fields.timeline_help')" required />
                    </fieldset>

                    {{-- STEP 3 — Documents, validation, summary --}}
                    <fieldset data-step="3" x-show="step === 3" @if($initialStep !== 3) x-cloak @endif class="space-y-8">
                        <legend class="mb-8" tabindex="-1">
                            <span class="text-h3 block font-display font-extrabold">3. {{ $steps[3]['title'] }}</span>
                            <span class="mt-1 block text-base text-ink-500">{{ $steps[3]['text'] }}</span>
                        </legend>

                        {{-- Secure upload (§6.3) --}}
                        <div>
                            <p class="field-label" id="documents-label">{{ __('submit.fields.documents') }}</p>
                            <p class="field-help !mt-0 mb-4" id="documents-help">{{ __('submit.fields.documents_help', ['count' => $uploads['max_files'], 'size' => $maxMb]) }}</p>
                            <label for="documents" class="flex cursor-pointer flex-col items-center justify-center gap-3 border-2 border-dashed border-ink-300 bg-ink-50 px-6 py-10 text-center transition hover:border-brand-green hover:bg-paper"
                                   x-on:dragover.prevent="$el.classList.add('!border-brand-green')" x-on:dragleave="$el.classList.remove('!border-brand-green')"
                                   x-on:drop.prevent="$el.classList.remove('!border-brand-green'); addFiles({ target: { files: $event.dataTransfer.files } })">
                                <x-glyph name="upload" :size="32" class="text-brand-green" />
                                <span class="btn btn-dark btn-sm pointer-events-none">{{ __('submit.fields.drop') }}</span>
                                <span class="text-sm text-ink-500">{{ __('submit.fields.drop_text') }}</span>
                            </label>
                            <input id="documents" x-ref="fileInput" type="file" name="documents[]" multiple class="sr-only"
                                   accept="{{ collect($uploads['extensions'])->map(fn ($e) => '.'.$e)->implode(',') }}"
                                   aria-labelledby="documents-label" aria-describedby="documents-help"
                                   x-on:change="addFiles($event)">
                            <ul class="mt-4 space-y-2" x-show="files.length">
                                <template x-for="(file, index) in files" :key="file.name + file.size">
                                    <li class="flex items-center gap-3 bg-paper p-3 shadow-[inset_0_0_0_1px_var(--color-ink-200)]">
                                        <x-glyph name="file" :size="20" class="text-brand-green-deep" />
                                        <span class="min-w-0 flex-1 truncate text-[0.9375rem] font-semibold" x-text="file.name"></span>
                                        <span class="text-sm text-ink-500" x-text="size(file.size)"></span>
                                        <button type="button" class="inline-flex size-10 items-center justify-center text-ink-500 hover:text-brand-red" x-on:click="removeFile(index)" :aria-label="'{{ __('submit.fields.remove') }} ' + file.name">
                                            <x-glyph name="trash" :size="18" />
                                        </button>
                                    </li>
                                </template>
                            </ul>
                            <template x-for="error in fileErrors">
                                <p class="field-error" role="alert"><x-glyph name="alert" :size="16" class="mt-0.5" /><span x-text="error"></span></p>
                            </template>
                            @foreach ($errors->get('documents*') as $messages)
                                @foreach ($messages as $message)
                                    <p class="field-error"><x-glyph name="alert" :size="16" class="mt-0.5" />{{ $message }}</p>
                                @endforeach
                            @endforeach
                        </div>

                        {{-- Summary before sending --}}
                        <div class="bg-ink-50 p-6 md:p-8">
                            <h3 class="font-display text-lg font-bold">{{ __('submit.summary.title') }}</h3>
                            <div class="mt-6 grid gap-8 md:grid-cols-2">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <p class="text-label !text-xs text-ink-500">{{ __('submit.summary.holder') }}</p>
                                        <button type="button" class="link text-sm" x-on:click="step = 1">{{ __('submit.summary.edit') }}</button>
                                    </div>
                                    <dl class="mt-3 space-y-2 text-[0.9375rem]">
                                        @foreach (['full_name', 'organization', 'email', 'phone', 'country', 'position'] as $field)
                                            <div class="grid grid-cols-[8rem_1fr] gap-3"><dt class="text-ink-500">{{ __('forms.labels.'.$field) }}</dt><dd class="font-semibold break-words" x-text="label('{{ $field }}')"></dd></div>
                                        @endforeach
                                    </dl>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between">
                                        <p class="text-label !text-xs text-ink-500">{{ __('submit.summary.project') }}</p>
                                        <button type="button" class="link text-sm" x-on:click="step = 2">{{ __('submit.summary.edit') }}</button>
                                    </div>
                                    <dl class="mt-3 space-y-2 text-[0.9375rem]">
                                        @foreach (['project_name' => 'project_name', 'project_country' => 'project_country', 'sector_id' => 'sector', 'stage' => 'stage', 'funding_type' => 'funding', 'timeline' => 'timeline'] as $field => $labelKey)
                                            <div class="grid grid-cols-[8rem_1fr] gap-3"><dt class="text-ink-500">{{ __('submit.fields.'.$labelKey) }}</dt><dd class="font-semibold break-words" x-text="label('{{ $field }}')"></dd></div>
                                        @endforeach
                                        <div class="grid grid-cols-[8rem_1fr] gap-3"><dt class="text-ink-500">{{ __('submit.fields.investment') }}</dt><dd class="font-semibold" x-text="(data.investment_amount || '—') + ' ' + (data.currency || '')"></dd></div>
                                        <div class="grid grid-cols-[8rem_1fr] gap-3"><dt class="text-ink-500">{{ __('submit.summary.documents') }}</dt><dd class="font-semibold" x-text="files.length ? files.map(f => f.name).join(', ') : @js(__('submit.summary.no_documents'))"></dd></div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <x-form.checkbox name="consent" required>
                                {!! __('submit.fields.consent', ['policy' => '<a href="'.lroute('privacy').'" class="link" target="_blank">'.__('forms.consent_policy').'</a>']) !!}
                            </x-form.checkbox>
                            <x-form.checkbox name="certify" required>{{ __('submit.fields.certify') }}</x-form.checkbox>
                        </div>
                    </fieldset>

                    {{-- Navigation --}}
                    <div class="mt-12 flex flex-col-reverse gap-3 border-t border-ink-200 pt-8 sm:flex-row sm:items-center sm:justify-between">
                        <button type="button" class="btn btn-outline" x-show="step > 1" x-cloak x-on:click="previous()">
                            <x-glyph name="arrow-left" :size="18" /> {{ __('submit.actions.previous') }}
                        </button>
                        <span x-show="step === 1"></span>
                        <button type="button" class="btn btn-dark" x-show="step < 3" x-on:click="next()">
                            {{ __('submit.actions.next') }} <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
                        </button>
                        <button type="submit" class="btn btn-submit" x-show="step === 3" x-cloak :disabled="submitting">
                            <span x-show="!submitting">{{ __('submit.actions.submit') }}</span>
                            <span x-show="submitting" x-cloak>{{ __('forms.sending') }}</span>
                            <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
                        </button>
                    </div>
                </form>
            </div>

            <aside class="lg:col-span-4">
                <div class="on-dark bg-ink p-8 text-paper lg:sticky lg:top-28">
                    <x-tricolor />
                    <h2 class="mt-6 font-display text-xl font-extrabold text-brand-gold">{{ __('submit.aside.title') }}</h2>
                    <ul class="mt-6 space-y-4 text-[0.9375rem] text-ink-200">
                        @foreach (__('submit.aside.items') as $item)
                            <li class="flex gap-3"><x-glyph name="check" :size="20" class="mt-0.5 shrink-0 text-brand-gold" />{{ $item }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ lroute('contact', ['subject' => 'project']) }}" class="link-arrow mt-8">{{ __('submit.aside.help') }} <x-glyph name="arrow-right" :size="18" /></a>
                </div>
            </aside>
        </div>
    </section>
@endsection
