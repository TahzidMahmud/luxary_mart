<div
    class="input-select2 {{ $labelInline ? 'theme-input-group' : '' }} {{ !$label ? 'no-label' : '' }} {{ $groupClass }}">
    @if ($label)
        <label for="{{ $name }}"
            class="theme-input-label {{ $labelInline ? '' : 'pt-0' }} {{ $isRequired ? 'input-required' : '' }}">{{ translate($label) }}</label>
    @endif

    <div class="theme-input-wrapper {{ $wrapperClass }}">
        <select {{ $attributes->merge(['class' => $type . ' theme-input']) }} name="{{ $name }}" @required($isRequired)
            @if ($isDisabled) disabled @endif>
            {{ $slot }}
        </select>
    </div>
</div>
