@props(['type' => 'info'])
@php
    [$icon, $classes] = match ($type) {
        'error' => ['alert', 'border-brand-red bg-[#fdf2f3] text-ink'],
        'success' => ['check-circle', 'border-brand-green bg-[#f0f9f4] text-ink'],
        default => ['info', 'border-ink bg-ink-50 text-ink'],
    };
@endphp
<div {{ $attributes->merge(['class' => "flex gap-3 border-l-4 px-5 py-4 text-[0.9375rem] $classes"]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <x-glyph :name="$icon" :size="20" @class(['mt-0.5', 'text-brand-red' => $type === 'error', 'text-brand-green-deep' => $type === 'success']) />
    <div class="flex-1">{{ $slot }}</div>
</div>
