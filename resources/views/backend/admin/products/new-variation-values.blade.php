<x-backend.inputs.select name="option_{{ $variation_id }}_choices[]" onchange="generateVariationCombinations()"
    data-placeholder="{{ translate('Select values here') }}" multiple data-selection-css-class="multi-select2">
    @foreach ($variation_values as $variation_value)
        <x-backend.inputs.select-option name="{{ $variation_value->collectTranslation('name') }}"
            value="{{ $variation_value->id }}" />
    @endforeach
</x-backend.inputs.select>
