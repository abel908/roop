@props(['size' => null, 'location' => 'page'])
{{-- The two primary actions (§5.1): green investor path, black & gold project-holder path --}}
<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 sm:flex-row sm:flex-wrap']) }}>
    <a href="{{ lroute('invest') }}" @class(['btn btn-invest', 'btn-sm' => $size === 'sm'])
       data-track="cta_invest_click" data-track-location="{{ $location }}">
        {{ __('site.cta.invest') }}
        <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
    </a>
    <a href="{{ lroute('submit') }}" @class(['btn btn-submit', 'btn-sm' => $size === 'sm'])
       data-track="cta_submit_click" data-track-location="{{ $location }}">
        {{ __('site.cta.submit') }}
        <x-glyph name="arrow-right" :size="18" class="btn-arrow" />
    </a>
</div>
