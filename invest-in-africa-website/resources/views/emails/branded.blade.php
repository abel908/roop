@php($locale = app()->getLocale())
<!DOCTYPE html>
<html lang="{{ \App\Support\Locales::hreflang($locale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $heading }}</title>
</head>
{{-- Institutional HTML template (§6.5) — table layout and inline styles for email clients --}}
<body style="margin:0;padding:0;background:#f7f7f5;font-family:Helvetica,Arial,'PingFang SC','Microsoft YaHei',sans-serif;color:#000;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f5;">
    <tr>
        <td align="center" style="padding:32px 16px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;">
                <tr>
                    <td style="padding:0;font-size:0;line-height:0;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td height="6" style="background:#0B9444;width:33.33%;"></td>
                            <td height="6" style="background:#FEC43F;width:33.33%;"></td>
                            <td height="6" style="background:#BF1E2D;width:33.34%;"></td>
                        </tr></table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px 40px 8px;">
                        <img src="{{ asset('images/logo-invest-in-africa.png') }}" width="140" alt="{{ config('site.name') }}" style="display:block;width:140px;height:auto;border:0;">
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 40px 8px;">
                        <h1 style="margin:0;font-size:26px;line-height:1.25;font-weight:800;color:#000;">{{ $heading }}</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 40px 0;font-size:16px;line-height:1.65;color:#262626;">
                        <p style="margin:0 0 16px;">{{ $body }}</p>
                        @if ($next)
                            <p style="margin:0 0 16px;">{{ $next }}</p>
                        @endif
                    </td>
                </tr>
                @if ($details)
                    <tr>
                        <td style="padding:8px 40px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-left:4px solid #FEC43F;background:#f7f7f5;">
                                <tr><td colspan="2" style="padding:16px 20px 4px;font-size:12px;letter-spacing:.08em;text-transform:uppercase;font-weight:700;color:#5c5c5c;">{{ __('emails.details') }}</td></tr>
                                @foreach ($details as $label => $value)
                                    <tr>
                                        <td valign="top" style="padding:6px 12px 6px 20px;font-size:14px;color:#5c5c5c;width:38%;">{{ $label }}</td>
                                        <td valign="top" style="padding:6px 20px 6px 0;font-size:14px;font-weight:700;color:#000;white-space:pre-line;">{{ $value }}</td>
                                    </tr>
                                @endforeach
                                <tr><td colspan="2" height="12"></td></tr>
                            </table>
                        </td>
                    </tr>
                @endif
                @if ($action && $actionUrl)
                    <tr>
                        <td style="padding:24px 40px 8px;">
                            <a href="{{ $actionUrl }}" style="display:inline-block;background:#0B9444;color:#ffffff;text-decoration:none;font-weight:700;font-size:15px;padding:14px 24px;border-radius:2px;">{{ $action }} &rarr;</a>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:24px 40px 32px;font-size:16px;line-height:1.6;color:#262626;">
                        {{ __('emails.signature') }}
                    </td>
                </tr>
                <tr>
                    <td style="background:#000;padding:24px 40px;font-size:12px;line-height:1.6;color:#bdbdbd;">
                        <strong style="color:#FEC43F;">{{ config('site.name') }}</strong><br>
                        {{ __('emails.footer') }}<br>
                        <a href="{{ lroute('home', [], $locale) }}" style="color:#ffffff;">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
