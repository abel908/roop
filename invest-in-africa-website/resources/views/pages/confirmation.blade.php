@extends('layouts.site')

@php($type = $confirmation['type'])

@section('content')
    <section class="on-dark grain relative overflow-hidden bg-ink text-paper">
        <x-rays class="absolute -right-[30rem] -bottom-[36rem] size-[66rem] opacity-40" :count="26" />
        <div class="container-site relative py-20 md:py-28">
            <div class="max-w-3xl">
                <span class="flex size-16 items-center justify-center bg-brand-green text-paper"><x-glyph name="check" :size="34" /></span>
                <h1 class="text-h1 mt-10">{{ __("confirmation.$type.title") }}</h1>
                <p class="text-lead mt-6 text-ink-300">{{ __("confirmation.$type.text", ['project' => $confirmation['project'] ?? '']) }}</p>

                <div class="mt-10 inline-flex flex-col border-l-4 border-brand-gold bg-ink-800 px-8 py-6">
                    <span class="text-label !text-xs text-ink-400">{{ __('confirmation.reference') }}</span>
                    <span class="mt-2 font-mono text-3xl font-bold tracking-wider text-brand-gold md:text-4xl" data-track-view="{{ ['submission' => 'project_submit', 'interest' => 'interest_submit', 'contact' => 'contact_submit'][$type] }}">{{ $confirmation['reference'] }}</span>
                </div>
                <p class="mt-6 text-ink-300">{{ __('confirmation.email_sent', ['email' => $confirmation['email']]) }} {{ __('confirmation.keep') }}</p>
            </div>
        </div>
    </section>

    <section class="section-y">
        <div class="container-site">
            <h2 class="text-h2">{{ __('confirmation.next_title') }}</h2>
            <ol class="mt-10 grid gap-8 md:grid-cols-3">
                @foreach (__("confirmation.$type.next") as $step)
                    <li class="flex gap-5">
                        <span class="flex size-12 shrink-0 items-center justify-center bg-ink font-display font-extrabold text-brand-gold">{{ $loop->iteration }}</span>
                        <p class="pt-2.5 font-semibold">{{ $step }}</p>
                    </li>
                @endforeach
            </ol>
            <a href="{{ lroute('home') }}" class="btn btn-outline mt-14"><x-glyph name="arrow-left" :size="18" /> {{ __('confirmation.back_home') }}</a>
        </div>
    </section>
@endsection
