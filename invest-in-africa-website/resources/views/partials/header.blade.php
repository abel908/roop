@php
    // Main navigation, administrable per language in the back-office (§7.2, §8.1).
    $nav = \App\Models\MenuItem::for('header')->map->toNav()->all() ?: [
        ['url' => lroute('about'), 'label' => __('site.nav.about'), 'mega' => null, 'active' => ['about'], 'new_tab' => false, 'current' => false],
        ['url' => lroute('mission'), 'label' => __('site.nav.mission'), 'mega' => null, 'active' => ['mission'], 'new_tab' => false, 'current' => false],
        ['url' => lroute('what-we-do'), 'label' => __('site.nav.what_we_do'), 'mega' => 'domains', 'active' => ['what-we-do', 'domain'], 'new_tab' => false, 'current' => false],
        ['url' => lroute('get-involved'), 'label' => __('site.nav.get_involved'), 'mega' => 'involved', 'active' => ['get-involved', 'invest', 'project', 'submit'], 'new_tab' => false, 'current' => false],
        ['url' => lroute('partners'), 'label' => __('site.nav.partners'), 'mega' => null, 'active' => ['partners'], 'new_tab' => false, 'current' => false],
        ['url' => lroute('contact'), 'label' => __('site.nav.contact'), 'mega' => null, 'active' => ['contact'], 'new_tab' => false, 'current' => false],
    ];
