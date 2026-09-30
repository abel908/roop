@props(['name', 'size' => 20])
{{-- Interface icons — linear style consistent with the domain pictograms --}}
<svg {{ $attributes->merge(['class' => 'shrink-0']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
@switch($name)
    @case('arrow-right')<path d="M4 12h15M13 6l6 6-6 6"/>@break
    @case('arrow-left')<path d="M20 12H5M11 6l-6 6 6 6"/>@break
    @case('arrow-up-right')<path d="M7 17 17 7M8 7h9v9"/>@break
    @case('chevron-down')<path d="m6 9 6 6 6-6"/>@break
    @case('chevron-right')<path d="m9 6 6 6-6 6"/>@break
    @case('menu')<path d="M3 7h18M3 12h18M3 17h18"/>@break
    @case('close')<path d="M6 6l12 12M18 6 6 18"/>@break
    @case('globe')<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3Z"/>@break
    @case('mail')<rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3.5 6 8.5 7 8.5-7"/>@break
    @case('phone')<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/>@break
    @case('map-pin')<path d="M12 21s-7-6.1-7-11.5a7 7 0 1 1 14 0C19 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>@break
    @case('clock')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@break
    @case('download')<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>@break
    @case('file')<path d="M14 3H6v18h12V7l-4-4Z"/><path d="M14 3v4h4"/>@break
    @case('upload')<path d="M12 16V4M7 9l5-5 5 5M5 20h14"/>@break
    @case('check')<path d="m5 12.5 4.5 4.5L19 7.5"/>@break
    @case('check-circle')<circle cx="12" cy="12" r="9"/><path d="m8 12.5 3 3 5-6"/>@break
    @case('alert')<circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5M12 16.5v.01"/>@break
    @case('info')<circle cx="12" cy="12" r="9"/><path d="M12 11v5.5M12 7.5v.01"/>@break
    @case('search')<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>@break
    @case('filter')<path d="M4 6h16M7 12h10M10 18h4"/>@break
    @case('trash')<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>@break
    @case('lock')<rect x="5" y="11" width="14" height="10" rx="1"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>@break
    @case('users')<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.2a6.5 6.5 0 0 1 3.5 5.8"/>@break
    @case('target')<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>@break
    @case('leaf')<path d="M5 19c0-8 5-13 15-14-1 10-6 15-14 15"/><path d="M5 19 13 11"/>@break
    @case('briefcase')<rect x="3" y="7" width="18" height="13" rx="1"/><path d="M9 7V4h6v3M3 13h18"/>@break
    @case('calendar')<rect x="3.5" y="5" width="17" height="15" rx="1"/><path d="M3.5 10h17M8 3v4M16 3v4"/>@break
    @case('shield')<path d="M12 3 4.5 6v5.5c0 4.7 3.2 8.4 7.5 9.5 4.3-1.1 7.5-4.8 7.5-9.5V6L12 3Z"/><path d="m8.8 12 2.2 2.2 4.2-4.4"/>@break
    @case('handshake')<path d="m2.5 11.5 4-4 3 1.5 3-2 3.5 1 5.5 4"/><path d="m6.5 7.5-4 4 5.5 5.5c.8.8 2 .8 2.8 0l.2-.2M21.5 12l-5.5 5.5c-.8.8-2 .8-2.8 0L9 13.3"/><path d="m12.5 6.5-3 3a1.4 1.4 0 0 0 2 2l2.5-2"/>@break
    @case('spark')<path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"/>@break
    @case('linkedin')<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>@break
    @case('x')<path d="M4 4l16 16M20 4 4 20"/>@break
    @case('facebook')<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8Z"/>@break
    @case('instagram')<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5v.01"/>@break
    @case('youtube')<rect x="2.5" y="5.5" width="19" height="13" rx="3"/><path d="m10 9.5 5 2.5-5 2.5Z"/>@break
    @case('wechat')<path d="M9.5 4C5.4 4 2.5 6.6 2.5 9.7c0 1.8 1 3.3 2.5 4.4L4.5 16l2.4-1.2c.8.2 1.7.4 2.6.4"/><path d="M15 9.5c3.6 0 6.5 2.3 6.5 5.1 0 1.5-.8 2.8-2 3.8l.4 1.6-2-1c-.9.3-1.9.5-2.9.5-3.6 0-6.5-2.3-6.5-5.1S11.4 9.5 15 9.5Z"/>@break
    @case('weibo')<path d="M10 19c-4 0-7.5-1.9-7.5-4.7 0-2.9 4.2-7.3 7.6-7.3 1.7 0 1.4 1.8 1.1 2.8 2-1 5.4-1.3 4.5 1.5 1.7.5 3 1.3 3 2.8 0 2.8-4 4.9-8.7 4.9Z"/><path d="M16 4.5a4.5 4.5 0 0 1 4.5 4.5"/>@break
    @default<circle cx="12" cy="12" r="9"/>
@endswitch
</svg>
