@props(['icon', 'size' => 48])
{{--
    Custom pictograms for the six areas of intervention (§4.6) — linear
    style, 48 grid, 1.75 stroke, one accent stroke in the logo green.
--}}
<svg {{ $attributes->merge(['class' => 'shrink-0']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
@switch($icon)
    @case('fdi')
        {{-- 01 FDI Promotion, Orientation & Support: globe + inbound compass arrow --}}
        <circle cx="22" cy="24" r="15"/>
        <path d="M7 24h30M22 9c4 4.2 6 9.2 6 15s-2 10.8-6 15c-4-4.2-6-9.2-6-15s2-10.8 6-15Z"/>
        <path d="M44 6 31 19" stroke="#0B9444" stroke-width="2.25"/>
        <path d="M31 12v7h7" stroke="#0B9444" stroke-width="2.25"/>
        @break
    @case('financing')
        {{-- 02 African Projects Investment & Financing: coin stacks + growth --}}
        <ellipse cx="15" cy="34" rx="9" ry="3.5"/>
        <path d="M6 34v5c0 1.9 4 3.5 9 3.5s9-1.6 9-3.5v-5"/>
        <path d="M6 28.5c0 1.9 4 3.5 9 3.5s9-1.6 9-3.5M6 28.5v5.5M24 28.5v5.5"/>
        <ellipse cx="15" cy="28.5" rx="9" ry="3.5"/>
        <path d="M29 40h13M31 40V30M36 40V24M41 40V17" />
        <path d="M27 20 35 12l4 4 6-6" stroke="#0B9444" stroke-width="2.25"/>
        <path d="M40 10h5v5" stroke="#0B9444" stroke-width="2.25"/>
        @break
    @case('talents')
        {{-- 03 Local Talents & Ideas, SME Coaching & Training: bulb + growth spark --}}
        <path d="M17 31c-3.6-2.4-6-6.4-6-11a13 13 0 0 1 26 0c0 4.6-2.4 8.6-6 11v4H17v-4Z"/>
        <path d="M18 39h12M20 43h8"/>
        <path d="M24 35V25l-4-4M24 25l4-4" stroke="#0B9444" stroke-width="2.25"/>
        <path d="M24 2v3M40 8l-2 2M8 8l2 2M46 20h-3M2 20h3"/>
        @break
    @case('industry')
        {{-- 04 VSME Coaching in Industrialization & Manufacturing: factory + gear --}}
        <path d="M5 42V25l9 5v-5l9 5v-5l9 5V9h8v33H5Z"/>
        <path d="M2 42h44M10 36h4M18 36h4M26 36h4M34 16h4"/>
        <circle cx="14" cy="12" r="3.25" stroke="#0B9444" stroke-width="2.25"/>
        <path d="M14 4.5v2.2M14 17.3v2.2M6.5 12h2.2M19.3 12h2.2M8.7 6.7l1.6 1.6M17.7 15.7l1.6 1.6M8.7 17.3l1.6-1.6M17.7 8.3l1.6-1.6" stroke="#0B9444" stroke-width="2.25"/>
        @break
    @case('diaspora')
        {{-- 05 Diaspora Investment Guide & Relations: two places linked by a route --}}
        <path d="M11 20s-6-5.2-6-9.8a6 6 0 1 1 12 0c0 4.6-6 9.8-6 9.8Z"/>
        <circle cx="11" cy="10" r="2"/>
        <path d="M37 44s-6-5.2-6-9.8a6 6 0 1 1 12 0c0 4.6-6 9.8-6 9.8Z"/>
        <circle cx="37" cy="34" r="2"/>
        <path d="M11 24c0 8 6 10 13 10M24 14c7 0 13 2 13 10" stroke="#0B9444" stroke-width="2.25" stroke-dasharray="1 4.5"/>
        <path d="M21 11l3 3-3 3" stroke="#0B9444" stroke-width="2.25"/>
        @break
    @case('government')
        {{-- 06 African Government Affairs & Lobbying: institution --}}
        <path d="M4 17 24 5l20 12H4Z"/>
        <path d="M8 21v15M16 21v15M32 21v15M40 21v15"/>
        <path d="M24 21v15" stroke="#0B9444" stroke-width="2.25"/>
        <path d="M5 40h38M3 44h42"/>
        @break
    @default
        <circle cx="24" cy="24" r="18"/>
@endswitch
</svg>