@endphp
{{-- Fixed header, lighter on scroll, always reachable (§3.2) --}}
<header x-data="siteHeader" x-on:keydown.escape.window="closeAll(); mobileOpen = false"
        x-on:click.outside="closeAll()"
        class="sticky top-0 z-50 bg-paper"
        :class="scrolled ? 'shadow-[0_1px_0_0_var(--color-ink-200)]' : ''">
    <div class="container-site flex items-center justify-between gap-6 transition-[height] duration-300"
         :class="scrolled ? 'h-[4.5rem]' : 'h-20 lg:h-24'">
        <a href="{{ lroute('home') }}" class="-my-2 shrink-0" aria-label="{{ config('site.name') }} — {{ __('site.nav.home') }}">
            <span class="block origin-left transition-transform duration-300" :class="scrolled ? 'scale-[0.84]' : ''">
                <x-logo :width="112" eager />
            </span>
        </a>

        {{-- Desktop / laptop navigation --}}
        <nav class="hidden h-full lg:block" aria-label="{{ __('site.a11y.main_nav') }}">
            <ul class="flex h-full items-stretch gap-5 xl:gap-8">
                @foreach ($nav as $item)
                    @php($isActive = $item['current'] || ($item['active'] && is_page(...$item['active'])))
                    <li class="flex">
                        @if ($item['mega'])
                            <button type="button" class="nav-link gap-1" x-on:click="toggleMega('{{ $item['mega'] }}')"
                                    :aria-expanded="megaOpen === '{{ $item['mega'] }}'" aria-controls="mega-{{ $item['mega'] }}"
                                    @if($isActive) aria-current="page" @endif>
                                {{ $item['label'] }}
                                <x-glyph name="chevron-down" :size="16" class="transition-transform duration-300" x-bind:class="megaOpen === '{{ $item['mega'] }}' && 'rotate-180'" />
                            </button>
                        @else
                            <a href="{{ $item['url'] }}" class="nav-link" @if($isActive) aria-current="page" @endif @if($item['new_tab']) target="_blank" rel="noopener" @endif>{{ $item['label'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex items-center gap-3">
            @include('partials.language-switcher', ['alternates' => $alternates, 'variant' => 'header'])

            <a href="{{ lroute('get-involved') }}" class="btn btn-dark btn-sm hidden md:inline-flex" data-track="cta_get_involved_click">
                {{ __('site.nav.get_involved') }}
            </a>

            <button type="button" class="-mr-2 inline-flex size-12 items-center justify-center lg:hidden"
                    x-on:click="mobileOpen = true" aria-controls="mobile-menu" :aria-expanded="mobileOpen"
                    aria-label="{{ __('site.a11y.open_menu') }}">
                <x-glyph name="menu" :size="26" />
            </button>
        </div>
    </div>

    {{-- Mega-menu What We Do: the six areas with a short description (§3.2) --}}
    <div id="mega-domains" x-cloak x-show="megaOpen === 'domains'"
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute inset-x-0 top-full hidden border-t border-ink-200 bg-paper shadow-[0_30px_60px_-30px_rgba(0,0,0,.35)] lg:block">
        <div class="container-site grid grid-cols-12 gap-10 py-10">
            <div class="col-span-3 border-r border-ink-200 pr-8">
                <x-tricolor />
                <p class="mt-5 font-display text-2xl font-extrabold">{{ __('site.nav.what_we_do') }}</p>
                <p class="mt-3 text-base text-ink-500">{{ __('site.mega.domains_intro') }}</p>
                <a href="{{ lroute('what-we-do') }}" class="link-arrow mt-6">{{ __('site.mega.domains_all') }} <x-glyph name="arrow-right" :size="18" /></a>
            </div>
            <ul class="col-span-9 grid grid-cols-3 gap-x-6 gap-y-2">
                @foreach ($navDomains as $domain)
                    <li>
                        <a href="{{ $domain->url() }}" class="group flex h-full gap-4 p-4 transition-colors hover:bg-ink-50">
                            <x-domain-icon :icon="$domain->icon" :size="36" class="text-ink" />
                            <span>
                                <span class="block font-mono text-xs text-ink-400">{{ $domain->numberLabel() }}</span>
                                <span class="mt-0.5 block font-display text-[0.9375rem] leading-snug font-bold group-hover:text-brand-green-deep">{{ $domain->tr('title') }}</span>
                                <span class="mt-1.5 block text-sm leading-snug text-ink-500">{{ $domain->tr('tagline') }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Get Involved: the two clearly differentiated paths (§3.2) --}}
    <div id="mega-involved" x-cloak x-show="megaOpen === 'involved'"
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute inset-x-0 top-full hidden border-t border-ink-200 bg-paper shadow-[0_30px_60px_-30px_rgba(0,0,0,.35)] lg:block">
        <div class="container-site grid grid-cols-12 gap-6 py-10">
            <div class="col-span-4 pr-6">
                <x-tricolor />
                <p class="mt-5 font-display text-2xl font-extrabold">{{ __('site.nav.get_involved') }}</p>
                <p class="mt-3 text-base text-ink-500">{{ __('site.mega.involved_intro') }}</p>
                <a href="{{ lroute('get-involved') }}" class="link-arrow mt-6">{{ __('site.mega.involved_all') }} <x-glyph name="arrow-right" :size="18" /></a>
            </div>
            <a href="{{ lroute('invest') }}" class="group col-span-4 flex flex-col justify-between bg-brand-green p-8 text-paper transition hover:bg-brand-green-deep"
               data-track="cta_invest_click" data-track-location="menu">
                <span class="text-label opacity-80">A · {{ __('site.mega.for_investors') }}</span>
                <span class="mt-10 block font-display text-2xl font-extrabold">{{ __('site.cta.invest') }}</span>
                <span class="mt-2 flex items-end justify-between gap-4 text-base opacity-90">{{ __('site.mega.invest_text') }} <x-glyph name="arrow-right" :size="22" class="shrink-0 transition group-hover:translate-x-1" /></span>
            </a>
            <a href="{{ lroute('submit') }}" class="group col-span-4 flex flex-col justify-between bg-ink p-8 text-paper transition hover:bg-ink-800"
               data-track="cta_submit_click" data-track-location="menu">
                <span class="text-label text-brand-gold">B · {{ __('site.mega.for_holders') }}</span>
                <span class="mt-10 block font-display text-2xl font-extrabold text-brand-gold">{{ __('site.cta.submit') }}</span>
                <span class="mt-2 flex items-end justify-between gap-4 text-base text-ink-200">{{ __('site.mega.submit_text') }} <x-glyph name="arrow-right" :size="22" class="shrink-0 text-brand-gold transition group-hover:translate-x-1" /></span>
            </a>
        </div>
    </div>

    {{-- Mobile: full-screen menu, accordions; language + main actions visible without scrolling (§3.2) --}}
    <div id="mobile-menu" x-cloak x-show="mobileOpen" x-trap.inert.noscroll="mobileOpen"
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex flex-col overflow-y-auto bg-paper lg:hidden" role="dialog" aria-modal="true" aria-label="{{ __('site.a11y.main_nav') }}">
        <div class="container-site flex h-20 shrink-0 items-center justify-between border-b border-ink-200">
            <x-logo :width="100" />
            <button type="button" x-on:click="mobileOpen = false" class="-mr-2 inline-flex size-12 items-center justify-center" aria-label="{{ __('site.a11y.close_menu') }}">
                <x-glyph name="close" :size="26" />
            </button>
        </div>
        <div class="container-site shrink-0 space-y-4 bg-ink-50 py-5">
            @include('partials.language-switcher', ['alternates' => $alternates, 'variant' => 'inline'])
            <x-cta-buttons location="mobile_menu" class="!flex-col [&>a]:w-full" />
        </div>
        <nav class="container-site flex-1 py-4" aria-label="{{ __('site.a11y.main_nav') }}">
            <ul class="divide-y divide-ink-200">
                <li><a href="{{ lroute('home') }}" class="flex min-h-14 items-center font-display text-lg font-bold">{{ __('site.nav.home') }}</a></li>
                @foreach ($nav as $item)
                    <li x-data="{ open: false }">
                        @if ($item['mega'])
                            <button type="button" x-on:click="open = !open" :aria-expanded="open" class="flex min-h-14 w-full items-center justify-between font-display text-lg font-bold">
                                {{ $item['label'] }}
                                <x-glyph name="chevron-down" :size="22" class="transition-transform" x-bind:class="open && 'rotate-180'" />
                            </button>
                            <ul x-show="open" x-collapse class="pb-4">
                                <li><a href="{{ $item['url'] }}" class="flex min-h-11 items-center font-semibold text-brand-green-deep">{{ $item['mega'] === 'domains' ? __('site.mega.domains_all') : __('site.mega.involved_all') }}</a></li>
                                @if ($item['mega'] === 'domains')
                                    @foreach ($navDomains as $domain)
                                        <li><a href="{{ $domain->url() }}" class="flex min-h-11 items-start gap-3 py-2 text-base"><span class="font-mono text-xs text-ink-400 pt-1">{{ $domain->numberLabel() }}</span>{{ $domain->tr('title') }}</a></li>
                                    @endforeach
                                @else
                                    <li><a href="{{ lroute('invest') }}" class="flex min-h-11 items-center text-base">{{ __('site.cta.invest') }}</a></li>
                                    <li><a href="{{ lroute('submit') }}" class="flex min-h-11 items-center text-base">{{ __('site.cta.submit') }}</a></li>
                                @endif
                            </ul>
                        @else
                            <a href="{{ $item['url'] }}" class="flex min-h-14 items-center font-display text-lg font-bold" @if($item['new_tab']) target="_blank" rel="noopener" @endif>{{ $item['label'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</header>
