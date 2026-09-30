@props(['bag' => 'default'])
{{-- Honeypot + filling-time token (§6.3) — no third-party captcha, works from China --}}
<div class="hp-field" aria-hidden="true">
    <label for="hp-{{ config('site.antispam.honeypot_field') }}">Website</label>
    <input id="hp-{{ config('site.antispam.honeypot_field') }}" type="text" name="{{ config('site.antispam.honeypot_field') }}" tabindex="-1" autocomplete="off" value="">
</div>
<input type="hidden" name="_started" value="{{ \App\Http\Middleware\ProtectAgainstSpam::token() }}">
@error('form')
    <x-alert type="error" class="mb-6">{{ $message }}</x-alert>
@enderror
@if (\App\Support\Captcha::required(request()))
    {{-- Self-hosted security question, shown only when traffic looks suspicious --}}
    <div class="border-l-4 border-brand-gold bg-ink-50 p-5">
        @php($captchaError = $errors->first('captcha') ?: $errors->getBag($bag)->first('captcha'))
        <label for="f-captcha-{{ $bag }}" class="field-label">
            {{ \App\Support\Captcha::question(request()) }}<span class="field-required" aria-hidden="true">*</span>
        </label>
        <p class="field-help !mt-0 mb-3" id="f-captcha-{{ $bag }}-help">{{ __('forms.captcha.help') }}</p>
        <input id="f-captcha-{{ $bag }}" name="captcha" type="text" inputmode="numeric" autocomplete="off" required
               class="field-input max-w-40" aria-describedby="f-captcha-{{ $bag }}-help" @if($captchaError) aria-invalid="true" @endif>
        @if ($captchaError)
            <p class="field-error"><x-glyph name="alert" :size="16" class="mt-0.5" />{{ $captchaError }}</p>
        @endif
    </div>
@endif
