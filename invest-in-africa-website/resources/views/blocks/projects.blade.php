@php($projects = \App\Models\Project::published()->with(['sector', 'domain'])->orderByDesc('is_featured')->latest('published_at')->take((int) ($data['limit'] ?? 3))->get())
@if ($projects->isNotEmpty())
    <section class="section-y bg-ink-50">
        <div class="container-site">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                @if ($heading = tv($data['heading'] ?? null))<x-section-heading :title="e($heading)" />@endif
                <a href="{{ lroute('invest') }}" class="link-arrow">{{ __('site.cta.view_all_projects') }} <x-glyph name="arrow-right" :size="18" /></a>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" class="reveal" />
                @endforeach
            </div>
        </div>
    </section>
@endif
