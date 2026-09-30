@php($left = ($data['position'] ?? 'right') === 'left')
<section class="section-y">
    <div class="container-site grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
        <div @class(['reveal', 'lg:order-2' => ! $left])>
            <x-picture :src="$data['image'] ?? null" :alt="tv($data['alt'] ?? null) ?? ''" sizes="(min-width: 1024px) 50vw, 100vw" img-class="aspect-[4/3] w-full object-cover" />
        </div>
        <div class="reveal">
            @if ($heading = tv($data['heading'] ?? null))
                <x-tricolor />
                <h2 class="text-h2 mt-6">{{ $heading }}</h2>
            @endif
            <div class="prose-site mt-6">{!! \App\Support\Html::clean(tv($data['body'] ?? null)) !!}</div>
        </div>
    </div>
</section>
