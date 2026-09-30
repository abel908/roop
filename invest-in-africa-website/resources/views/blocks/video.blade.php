<section class="section-y">
    <div class="container-site">
        <figure class="reveal">
            <video class="aspect-video w-full bg-ink" controls preload="none" playsinline
                   @if(! empty($data['image'])) poster="{{ asset('storage/'.$data['image']) }}" @endif>
                <source src="{{ asset('storage/'.$data['video']) }}" type="video/mp4">
            </video>
            @if ($caption = tv($data['caption'] ?? null))
                <figcaption class="mt-4 text-sm text-ink-500">{{ $caption }}</figcaption>
            @endif
        </figure>
    </div>
</section>
