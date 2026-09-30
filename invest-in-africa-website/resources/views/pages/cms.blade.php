@extends('layouts.site')

@section('content')
    @if ($preview)
        <div class="sticky top-0 z-[55] bg-brand-gold px-5 py-2 text-center text-sm font-bold text-ink" role="status">
            {{ __('admin.preview_banner', ['status' => $page->isLive() ? __('admin.status.published') : __('admin.status.draft')]) }}
        </div>
    @endif

    @if (($blocks->first()['type'] ?? null) !== 'hero')
        <x-page-hero :title="e($page->tr('title'))" />
    @endif

    @foreach ($blocks as $block)
        @includeIf('blocks.'.$block['type'], ['data' => $block['data'] ?? [], 'first' => $loop->first])
    @endforeach
@endsection
