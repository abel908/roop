<section class="section-y bg-ink-50">
    <div class="container-site grid gap-12 lg:grid-cols-12">
        <div class="lg:col-span-4">
            @if ($heading = tv($data['heading'] ?? null))<x-section-heading :title="e($heading)" />@endif
        </div>
        <div class="divide-y divide-ink-200 border-y border-ink-200 lg:col-span-8">
            @foreach ($data['items'] ?? [] as $item)
                <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    <h3>
                        <button type="button" x-on:click="open = !open" :aria-expanded="open" class="flex min-h-16 w-full items-center justify-between gap-6 py-5 text-left font-display text-lg font-bold">
                            {{ tv($item['q'] ?? null) }}
                            <x-glyph name="chevron-down" :size="22" class="transition-transform" x-bind:class="open && 'rotate-180'" />
                        </button>
                    </h3>
                    <div x-show="open" x-collapse><p class="pb-6 text-ink-600">{{ tv($item['a'] ?? null) }}</p></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
