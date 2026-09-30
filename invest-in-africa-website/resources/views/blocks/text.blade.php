<section class="section-y">
    <div class="container-site grid gap-10 lg:grid-cols-12">
        @if ($heading = tv($data['heading'] ?? null))
            <div class="lg:col-span-4"><x-section-heading :title="e($heading)" /></div>
        @endif
        <div @class(['prose-site reveal', 'lg:col-span-7 lg:col-start-6' => $heading, 'lg:col-span-8' => ! $heading])>
            {!! \App\Support\Html::clean(tv($data['body'] ?? null)) !!}
        </div>
    </div>
</section>
