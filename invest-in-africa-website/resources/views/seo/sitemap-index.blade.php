{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($sitemaps as $sitemap)
    <sitemap>
        <loc>{{ $sitemap }}</loc>
@if ($lastmod)
        <lastmod>{{ \Illuminate\Support\Carbon::parse($lastmod)->toAtomString() }}</lastmod>
@endif
    </sitemap>
@endforeach
</sitemapindex>
