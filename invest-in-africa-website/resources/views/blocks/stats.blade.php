@php($dark = ! empty($data['dark']))
<section @class(['section-y', 'on-dark grain bg-ink text-paper' => $dark, 'bg-ink-50' => ! $dark])>
    <div class="container-site">
        @if ($heading = tv($data['heading'] ?? null))
            <x-section-heading :title="e($heading)" :dark="$dark" />
        @endif
        <div class="mt-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($data['items'] ?? [] as $item)
                <x-stat :dark="$dark" :value="$item['value'] ?? 0" :suffix="$item['suffix'] ?? ''" :label="tv($item['label'] ?? null) ?? ''" />
            @endforeach
        </div>
    </div>
</section>
