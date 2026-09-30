{{-- Identical box per logo, centred, original proportions and colours, no filter (§5.5) --}}
@php($tabbable = $tabbable ?? true)
@if ($partner->website_url)
    <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" class="flex size-full items-center justify-center" data-track="partner_click" data-track-partner="{{ $partner->name }}" @unless($tabbable) tabindex="-1" @endunless>
@else
    <span class="flex size-full items-center justify-center">
@endif
    @if ($partner->logoUrl())
        <img src="{{ $partner->logoUrl() }}" alt="{{ $partner->name }}" loading="lazy" decoding="async" class="max-h-16 w-auto max-w-full object-contain">
    @else
        <span class="font-display text-lg font-bold text-ink-600">{{ $partner->name }}</span>
    @endif
@if ($partner->website_url)
    </a>
@else
    </span>
@endif
