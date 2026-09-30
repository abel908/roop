@props(['dark' => true])
@php($crumbs = app(\App\Support\Seo::class)->breadcrumbs)
@if ($crumbs)
    <nav aria-label="{{ __('site.a11y.breadcrumb') }}">
        <ol @class(['flex flex-wrap items-center gap-x-2 gap-y-1 text-sm', 'text-ink-300' => $dark, 'text-ink-500' => ! $dark])>
            <li><a href="{{ lroute('home') }}" class="underline-offset-4 hover:underline">{{ __('site.nav.home') }}</a></li>
            @foreach ($crumbs as $crumb)
                <li class="flex items-center gap-2" @if($loop->last) aria-current="page" @endif>
                    <x-glyph name="chevron-right" :size="14" class="opacity-60" />
                    @if ($loop->last)
                        <span @class(['font-medium', 'text-paper' => $dark, 'text-ink' => ! $dark])>{{ $crumb['label'] }}</span>
                    @else
                        <a href="{{ $crumb['url'] }}" class="underline-offset-4 hover:underline">{{ $crumb['label'] }}</a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
