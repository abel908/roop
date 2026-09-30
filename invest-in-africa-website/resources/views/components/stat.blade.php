@props(['value', 'label', 'suffix' => '', 'dark' => false, 'note' => null])
<div {{ $attributes->merge(['class' => 'reveal']) }} x-data="counter({{ (float) $value }})" x-intersect.once="start()">
    <p @class(['font-display text-5xl font-extrabold tracking-tight md:text-6xl', 'text-brand-gold' => $dark, 'text-ink' => ! $dark])>
        <span x-text="display">{{ $value }}</span>{{ $suffix }}
    </p>
    <x-tricolor class="my-4 !w-10" />
    <p @class(['max-w-[16rem] font-semibold', 'text-paper' => $dark, 'text-ink' => ! $dark])>{{ $label }}</p>
    @if ($note)
        <p @class(['mt-1 text-sm', 'text-ink-400' => $dark, 'text-ink-500' => ! $dark])>{{ $note }}</p>
    @endif
</div>
