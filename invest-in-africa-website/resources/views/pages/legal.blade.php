@extends('layouts.site')

@section('content')
    <x-page-hero :title="__('legal.'.$page.'.title')" :dark="false" :rays="false" />

    <section class="section-y">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <nav class="lg:col-span-3" aria-label="{{ __('site.footer.institution') }}">
                <ul class="space-y-1 border-l border-ink-200">
                    @foreach (['legal', 'privacy', 'cookies', 'terms'] as $item)
                        <li>
                            <a href="{{ lroute($item) }}" @if($item === $page) aria-current="page" @endif
                               class="-ml-px flex min-h-11 items-center border-l-2 pl-5 text-[0.9375rem] {{ $item === $page ? 'border-brand-green font-bold text-ink' : 'border-transparent text-ink-500 hover:text-ink' }}">
                                {{ __("legal.$item.title") }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <article class="prose-site lg:col-span-8 lg:col-start-5">
                {!! __('legal.'.$page.'.body', $replace) !!}
            </article>
        </div>
    </section>
@endsection
