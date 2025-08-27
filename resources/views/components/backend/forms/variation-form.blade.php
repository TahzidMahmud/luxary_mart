<form action="{{ $variation ? route('admin.variations.update', $variation->id) : route('admin.variations.store') }}"
    method="POST">
    @csrf

    @if ($variation)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type variation name"
            value="{{ $variation?->collectTranslation('name', $langKey) }}" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Variation" type="submit" />
        </div>
    </div>
</form>
