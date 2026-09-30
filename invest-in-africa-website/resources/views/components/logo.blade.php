@props(['plate' => false, 'width' => 150, 'eager' => false])
{{--
    Official logo file only — no redrawing, recolouring, filter or distortion (§4.4).
    The transmitted file has a white background: on dark sections it sits on a
    white plate that also serves as its protection area.
--}}
<span {{ $attributes->class(['inline-flex shrink-0', 'bg-paper p-3' => $plate]) }}>
    <img src="{{ asset('images/logo-invest-in-africa.png') }}"
         alt="{{ config('site.name') }}"
         width="{{ $width }}" height="{{ round($width * 214 / 298) }}"
         style="width: {{ $width }}px; height: auto; min-width: 96px;"
         @if($eager) fetchpriority="high" @else loading="lazy" @endif
         decoding="async">
</span>
