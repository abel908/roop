<section class="section-y">
    <div class="container-site">
        @if ($heading = tv($data['heading'] ?? null))<x-section-heading :title="e($heading)" />@endif
        <ul class="mt-12 grid gap-px bg-ink-200 shadow-[0_0_0_1px_var(--color-ink-200)] sm:grid-cols-2 lg:grid-cols-3">
            @foreach (\App\Models\Domain::published()->get() as $domain)
                <li class="reveal bg-paper">@include('partials.domain-card', ['domain' => $domain])</li>
            @endforeach
        </ul>
    </div>
</section>
