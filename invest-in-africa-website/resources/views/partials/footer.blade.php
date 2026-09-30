@php
    $contact = data_get($settings, 'contact', []);
    $socials = array_filter(data_get($settings, 'socials', []) ?: []);
@endphp
{{-- Footer (§3.3): logo, short signature, the two main actions, full navigation,
     official contact details, social networks, language selector, legal links. --}}
<footer class="on-dark grain relative overflow-hidden bg-ink text-paper">
    <x-tricolor class="!h-1.5 !w-full" />
    <div class="container-site relative">
        <div class="grid gap-10 border-b border-ink-700 py-14 lg:grid-cols-12 lg:py-20">
            <div class="lg:col-span-5">
                <a href="{{ lroute('home') }}" class="inline-block" aria-label="{{ config('site.name') }} — {{ __('site.nav.home') }}">
                    <x-logo :width="132" plate />
                </a>
                <p class="mt-8 max-w-md font-display text-xl leading-snug font-bold md:text-2xl">{{ __('site.footer.signature') }}</p>
            </div>
            <div class="flex flex-col justify-end gap-4 lg:col-span-7 lg:items-end">
                <p class="text-ink-300">{{ __('site.footer.cta_text') }}</p>
                <x-cta-buttons location="footer" />
            </div>
        </div>

        <div class="grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-12">
            <nav class="lg:col-span-3" aria-labelledby="footer-institution">
                <h2 id="footer-institution" class="text-label text-brand-gold">{{ __('site.footer.institution') }}</h2>
                <ul class="mt-5 space-y-1 text-[0.9375rem]">
                    @php($footerNav = \App\Models\MenuItem::for('footer')->map->toNav()->all() ?: collect(['about', 'mission', 'partners', 'contact'])->map(fn ($p) => ['url' => lroute($p), 'label' => __('site.nav.'.$p), 'new_tab' => false])->all())
                    @foreach ($footerNav as $item)
                        <li><a href="{{ $item['url'] }}" @if($item['new_tab']) target="_blank" rel="noopener" @endif class="inline-flex min-h-10 items-center text-ink-200 hover:text-paper hover:underline underline-offset-4">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <nav class="lg:col-span-4" aria-labelledby="footer-domains">
                <h2 id="footer-domains" class="text-label text-brand-gold"><a href="{{ lroute('what-we-do') }}" class="hover:underline underline-offset-4">{{ __('site.nav.what_we_do') }}</a></h2>
                <ul class="mt-5 space-y-1 text-[0.9375rem]">
                    @foreach ($navDomains as $domain)
                        <li><a href="{{ $domain->url() }}" class="inline-flex min-h-10 items-center py-1 leading-snug text-ink-200 hover:text-paper hover:underline underline-offset-4">{{ $domain->tr('title') }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <nav class="lg:col-span-2" aria-labelledby="footer-involved">
                <h2 id="footer-involved" class="text-label text-brand-gold">{{ __('site.nav.get_involved') }}</h2>
                <ul class="mt-5 space-y-1 text-[0.9375rem]">
                    <li><a href="{{ lroute('get-involved') }}" class="inline-flex min-h-10 items-center text-ink-200 hover:text-paper hover:underline underline-offset-4">{{ __('site.nav.get_involved') }}</a></li>
                    <li><a href="{{ lroute('invest') }}" class="inline-flex min-h-10 items-center text-ink-200 hover:text-paper hover:underline underline-offset-4">{{ __('site.cta.invest_short') }}</a></li>
                    <li><a href="{{ lroute('submit') }}" class="inline-flex min-h-10 items-center text-ink-200 hover:text-paper hover:underline underline-offset-4">{{ __('site.cta.submit_short') }}</a></li>
                </ul>
            </nav>
            <div class="lg:col-span-3">
                <h2 class="text-label text-brand-gold">{{ __('site.footer.contact') }}</h2>
                <ul class="mt-5 space-y-3 text-[0.9375rem] text-ink-200">
                    @if (! empty($contact['email']))
                        <li><a href="mailto:{{ $contact['email'] }}" class="flex items-center gap-3 hover:text-paper"><x-glyph name="mail" :size="18" class="text-brand-gold" />{{ $contact['email'] }}</a></li>
                    @endif
                    @if (! empty($contact['phone']))
                        <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['phone']) }}" class="flex items-center gap-3 hover:text-paper"><x-glyph name="phone" :size="18" class="text-brand-gold" />{{ $contact['phone'] }}</a></li>
                    @endif
                    @if (! empty($contact['address']))
                        <li class="flex gap-3"><x-glyph name="map-pin" :size="18" class="mt-1 text-brand-gold" /><span class="whitespace-pre-line">{{ $contact['address'] }}</span></li>
                    @endif
                    <li><a href="{{ lroute('contact') }}" class="link-arrow">{{ __('site.footer.write_us') }} <x-glyph name="arrow-right" :size="16" /></a></li>
                </ul>
                @if ($socials)
                    <ul class="mt-6 flex flex-wrap gap-2" aria-label="{{ __('site.footer.socials') }}">
                        @foreach ($socials as $network => $url)
                            <li>
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center text-paper shadow-[inset_0_0_0_1px_var(--color-ink-600)] transition hover:bg-brand-gold hover:text-ink hover:shadow-none" aria-label="{{ ucfirst($network) }}">
                                    <x-glyph :name="$network" :size="18" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="flex flex-col gap-6 border-t border-ink-700 py-8 text-sm text-ink-300 lg:flex-row lg:items-center lg:justify-between">
            @include('partials.language-switcher', ['alternates' => $alternates, 'variant' => 'footer'])
            <ul class="flex flex-wrap gap-x-6 gap-y-2">
                @foreach (['legal', 'privacy', 'cookies', 'terms'] as $page)
                    <li><a href="{{ lroute($page) }}" class="hover:text-paper hover:underline underline-offset-4">{{ __("legal.$page.title") }}</a></li>
                @endforeach
                <li><button type="button" class="hover:text-paper hover:underline underline-offset-4" x-data x-on:click="$dispatch('open-cookie-settings')">{{ __('site.cookies.settings') }}</button></li>
            </ul>
            <p>© {{ now()->year }} {{ config('site.name') }}. {{ __('site.footer.rights') }}</p>
        </div>
    </div>
</footer>
