@props(['name', 'label', 'options' => [], 'required' => false, 'help' => null, 'bag' => 'default', 'value' => null, 'placeholder' => null, 'id' => null])
@php
    $id ??= 'f-'.$name;
    $message = $errors->getBag($bag)->first($name);
    $selected = (string) old($name, $value);
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="field-label">{{ $label }}@if($required)<span class="field-required" aria-hidden="true">*</span>@endif</label>
    <select id="{{ $id }}" name="{{ $name }}"
            {{ $attributes->except('class')->merge(['class' => 'field-input']) }}
            @if($required) required aria-required="true" @endif
            @if($message) aria-invalid="true" @endif
            aria-describedby="{{ $id }}-error{{ $help ? " $id-help" : '' }}"
            x-on:change="typeof checkField === 'function' && checkField($el)">
        <option value="">{{ $placeholder ?? __('forms.select') }}</option>
        {{ $slot }}
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($help)<p id="{{ $id }}-help" class="field-help">{{ $help }}</p>@endif
    <p id="{{ $id }}-error" class="field-error" @if(! $message) x-cloak x-show="errors && errors['{{ $name }}']" @endif>
        <x-glyph name="alert" :size="16" class="mt-0.5" />
        <span @if(! $message) x-text="errors && errors['{{ $name }}']" @endif>{{ $message }}</span>
    </p>
</div>
