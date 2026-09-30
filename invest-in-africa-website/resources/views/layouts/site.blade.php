@php
    $alternates = $seo->resolvedAlternates();
    $ogImage = $seo->image ?: asset('images/og-default.png');
    $suggested = \App\Support\Locales::fromBrowser(request()->header('Accept-Language'));
    $suggestLocale = request()->cookie(\App\Http\Middleware\SetLocale::COOKIE) === null && $suggested && $suggested !== $currentLocale ? $suggested : null;
    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => config('site.name'),
        'url' => lroute('home'),
        'logo' => asset('images/logo-invest-in-africa.png'),
        'email' => data_get($settings, 'contact.email'),
        'telephone' => data_get($settings, 'contact.phone'),
        'sameAs' => array_values(array_filter(data_get($settings, 'socials', []) ?: [])),
    ]);
@endphp
<!DOCTYPE html>
<html lang="{{ \App\Support\Locales::hreflang() }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $seo->fullTitle() }}</title>
    @if ($seo->description)
        <meta name="description" content="{{ $seo->description }}">
    @endif
    @if ($seo->noindex || ! app()->isProduction())
        <meta name="robots" content="noindex, nofollow">
    @endif

    {{-- Self-referencing canonical + reciprocal hreflang (§7.4, §10.1) --}}
    <link rel="canonical" href="{{ $seo->canonical() }}">
    @foreach ($alternates as $locale => $url)
        <link rel="alternate" hreflang="{{ \App\Support\Locales::hreflang($locale) }}" href="{{ $url }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $alternates[config('site.default_locale')] ?? lroute('home', [], config('site.default_locale')) }}">

    {{-- Open Graph per page and per language --}}
    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo->title ?? config('site.name') }}">
    @if ($seo->description)<meta property="og:description" content="{{ $seo->description }}">@endif
    <meta property="og:url" content="{{ $seo->canonical() }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="{{ \App\Support\Locales::og() }}">
    @foreach (\App\Support\Locales::codes() as $locale)
        @if ($locale !== $currentLocale)<meta property="og:locale:alternate" content="{{ \App\Support\Locales::og($locale) }}">@endif
    @endforeach
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <meta name="theme-color" content="#000000">
    <meta name="format-detection" content="telephone=no">
    @if (config('site.analytics.ga4_id'))
        <meta name="ga4-id" content="{{ config('site.analytics.ga4_id') }}">
    @endif

    @vite(array_filter(['resources/css/app.css', $currentLocale === 'zh' ? 'resources/css/zh.css' : null, 'resources/js/app.js']))
    @stack('head')

    <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">@json($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
    @if ($breadcrumbs = $seo->breadcrumbSchema())
        <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">@json($breadcrumbs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
    @endif
    @foreach ($seo->schemas as $schema)
        <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">@json($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
    @endforeach
</head>
<body class="flex min-h-dvh flex-col">
    <a href="#main" class="sr-only z-[100] bg-brand-gold px-5 py-3 font-bold text-ink focus:not-sr-only focus:fixed focus:top-3 focus:left-3">
        {{ __('site.a11y.skip') }}
    </a>

    @if ($suggestLocale)
        <div x-data="{ show: true }" x-show="show" x-collapse class="bg-ink text-paper" lang="{{ \App\Support\Locales::hreflang($suggestLocale) }}">
            <div class="container-site flex items-center justify-between gap-4 py-2.5 text-sm">
                <p class="flex items-center gap-2">
                    <x-glyph name="globe" :size="16" class="text-brand-gold" />
                    {{ __('site.language.suggestion', [], $suggestLocale) }}
                    <a href="{{ $alternates[$suggestLocale] ?? lroute('home', [], $suggestLocale) }}" hreflang="{{ \App\Support\Locales::hreflang($suggestLocale) }}"
                       class="font-bold text-brand-gold underline underline-offset-4" data-track="language_switch" data-track-to="{{ $suggestLocale }}">
                        {{ \App\Support\Locales::label($suggestLocale) }}
                    </a>
                </p>
                <button type="button" x-on:click="show = false" class="-m-2 p-2 text-ink-300 hover:text-paper" aria-label="{{ __('site.a11y.close') }}">
                    <x-glyph name="close" :size="18" />
                </button>
            </div>
        </div>
    @endif

    @include('partials.header', ['alternates' => $alternates])

    <main id="main" class="flex-1" tabindex="-1">
        @if (session('notice'))
            <div class="container-site pt-6">
                <x-alert type="info">{{ session('notice') }}</x-alert>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer', ['alternates' => $alternates])
    @include('partials.cookie-banner')
    @stack('scripts')
</body>
</html>
