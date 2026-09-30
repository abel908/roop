{{-- Area of intervention card: number, custom pictogram, official title, tagline, link --}}
<a href="{{ $domain->url() }}" class="group relative flex h-full flex-col p-8 transition-colors duration-300 hover:bg-ink-50 md:p-10">
    <span class="absolute inset-x-0 top-0 h-1 origin-left scale-x-0 bg-brand-green transition-transform duration-500 ease-[var(--ease-out-soft)] group-hover:scale-x-100" aria-hidden="true"></span>
    <div class="flex items-start justify-between">
        <x-domain-icon :icon="$domain->icon" :size="52" class="text-ink transition-transform duration-500 group-hover:-translate-y-1" />
        <span class="font-display text-sm font-bold text-ink-400">{{ $domain->numberLabel() }}</span>
    </div>
    <h3 class="text-h3 mt-10 text-ink">{{ $domain->tr('title') }}</h3>
    <p class="mt-3 flex-1 text-base text-ink-500">{{ $domain->tr('tagline') }}</p>
    <span class="link-arrow mt-8">{{ __('site.cta.discover') }} <x-glyph name="arrow-right" :size="18" /></span>
</a>
