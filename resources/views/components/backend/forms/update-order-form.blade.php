<form action="{{ route('admin.orders.update') }}" method="POST" class="update-order-form">
    @csrf
    <div class="space-y-4">
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <input type="hidden" name="warehouse_id" value="{{ $order->warehouse_id }}">
        <div class="group relative !mt-4" tabindex="0">
            <x-backend.inputs.search-input label="Search Products" class="update-order-product-search-input"
                name="" placeholder="Search product by name" value="" />

            <div
                class="update-order-product-search absolute z-[1] bg-background rounded-md right-0 top-[calc(100%)] w-full min-w-[300px] min-h-[150px] shadow max-h-[350px] overflow-y-auto p-[2px] divide-y divide-[#78787829] hidden group-focus-within:block transition-all duration-300">

                <div class="block py-4 px-6 bg-theme-primary/[.03] text-muted font-bold">
                    <span class="uppercase">{{ translate('Search Results') }}</span>
                </div>

                {{-- search result here --}}
                <div class="divide-y divide-[#78787829] update-order-search-results">
                    @include('backend.admin.orders.product-search-results', [
                        'productVariations' => [],
                    ])
                </div>
            </div>
        </div>

        {{-- start:order list --}}
        <div class="!mt-4">
            <label class="theme-input-label !pt-0 input-required">
                {{ translate('Products') }}
            </label>
            @include('backend.admin.orders.update-order-table', [
                'orders' => collect(),
                'returnOrder' => null,
            ])
        </div>
        {{-- end:order list --}}

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Update Order" type="submit" />
        </div>
    </div>
</form>
