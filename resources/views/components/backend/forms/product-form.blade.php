<form
    action="{{ $product ? route(routePrefix() . '.products.update', $product->id) : route(routePrefix() . '.products.store') }}"
    method="POST" id="product-form">
    @csrf
    @if ($product)
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="grid md:grid-cols-12 gap-3">
        <div class="md:col-span-8 md:row-span-2">
            {{-- Name & Slug --}}
            <div class="card">
                <div class="card__title flex justify-between items-center">
                    {{ translate('General Informations') }}
                    @if ($product)
                        <x-backend.inputs.change-languages class="!text-sm !font-normal"
                            langKey="{{ $langKey }}" />
                    @endif
                </div>

                <div class="card__content">
                    <div class="space-y-3">
                        <x-backend.inputs.text label="Product Name" name="name" placeholder="Type product name"
                            value="{{ $product?->collectTranslation('name', $langKey) }}" />

                        <div class="unit-tree-data">
                            @include('components.backend.inc.products.unit-list', [
                                'product' => $product,
                                'units' => $units,
                                'selectedUnit' => null,
                            ])
                        </div>


                        @if (user()->user_type != 'seller')
                            @can('create_units')
                                <div class="text-end !my-3">
                                    <a href="javascript:void(0);" class="text-theme-secondary-light unit-create-modal"
                                        data-micromodal-trigger="unit-create-modal">
                                        {{ translate('Add New Unit') }}
                                        <span class="ml-2">
                                            <i class="fa-solid fa-plus"></i>
                                        </span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (!$product || ($product && $langKey == config('app.default_language')))
                            <x-backend.inputs.number label="Min Purchase Quantity" name="min_purchase_qty"
                                placeholder="Type minimum purchase quantity"
                                value="{{ $product?->min_purchase_qty ?? 1 }}" min="1" />

                            <x-backend.inputs.number label="Max Purchase Quantity" name="max_purchase_qty"
                                placeholder="Type maximum purchase quantity"
                                value="{{ $product?->max_purchase_qty ?? 1 }}" min="1" />
                        @endif
                    </div>
                </div>
            </div>

            @if (!$product || ($product && $langKey == config('app.default_language')))
                {{-- Images --}}
                <div class="card mt-3">
                    <div class="card__title">
                        {{ translate('Product Images') }}
                    </div>
                    <div class="card__content">
                        <div class="space-y-3">
                            <x-backend.inputs.file label="Product Image" name="thumbnail_image"
                                value="{{ $product?->thumbnail_image }}"
                                filesHint="This image will be used as thumbnail image of product. Recommended size: 512*512" />

                            <x-backend.inputs.file :multiple="true" label="Gallery Images" name="gallery_images"
                                value="{{ $product?->gallery_images }}"
                                filesHint="These images will be used as slider images of product in details page. Recommended size: 512*512" />

                            <x-backend.inputs.file :multiple="true" label="Real Images" name="real_pictures"
                                value="{{ $product?->real_pictures }}" />
                        </div>
                    </div>
                </div>

                {{-- Product Price & Stock --}}
                <div class="card mt-3">
                    <div class="card__title">
                        <div class="flex items-center justify-between">
                            <span>{{ translate('Product Price & Stock') }}</span>
                            <span>
                                <span class="flex items-center">
                                    <label for="is_variant" class="cursor-pointer">
                                        {{ translate('Has Variations?') }}
                                    </label>
                                    <span class="ms-3">
                                        <x-backend.inputs.checkbox toggler="true" id="is_variant" name="is_variant"
                                            onchange="isVariantProduct(this)" :isChecked="$product?->has_variation" />
                                    </span>
                                </span>

                            </span>
                        </div>
                    </div>

                    <div class="card__content">
                        {{-- no variation --}}
                        <div class="noVariation space-y-3"
                            @if ($product?->has_variation) style="display:none;" @endif>

                            @php
                                $price = 0;
                                $stock_qty = 0;
                                $sku = null;
                                $code = null;
                                $discount_value = 0;
                                $discount_type = 'amount';

                                if ($product) {
                                    $firstVariation = $product->variations()->first();
                                    if ($firstVariation) {
                                        $price = $firstVariation->price;
                                        $discount_value = $firstVariation->discount_value;
                                        $discount_type = $firstVariation->discount_type;

                                        $defaultWarehouse = shop()->warehouses()->where('is_default', 1)->first();
                                        if ($defaultWarehouse) {
                                            $stock = $firstVariation
                                                ->productVariationStocks()
                                                ->where('warehouse_id', $defaultWarehouse->id)
                                                ->first();
                                            if ($stock) {
                                                $stock_qty = $stock->stock_qty;
                                            }
                                        }

                                        $sku = $firstVariation->sku;
                                        $code = $firstVariation->code;
                                    }
                                }
                            @endphp

                            <x-backend.inputs.number label="Price" name="price" placeholder="Type price of product"
                                value="{{ $price }}" min="0" step="0.001" />

                            <x-backend.inputs.text label="Sku" name="sku" placeholder="Type sku of product"
                                value="{{ $sku }}" />

                            @if (!useInventory())
                                <x-backend.inputs.number label="Stock" name="stock_qty"
                                    placeholder="Type stock of product" value="{{ $stock_qty }}" min="0" />
                            @endif

                            <x-backend.inputs.number label="Discount Value" name="discount_value" min="0"
                                step="0.001" :isRequired="false" value="{{ $discount_value }}" />

                            <x-backend.inputs.select label="Discount Type" name="discount_type" data-search="false"
                                :isRequired="false">
                                <x-backend.inputs.select-option name="{{ translate('Amount') }}" value="amount"
                                    selected="{{ $discount_type }}" />
                                <x-backend.inputs.select-option name="{{ translate('Percentage') }}" value="percentage"
                                    selected="{{ $discount_type }}" />
                            </x-backend.inputs.select>
                        </div>

                        {{-- has variations --}}
                        <div class="hasVariation space-y-3"
                            @if ($product && !$product->has_variation) style="display:none;" @elseif(!$product) style="display:none;" @endif>
                            {{-- start:variations --}}
                            <div class="chosen_variation_options">
                                @if (!$product || !$product?->has_variation)
                                    <div class="grid md:grid-cols-2 gap-3">
                                        <div class="md:col-span-1">
                                            <div class="flex items-center">
                                                <div class="w-full">{{ translate('Attribute') }} <span
                                                        class="attribute-counter">1</span></div>
                                                <x-backend.inputs.select groupClass="w-full ms-4"
                                                    name="chosen_variations[]" onchange="getVariationValues(this)"
                                                    class="chosen_variations" :isRequired="false" data-search="false">
                                                    <option value="">{{ translate('Select Variation') }}
                                                        @foreach ($variations as $variation)
                                                            <x-backend.inputs.select-option
                                                                name="{{ $variation->collectTranslation('name') }}"
                                                                value="{{ $variation->id }}" />
                                                        @endforeach
                                                </x-backend.inputs.select>
                                            </div>
                                        </div>

                                        <div class="md:col-span-1">
                                            <div class="flex items-center">
                                                <div class="variationvalues w-full">
                                                    <x-backend.inputs.text name=""
                                                        placeholder="Select variation values" :disabled="true" />
                                                </div>
                                                <button class="button button-default" type="button"
                                                    onclick="addAnotherVariation()">
                                                    <span class="footable-toggle fooicon fooicon-plus"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @foreach (generateVariationCombinations($product->variationCombinations()->get()) as $key => $combination)
                                        <div class="grid md:grid-cols-2 gap-3 mb-3">
                                            <div class="md:col-span-1">
                                                <div class="flex items-center">
                                                    <div class="w-full">{{ translate('Attribute') }} <span
                                                            class="attribute-counter">{{ $key + 1 }}</span></div>

                                                    <input type="hidden" name="chosen_variations[]"
                                                        value="{{ $combination['id'] }}">
                                                    <x-backend.inputs.text class="ms-4" name=""
                                                        value="{{ $combination['name'] }}" :disabled="true" />
                                                </div>
                                            </div>

                                            <div class="md:col-span-1">
                                                <div class="flex items-center">
                                                    <div class="variationvalues w-full">
                                                        @php
                                                            $variation_values = \App\Models\VariationValue::where(
                                                                'variation_id',
                                                                $combination['id'],
                                                            )->get();
                                                            $old_val = array_map(function ($val) {
                                                                return $val['id'];
                                                            }, $combination['values']);

                                                        @endphp

                                                        <x-backend.inputs.select
                                                            name="option_{{ $combination['id'] }}_choices[]"
                                                            onchange="generateVariationCombinations()"
                                                            data-placeholder="{{ translate('Select values here') }}"
                                                            multiple data-selection-css-class="multi-select2">
                                                            @foreach ($variation_values as $variation_value)
                                                                <x-backend.inputs.select-option
                                                                    name="{{ $variation_value->collectTranslation('name') }}"
                                                                    value="{{ $variation_value->id }}"
                                                                    selected="{{ in_array($variation_value->id, $old_val) ? $variation_value->id : '' }}" />
                                                            @endforeach
                                                        </x-backend.inputs.select>

                                                    </div>
                                                    @if ($key == 0)
                                                        <button class="button button-default" type="button"
                                                            onclick="addAnotherVariation()">
                                                            <span class="footable-toggle fooicon fooicon-plus"></span>
                                                        </button>
                                                    @else
                                                        <button class="button button-default" type="button"
                                                            onclick="removeVariation(this)">
                                                            <span class="footable-toggle fooicon fooicon-minus"></span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            {{-- end:variations --}}

                            <div class="variation_combination" id="variation_combination">
                                {{-- combinations will be added here via ajax response --}}
                                @if ($product && $product->has_variation)
                                    @include('backend.admin.products.update-variation-combinations', [
                                        'combinations' => $product->variations,
                                    ])
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 space-y-3">
                            @php
                                $start_date = date('m/d/Y');
                                $end_date = date('m/d/Y');

                                if ($product) {
                                    $start_date = $product->discount_start_date
                                        ? date('m/d/Y', $product->discount_start_date)
                                        : date('m/d/Y');
                                    $end_date = $product->discount_end_date
                                        ? date('m/d/Y', $product->discount_end_date)
                                        : date('m/d/Y');
                                }
                            @endphp

                            <x-backend.inputs.datepicker placeholder="Start date - End date" label="Discount Schedule"
                                name="date_range" :isRequired="false" type="range"
                                value="{{ $start_date . ' - ' . $end_date }}" />
                        </div>
                    </div>
                </div>

            @endif
            {{-- Description --}}
            <div class="card mt-3">
                <div class="card__title">
                    {{ translate('Product Description') }}
                </div>

                <div class="card__content">
                    <div class="space-y-3">
                        <x-backend.inputs.textarea rich="true" name="description"
                            placeholder="Type description of product"
                            value="{{ $product?->collectTranslation('description', $langKey) }}" :isRequired="false"
                            aiGenerate="true" />
                    </div>
                </div>
            </div>

            @if (!$product || ($product && $langKey == config('app.default_language')))
                {{-- SEO --}}
                <div class="card mt-3">
                    <div class="card__title">
                        {{ translate('Product SEO Informations') }}
                    </div>

                    <div class="card__content">
                        <div class="space-y-3">
                            <x-backend.inputs.seo metaTitle="{{ $product?->meta_title }}"
                                metaDescription="{{ $product?->meta_description }}"
                                metaKeywords="{{ $product?->meta_keywords }}"
                                metaImage="{{ $product?->meta_image }}" />
                        </div>
                    </div>
                </div>
            @endif

            {{-- submit button --}}
            <div class="justify-end mt-6 hidden md:flex">
                <x-backend.inputs.button buttonText="Save Product" type="submit" />
            </div>
        </div>

        {{-- right bar --}}
        @if (!$product || ($product && $langKey == config('app.default_language')))
            <div class="md:col-span-4 space-y-3">
                {{-- Status --}}
                <div class="card">
                    <div class="card__title">
                        {{ translate('Product Status') }}
                    </div>
                    <div class="card__content">
                        <div class="space-y-5">
                            @if ($product && $langKey == config('app.default_language'))
                                <div>
                                    <label class="mb-2">
                                        {{ translate('Slug') }}
                                        <span class="text-muted ml-2">{{ url('/products') }}/</span>
                                    </label>
                                    <x-backend.inputs.text name="slug" placeholder="Type custom slug"
                                        value="{{ $product?->slug }}" />
                                </div>


                                <a href="{{ url('/products') }}/{{ $product?->slug }}" target="_blank">
                                    {{ translate('Visit Product') }}
                                    <span class="ml-2 text-theme-secondary-light">
                                        <i class="fa-solid fa-eye"></i>
                                    </span>
                                </a>
                            @endif
                            <div class="flex items-center justify-between">
                                <p>{{ translate('Published') }}</p>
                                <x-backend.inputs.checkbox toggler="true" name="is_published" value="1"
                                    :isChecked="$product?->is_published" />
                            </div>

                            @if ($product)
                                <div class="flex justify-between gap-4">
                                    <span>{{ translate('Date Created') }}</span>
                                    <span
                                        class="text-muted">{{ date('d M, Y', strtotime($product->created_at)) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="card">
                    <div class="card__title">
                        {{ translate('Product Categories') }}
                    </div>
                    <div class="card__content">
                        @php
                            $productCategories = $product ? $product->categories()->pluck('category_id') : collect();
                        @endphp

                        <ul class="max-h-[315px] overflow-y-auto cateogry-tree">
                            <div class="category-tree-data space-y-5">
                                @include('components.backend.inc.products.category-list', [
                                    'categories' => $categories,
                                    'productCategories' => $productCategories,
                                    'selectedCategories' => [],
                                    'langKey' => $langKey,
                                ])
                            </div>
                        </ul>
                        @if (user()->user_type != 'seller')
                            @can('create_categories')
                                <a href="javascript:void(0);"
                                    class="text-theme-secondary-light !mt-7 category-create-modal"
                                    data-micromodal-trigger="category-create-modal">
                                    {{ translate('Add New Category') }}
                                    <span class="ml-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>

                {{-- Brand --}}
                <div class="card">
                    <div class="card__title">
                        {{ translate('Product Brand') }}
                    </div>
                    <div class="card__content">
                        <ul class="space-y-5 max-h-[315px] overflow-y-auto brand-tree-data">

                            @include('components.backend.inc.products.brand-list', [
                                'product' => $product,
                                'langKey' => $langKey,
                                'selectedBrand' => null,
                            ])
                        </ul>

                        @if (user()->user_type != 'seller')
                            @can('create_brands')
                                <a href="javascript:void(0);" class="text-theme-secondary-light !mt-4 brand-create-modal"
                                    data-micromodal-trigger="brand-create-modal">
                                    {{ translate('Add New Brand') }}
                                    <span class="ml-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>

                {{-- Tags and Badges --}}
                <div class="card">
                    <div class="card__title">
                        {{ translate('Tags and Badges') }}
                    </div>

                    <div class="card__content">
                        <div class="space-y-8">
                            <div>
                                @php
                                    $productTags = collect();
                                    if ($product) {
                                        $productTags = $product->tags()->pluck('tag_id');
                                    }
                                @endphp
                                <div class="mb-2 flex justify-between">
                                    <span>{{ translate('Tags') }}</span>

                                    @if (user()->user_type != 'seller')
                                        @can('create_tags')
                                            <a href="javascript:void(0);"
                                                class="text-theme-secondary-light tag-create-modal"
                                                data-micromodal-trigger="tag-create-modal">

                                                <span class="mr-1">
                                                    <i class="fa-solid fa-plus"></i>
                                                </span>
                                                {{ translate('Add New Tag') }}
                                            </a>
                                        @endcan
                                    @endif
                                </div>

                                <div class="tag-tree-data">
                                    @include('components.backend.inc.products.tag-list', [
                                        'tags' => $tags,
                                        'productTags' => $productTags,
                                    ])
                                </div>
                            </div>

                            <div>
                                <div class="mb-2 flex justify-between">
                                    <span>{{ translate('Badges') }}</span>

                                    @if (user()->user_type != 'seller')
                                        @can('create_badges')
                                            <a href="javascript:void(0);"
                                                class="text-theme-secondary-light badge-create-modal"
                                                data-micromodal-trigger="badge-create-modal">

                                                <span class="mr-1">
                                                    <i class="fa-solid fa-plus"></i>
                                                </span>
                                                {{ translate('Add New Badge') }}
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                                @php
                                    $productBadges = collect();
                                    if ($product) {
                                        $productBadges = $product->badges()->pluck('badge_id');
                                    }
                                @endphp

                                <div class="badge-tree-data">
                                    @include('components.backend.inc.products.badge-list', [
                                        'badges' => $badges,
                                        'productBadges' => $productBadges,
                                    ])
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Taxes --}}
                <div class="card">
                    <div class="card__title flex items-center justify-between">
                        <span>
                            {{ translate('Product Taxes') }}
                        </span>

                        <small class="muted">
                            ({{ translate('Default 0%') }})
                        </small>
                    </div>
                    <div class="card__content">
                        @foreach ($taxes as $tax)
                            @php
                                $taxValue = 0;
                                $taxType = 'amount';
                                if ($product && $product->taxes()->count() > 0) {
                                    $productTax = $product->taxes()->firstWhere('tax_id', $tax->id);
                                    $taxValue = $productTax->tax_value;
                                    $taxType = $productTax->tax_type;
                                }
                            @endphp
                            <input type="hidden" value="{{ $tax->id }}" name="tax_ids[]">
                            <div class="mb-4">
                                <label for="" class="theme-input-label !pt-0">
                                    {{ $tax->name }}
                                </label>
                                <div class="grid md:grid-cols-2 gap-3">
                                    <div class="md:col-span-1">
                                        <x-backend.inputs.number name="tax_values[]" min="0" step="0.001"
                                            value="{{ $taxValue }}" />
                                    </div>
                                    <div class="md:col-span-1">
                                        <x-backend.inputs.select name="tax_types[]" class="">
                                            <x-backend.inputs.select-option name="{{ translate('Amount') }}"
                                                value="amount" selected="{{ $taxType }}" />
                                            <x-backend.inputs.select-option name="{{ translate('Percentage') }}"
                                                value="percentage" selected="{{ $taxType }}" />
                                        </x-backend.inputs.select>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card">
                    <div class="card__title flex items-center justify-between">
                        <span>
                            {{ translate('Other Informations') }}
                        </span>
                    </div>
                    <div class="card__content">
                        <div class="space-y-8">

                            {{-- commission_rate --}}
                            <div>
                                <x-backend.inputs.number :labelInline="false" label="Commission Rate"
                                    name="commission_rate" placeholder="Type commission %"
                                    value="{{ $product?->commission_rate }}" :isRequired="false" step="0.001" />
                            </div>

                            {{-- est_delivery_time --}}
                            <div>
                                <x-backend.inputs.text :labelInline="false" label="Estimated Delivery Time"
                                    name="est_delivery_time" placeholder="Type etimated delivery hours"
                                    value="{{ $product?->est_delivery_time }}" :isRequired="false" />
                            </div>

                            {{-- emi --}}
                            <div class="flex items-center justify-between">
                                <p>{{ translate('EMI Available') }}</p>
                                <x-backend.inputs.checkbox toggler="true" name="has_emi" value="1"
                                    :isChecked="$product?->has_emi" />
                            </div>

                            <div>
                                <x-backend.inputs.text :labelInline="false" label="Emi Info" name="emi_info"
                                    placeholder="Type EMI info if available" value="{{ $product?->emi_info }}"
                                    :isRequired="false" />
                            </div>

                            {{-- warrenty --}}
                            <div class="flex items-center justify-between">
                                <p>{{ translate('Warrenty Available') }}</p>
                                <x-backend.inputs.checkbox toggler="true" name="has_warranty" value="1"
                                    :isChecked="$product?->has_warranty" />
                            </div>

                            <div>
                                <label class="mb-2" for="warranty_info">{{ translate('Warranty Info') }}</label>
                                <x-backend.inputs.text name="warranty_info" placeholder="Type Warranty info"
                                    value="{{ $product?->warranty_info }}" :isRequired="false" />
                            </div>

                            {{-- alert_qty --}}
                            <div>
                                <x-backend.inputs.number :labelInline="false" label="Alert Quantity" name="alert_qty"
                                    placeholder="Type alert quantity" value="{{ $product?->alert_qty ?? 0 }}"
                                    min="0" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    {{-- submit button --}}
    <div class="flex justify-end mt-6 md:hidden">
        <x-backend.inputs.button buttonText="Save Product" type="submit" />
    </div>
</form>
