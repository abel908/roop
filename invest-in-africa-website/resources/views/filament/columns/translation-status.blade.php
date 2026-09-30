{{-- Translation status per language (§7.2): green = translated, amber = missing --}}
<div class="flex gap-1 px-3 py-2">
    @foreach ($getRecord()->translationStatus() as $locale => $done)
        <span title="{{ $done ? __('admin.translated') : __('admin.translation_missing') }}"
              style="display:inline-flex;align-items:center;padding:1px 6px;font-size:11px;font-weight:700;border-radius:3px;{{ $done ? 'background:#e7f5ec;color:#08703A' : 'background:#fff4d6;color:#8a5a00' }}">
            {{ $locale === 'zh' ? '中文' : strtoupper($locale) }}
        </span>
    @endforeach
</div>
