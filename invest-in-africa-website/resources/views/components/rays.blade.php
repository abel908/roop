@props([
    'count' => 26,
    'from' => 180,
    'to' => 270,
    'radius' => 1000,
    'palette' => ['#0B9444', '#FEC43F', '#BF1E2D', '#000000'],
    'fill' => 0.55,
    'animate' => true,
    'origin' => '100% 100%',
])
@php
    // Graphic motif inspired by the rays of the logo (§4.6): wedges fanning
    // out from one point, cycling through the official palette.
    $step = ($to - $from) / $count;
    $rays = [];
    for ($i = 0; $i < $count; $i++) {
        $a1 = deg2rad($from + $i * $step);
        $a2 = deg2rad($from + $i * $step + $step * $fill);
        $rays[] = [
            'd' => sprintf('M0 0L%.1f %.1fL%.1f %.1fZ', cos($a1) * $radius, sin($a1) * $radius, cos($a2) * $radius, sin($a2) * $radius),
            'color' => $palette[$i % count($palette)],
        ];
    }
@endphp
<svg {{ $attributes->merge(['class' => 'pointer-events-none']) }} viewBox="{{ -$radius }} {{ -$radius }} {{ $radius * 2 }} {{ $radius * 2 }}" aria-hidden="true" focusable="false" preserveAspectRatio="xMidYMid slice">
    <g @class(['rays-animate' => $animate]) style="--rays-origin: {{ $origin }}; transform-box: view-box; transform-origin: 50% 50%;">
        @foreach ($rays as $ray)
            <path d="{{ $ray['d'] }}" fill="{{ $ray['color'] }}"/>
        @endforeach
    </g>
</svg>
