@php($documents = \App\Models\MediaDocument::published()->whereIn('id', $data['documents'] ?? [])->get())
@if ($documents->isNotEmpty())
    <section class="section-y">
        <div class="container-site">
            @if ($heading = tv($data['heading'] ?? null))<x-section-heading :title="e($heading)" />@endif
            <ul class="mt-10 grid gap-4 md:grid-cols-2">
                @foreach ($documents as $document)
                    <li>
                        <a href="{{ asset('storage/'.$document->file) }}" download class="card card-hover flex items-center gap-5 p-6" data-track="document_download" data-track-document="{{ $document->tr('title') }}">
                            <span class="flex size-12 items-center justify-center bg-ink text-xs font-bold text-brand-gold">{{ $document->extension() }}</span>
                            <span class="flex-1 font-semibold">{{ $document->tr('title') }}</span>
                            <x-glyph name="download" :size="22" class="text-brand-green-deep" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
