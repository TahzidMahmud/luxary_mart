<form
    action="{{ $variationValue ? route('admin.variation-values.update', ['id' => $variationValue->id]) : route('admin.variation-values.store', ['variation_id' => $variation->id]) }}"
    method="POST">
    @csrf

    @if ($variationValue)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type value name"
            value="{{ $variationValue?->collectTranslation('name', $langKey) }}" />

        @if ($variation->id == colorVariationId())
            {{-- <x-backend.inputs.color label="Color Code" name="color_code" value="{{ $variationValue?->color_code }}"
                :isRequired="false" /> --}}

            <x-backend.inputs.file label="Image" name="thumbnail_image"
                value="{{ $variationValue?->thumbnail_image }}" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Value" type="submit" />
        </div>
    </div>
</form>
