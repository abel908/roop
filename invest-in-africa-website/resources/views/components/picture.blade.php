@props(['src' => null, 'media' => null, 'alt' => '', 'sizes' => '100vw', 'eager' => false, 'imgClass' => ''])
@php
    // Responsive image (§10.4): AVIF and WebP srcset with the original as fallback.
    if ($media instanceof \App\Models\Media) {
        $src = $media->file;
        $alt = $alt ?: $media->altText();
    }
    $manifest = $src ? \App\Services\ImageOptimizer::manifest($src) : null;
    $srcset = fn (string $format) => collect($manifest['variants'][$format] ?? [])
        ->map(fn ($path, $width) => \Illuminate\Support\Facades\Storage::disk('public')->url($path).' '.$width.'w')
        ->implode(', ');
@endphp
@if ($src)
    <picture {{ $attributes }}>
        @foreach (['avif', 'webp'] as $format)
            @if ($set = $srcset($format))
                <source type="image/{{ $format }}" srcset="{{ $set }}" sizes="{{ $sizes }}">
            @endif
        @endforeach
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($src) }}" alt="{{ $alt }}"
             @if($manifest) width="{{ $manifest['width'] }}" height="{{ $manifest['height'] }}" @endif
             @if($eager) fetchpriority="high" @else loading="lazy" @endif decoding="async"
             class="{{ $imgClass }}">
    </picture>
@endif
