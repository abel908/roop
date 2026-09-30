@php($locales = \App\Support\Locales::codes())
{{-- English · Français · 中文 — labels in their own language, no flags (§7.3) --}}
@if ($variant === 'header')
    <div class="relative hidden sm:block" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape="open = false">
        <button type="button" x-on:click="open = !open" :aria-expanded="open" aria-haspopup="true"
                class="inline-flex min-h-11 items-center gap-2 px-2 text-[0.9375rem] font-semibold text-ink hover:text-brand-green-deep"
                aria-label="{{ __('site.language.choose') }}: {{ \App\Support\Locales::label($currentLocale) }}">
            <x-glyph name="globe" :size="18" />
            <span>{{ \App\Support\Locales::label($currentLocale) }}</span>
            <x-glyph name="chevron-down" :size="14" />
        </button>
        <ul x-cloak x-show="open" x-transition.opacity.duration.200ms
            class="absolute right-0 mt-2 w-44 bg-paper py-2 shadow-[0_0_0_1px_var(--color-ink-200),0_20px_40px_-20px_rgba(0,0,0,.35)]">
            @foreach ($locales as $locale)
                <li>
                    <a href="{{ $alternates[$locale] }}" hreflang="{{ \App\Support\Locales::hreflang($locale) }}" lang="{{ \App\Support\Locales::hreflang($locale) }}"
                       @if($locale === $currentLocale) aria-current="true" @endif
                       class="flex min-h-11 items-center justify-between px-4 text-[0.9375rem] hover:bg-ink-50 {{ $locale === $currentLocale ? 'font-bold text-brand-green-deep' : 'text-ink' }}"
                       data-track="language_switch" data-track-to="{{ $locale }}">
                        {{ \App\Support\Locales::label($locale) }}
                        @if ($locale === $currentLocale)<x-glyph name="check" :size="16" />@endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <nav aria-label="{{ __('site.language.choose') }}">
        <ul class="flex shrink-0 flex-nowrap items-center gap-x-1 whitespace-nowrap text-[0.9375rem]">
            @foreach ($locales as $locale)
                <li class="flex items-center">
                    @if (! $loop->first)<span class="px-1.5 opacity-40" aria-hidden="true">·</span>@endif
                    <a href="{{ $alternates[$locale] }}" hreflang="{{ \App\Support\Locales::hreflang($locale) }}" lang="{{ \App\Support\Locales::hreflang($locale) }}"
                       @if($locale === $currentLocale) aria-current="true" @endif
                       class="inline-flex min-h-11 items-center px-1 {{ $locale === $currentLocale ? 'font-bold underline decoration-2 underline-offset-8 '.($variant === 'footer' ? 'text-brand-gold' : 'text-brand-green-deep') : 'hover:underline underline-offset-8' }}"
                       data-track="language_switch" data-track-to="{{ $locale }}">
                        {{ \App\Support\Locales::label($locale) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
@endif
