{{-- Consent banner: measurement tools only after consent (§10.5, §11.2) --}}
<div x-data="cookieConsent" x-cloak x-show="visible"
     x-transition:enter="transition duration-400 ease-out" x-transition:enter-start="opacity-0 translate-y-6"
     x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="opacity-0 translate-y-6"
     class="fixed inset-x-0 bottom-0 z-[70] p-3 sm:p-5" role="dialog" aria-live="polite" aria-labelledby="cookie-title">
    <div class="mx-auto max-w-4xl bg-ink p-6 text-paper shadow-[0_30px_60px_-20px_rgba(0,0,0,.6)] md:p-8 on-dark">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-xl">
                <p id="cookie-title" class="font-display text-lg font-bold text-brand-gold">{{ __('site.cookies.title') }}</p>
                <p class="mt-2 text-[0.9375rem] text-ink-200">
                    {{ __('site.cookies.text') }}
                    <a href="{{ lroute('cookies') }}" class="link">{{ __('site.cookies.policy') }}</a>
                </p>
                <div x-show="settings" x-collapse class="mt-5 space-y-3 text-[0.9375rem]">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" checked disabled class="checkbox opacity-60">
                        <span><strong class="text-paper">{{ __('site.cookies.necessary') }}</strong><span class="block text-ink-300">{{ __('site.cookies.necessary_text') }}</span></span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" x-model="analyticsChecked" class="checkbox">
                        <span><strong class="text-paper">{{ __('site.cookies.analytics') }}</strong><span class="block text-ink-300">{{ __('site.cookies.analytics_text') }}</span></span>
                    </label>
                </div>
            </div>
            <div class="flex shrink-0 flex-col gap-2 sm:flex-row md:flex-col">
                <button type="button" class="btn btn-gold btn-sm" x-on:click="acceptAll()">{{ __('site.cookies.accept') }}</button>
                <button type="button" class="btn btn-outline btn-sm" x-on:click="rejectAll()">{{ __('site.cookies.reject') }}</button>
                <button type="button" class="btn btn-sm text-ink-200 underline underline-offset-4 hover:text-paper" x-show="!settings" x-on:click="settings = true">{{ __('site.cookies.customize') }}</button>
                <button type="button" class="btn btn-outline btn-sm" x-show="settings" x-on:click="saveChoice()">{{ __('site.cookies.save') }}</button>
            </div>
        </div>
    </div>
</div>
