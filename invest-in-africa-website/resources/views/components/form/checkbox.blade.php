@props(['name', 'required' => false, 'bag' => 'default', 'id' => null])
@php
    $id ??= 'f-'.$name;
    $message = $errors->getBag($bag)->first($name);
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="flex cursor-pointer items-start gap-3 text-[0.9375rem] text-ink-700">
        <input id="{{ $id }}" type="checkbox" name="{{ $name }}" value="1" class="checkbox" @checked(old($name))
               @if($required) required aria-required="true" @endif
               @if($message) aria-invalid="true" @endif
               aria-describedby="{{ $id }}-error"
               x-on:change="typeof checkField === 'function' && checkField($el)">
        <span>{{ $slot }}@if($required)<span class="field-required" aria-hidden="true">*</span>@endif</span>
    </label>
    <p id="{{ $id }}-error" class="field-error pl-8" @if(! $message) x-cloak x-show="errors && errors['{{ $name }}']" @endif>
        <x-glyph name="alert" :size="16" class="mt-0.5" />
        <span @if(! $message) x-text="errors && errors['{{ $name }}']" @endif>{{ $message }}</span>
    </p>
</div>
