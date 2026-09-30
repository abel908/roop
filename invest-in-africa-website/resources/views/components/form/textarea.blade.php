@props(['name', 'label', 'required' => false, 'help' => null, 'bag' => 'default', 'value' => null, 'maxlength' => null, 'id' => null])
@php
    $id ??= 'f-'.$name;
    $message = $errors->getBag($bag)->first($name);
@endphp
<div {{ $attributes->only('class') }} x-data="{ count: {{ mb_strlen((string) old($name, $value)) }} }">
    <label for="{{ $id }}" class="field-label">{{ $label }}@if($required)<span class="field-required" aria-hidden="true">*</span>@endif</label>
    <textarea id="{{ $id }}" name="{{ $name }}"
              {{ $attributes->except('class')->merge(['class' => 'field-input', 'rows' => 6]) }}
              @if($maxlength) maxlength="{{ $maxlength }}" @endif
              @if($required) required aria-required="true" @endif
              @if($message) aria-invalid="true" @endif
              aria-describedby="{{ $id }}-error{{ $help ? " $id-help" : '' }}"
              x-on:input="count = $el.value.length"
              x-on:blur="typeof checkField === 'function' && checkField($el)">{{ old($name, $value) }}</textarea>
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
            @if ($help)<p id="{{ $id }}-help" class="field-help">{{ $help }}</p>@endif
            <p id="{{ $id }}-error" class="field-error" @if(! $message) x-cloak x-show="errors && errors['{{ $name }}']" @endif>
                <x-glyph name="alert" :size="16" class="mt-0.5" />
                <span @if(! $message) x-text="errors && errors['{{ $name }}']" @endif>{{ $message }}</span>
            </p>
        </div>
        @if ($maxlength)
            <p class="mt-2 text-sm tabular-nums text-ink-500" aria-live="polite"><span x-text="count">0</span> / {{ $maxlength }}</p>
        @endif
    </div>
</div>
