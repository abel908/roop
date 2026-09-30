{{-- Honeypot + filling-time token (§6.3) — no third-party captcha, works from China --}}
<div class="hp-field" aria-hidden="true">
    <label for="hp-{{ config('site.antispam.honeypot_field') }}">Website</label>
    <input id="hp-{{ config('site.antispam.honeypot_field') }}" type="text" name="{{ config('site.antispam.honeypot_field') }}" tabindex="-1" autocomplete="off" value="">
</div>
<input type="hidden" name="_started" value="{{ \App\Http\Middleware\ProtectAgainstSpam::token() }}">
@error('form')
    <x-alert type="error" class="mb-6">{{ $message }}</x-alert>
@enderror
