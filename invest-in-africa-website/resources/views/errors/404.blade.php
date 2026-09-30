@extends('layouts.site')

@section('content')
    {{-- Trilingual, carefully designed 404 with orientation towards key sections (§3.4) --}}
    <section class="on-dark grain relative overflow-hidden bg-ink text-paper">
        <x-rays class="absolute -right-[26rem] -bottom-[32rem] size-[64rem] opacity-60" :count="26" />
        <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/85 to-ink/20"></div>
        <div class="container-site relative py-24 md:py-32">
            <p class="font-display text-[7rem] leading-none font-extrabold text-brand-gold md:text-[10rem]">404</p>
            <h1 class="text-h1 mt-6">{{ __('errors.404.title') }}</h1>
            <p class="text-lead mt-5 max-w-2xl text-ink-300">{{ __('errors.404.text') }}</p>
            <ul class="mt-10 flex flex-wrap gap-3">
                @foreach (['home', 'about', 'what-we-do', 'get-involved', 'contact'] as $page)
                    <li><a href="{{ lroute($page) }}" class="btn btn-outline btn-sm">{{ __('site.nav.'.str_replace('-', '_', $page)) }}</a></li>
                @endforeach
            </ul>
            <div class="mt-8"><x-cta-buttons location="404" /></div>
        </div>
    </section>
@endsection
