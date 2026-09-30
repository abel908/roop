@if ($paginator->hasPages())
    <nav aria-label="{{ __('site.a11y.pagination') }}" class="flex items-center justify-center gap-1">
        @if ($paginator->onFirstPage())
            <span class="inline-flex size-11 items-center justify-center text-ink-300" aria-hidden="true"><x-glyph name="arrow-left" :size="18" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex size-11 items-center justify-center hover:bg-ink-50" aria-label="{{ __('pagination.previous') }}"><x-glyph name="arrow-left" :size="18" /></a>
        @endif
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="inline-flex size-11 items-center justify-center text-ink-400">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex size-11 items-center justify-center bg-ink font-bold text-paper">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex size-11 items-center justify-center font-semibold hover:bg-ink-50">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex size-11 items-center justify-center hover:bg-ink-50" aria-label="{{ __('pagination.next') }}"><x-glyph name="arrow-right" :size="18" /></a>
        @else
            <span class="inline-flex size-11 items-center justify-center text-ink-300" aria-hidden="true"><x-glyph name="arrow-right" :size="18" /></span>
        @endif
    </nav>
@endif
