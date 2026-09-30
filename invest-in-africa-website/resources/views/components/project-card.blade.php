@props(['project', 'headingLevel' => 'h3'])
@php
    $statusClasses = match ($project->status->value) {
        'open' => 'bg-brand-green text-paper',
        'funding' => 'bg-brand-gold text-ink',
        'funded' => 'bg-ink text-brand-gold',
        default => 'bg-ink-100 text-ink-600',
    };
@endphp
<article {{ $attributes->merge(['class' => 'card card-hover group']) }}>
    <div class="relative aspect-[16/10] overflow-hidden bg-ink">
        @if ($project->coverUrl())
            <x-picture :src="$project->cover_image" sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw"
                       class="block size-full" img-class="size-full object-cover transition duration-700 ease-out group-hover:scale-[1.03]" />
        @else
            {{-- Placeholder until visuals are supplied: sector pictogram on the brand motif --}}
            <div class="grain absolute inset-0 flex items-center justify-center overflow-hidden">
                <x-rays class="absolute -right-24 -bottom-40 size-[36rem] opacity-40" :count="16" :animate="false" :palette="['#0B9444', '#FEC43F', '#BF1E2D', '#262626']" />
                <x-domain-icon :icon="$project->domain?->icon ?? 'financing'" :size="64" class="relative text-paper" />
            </div>
        @endif
        <span class="absolute top-4 left-4 inline-flex items-center px-2.5 py-1 text-xs font-bold tracking-wide {{ $statusClasses }}">
            {{ $project->status->getLabel() }}
        </span>
    </div>
    <div class="flex flex-1 flex-col p-6">
        <p class="text-sm font-semibold text-ink-500">
            <span class="font-mono text-xs tracking-wider text-ink-400">{{ $project->reference }}</span>
            <span class="mx-1.5 text-ink-300" aria-hidden="true">/</span>
            {{ $project->sector?->tr('name') }}
        </p>
        <{{ $headingLevel }} class="text-h3 mt-3 text-ink">
            <a href="{{ $project->url() }}" class="after:absolute after:inset-0 focus-visible:outline-none" data-track="project_card_click" data-track-reference="{{ $project->reference }}">
                {{ $project->tr('title') }}
            </a>
        </{{ $headingLevel }}>
        <dl class="mt-auto grid grid-cols-2 gap-4 border-t border-ink-200 pt-5 text-sm">
            <div>
                <dt class="text-ink-500">{{ __('projects.fields.country') }}</dt>
                <dd class="mt-1 flex items-center gap-1.5 font-semibold text-ink"><x-glyph name="map-pin" :size="15" class="text-brand-green" />{{ $project->countryName() }}</dd>
            </div>
            <div>
                <dt class="text-ink-500">{{ __('projects.fields.amount') }}</dt>
                <dd class="mt-1 font-display font-bold text-ink">{{ $project->amountCompact() }}</dd>
            </div>
        </dl>
    </div>
</article>
