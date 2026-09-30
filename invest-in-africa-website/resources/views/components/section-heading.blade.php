@props(['eyebrow' => null, 'title', 'lead' => null, 'align' => 'left', 'as' => 'h2', 'dark' => false])
<div {{ $attributes->class(['reveal max-w-3xl', 'mx-auto text-center' => $align === 'center']) }}>
    @if ($eyebrow)
        <p @class(['eyebrow mb-5', 'eyebrow-on-dark' => $dark, 'justify-center' => $align === 'center'])>
            <x-tricolor class="!w-8" />{{ $eyebrow }}
        </p>
    @endif
    <{{ $as }} @class(['text-h2', 'text-paper' => $dark, 'text-ink' => ! $dark])>{!! $title !!}</{{ $as }}>
    @if ($lead)
        <p @class(['text-lead mt-5', 'text-ink-300' => $dark, 'text-ink-500' => ! $dark])>{{ $lead }}</p>
    @endif
</div>
