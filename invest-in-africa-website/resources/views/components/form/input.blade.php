@props(['name', 'label', 'type' => 'text', 'required' => false, 'help' => null, 'bag' => 'default', 'value' => null, 'id' => null])
@php
    $id ??= 'f-'.str_replace(['[', ']', '.'], '-', $name);
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $message = $errors->getBag($bag)->first($errorKey);
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="field-label">{{ $label }}@if($required)<span class="field-required" aria-hidden="true">*</span>@endif</label>
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($errorKey, $value) }}"
           {{ $attributes->except('class')->merge(['class' => 'field-input']) }}
           @if($required) required aria-required="true" @endif
           @if($message) aria-invalid="true" @endif
           aria-describedby="{{ $id }}-error{{ $help ? " $id-help" : '' }}"
           x-on:blur="typeof checkField === 'function' && checkField($el)">
    @if ($help)<p id="{{ $id }}-help" class="field-help">{{ $help }}</p>@endif
    <p id="{{ $id }}-error" class="field-error" @if(! $message) x-cloak x-show="errors && errors['{{ $name }}']" @endif>
        <x-glyph name="alert" :size="16" class="mt-0.5" />
        <span @if(! $message) x-text="errors && errors['{{ $name }}']" @endif>{{ $message }}</span>
    </p>
</div>
