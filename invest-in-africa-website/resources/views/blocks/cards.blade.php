<section class="section-y">
    <div class="container-site">
        @if ($heading = tv($data['heading'] ?? null))
            <x-section-heading :title="e($heading)" :lead="tv($data['lead'] ?? null)" />
        @endif
        <ul class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($data['items'] ?? [] as $item)
                <li @class(['card reveal p-8', 'card-hover' => ! empty($item['url'])])>
                    @if (! empty($item['icon']))<x-glyph :name="$item['icon']" :size="32" class="text-brand-green" />@endif
                    <h3 class="text-h3 mt-6">
                        @if (! empty($item['url']))
                            <a href="{{ $item['url'] }}" class="after:absolute after:inset-0">{{ tv($item['title'] ?? null) }}</a>
                        @else
                            {{ tv($item['title'] ?? null) }}
                        @endif
                    </h3>
                    @if ($text = tv($item['text'] ?? null))<p class="mt-3 text-base text-ink-500">{{ $text }}</p>@endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
