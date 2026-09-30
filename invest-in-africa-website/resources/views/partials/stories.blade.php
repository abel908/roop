@php($stories = collect(data_get($settings, 'impact.stories', []))->filter(fn ($s) => tv($s['title'] ?? null)))
{{-- Impact stories, case studies and testimonies (§5.1 section 7, §5.2, §5.3) --}}
@if ($stories->isNotEmpty())
    <div @class(['grid gap-6 md:grid-cols-2 lg:grid-cols-3', $class ?? ''])>
        @foreach ($stories as $story)
            <article @class(['reveal flex flex-col overflow-hidden', 'bg-ink-800 text-paper' => $dark ?? false, 'card' => ! ($dark ?? false)])>
                @if (! empty($story['image']))
                    <x-picture :src="$story['image']" sizes="(min-width: 1024px) 33vw, 100vw" class="block aspect-[4/3]" img-class="size-full object-cover" />
                @endif
                <div class="flex flex-1 flex-col p-7">
                    @if (! empty($story['country']))
                        <p @class(['flex items-center gap-1.5 text-sm font-semibold', 'text-brand-gold' => $dark ?? false, 'text-brand-green-deep' => ! ($dark ?? false)])>
                            <x-glyph name="map-pin" :size="15" />{{ \App\Support\Countries::name($story['country']) }}
                        </p>
                    @endif
                    <h3 class="text-h3 mt-3">{{ tv($story['title']) }}</h3>
                    @if ($text = tv($story['text'] ?? null))
                        <p @class(['mt-3 text-base', 'text-ink-300' => $dark ?? false, 'text-ink-500' => ! ($dark ?? false)])>{{ $text }}</p>
                    @endif
                    @if ($quote = tv($story['quote'] ?? null))
                        <blockquote @class(['mt-6 border-l-2 pl-4 font-display font-bold', 'border-brand-gold' => $dark ?? false, 'border-brand-green' => ! ($dark ?? false)])>
                            “{{ $quote }}”
                            @if ($author = tv($story['author'] ?? null))
                                <footer @class(['mt-2 font-sans text-sm font-normal', 'text-ink-400' => $dark ?? false, 'text-ink-500' => ! ($dark ?? false)])>— {{ $author }}</footer>
                            @endif
                        </blockquote>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif
