@php
    $categories = \App\Models\Category::where('parent_id', 0)
        ->orderBy('sorting_order_level', 'desc')
        ->with('childrenCategories')
        ->get();
    // $products = \App\Models\Product::shop()->isPublished()->get();
    $products = \App\Models\Product::isPublished()->latest()->get();
    $shops = \App\Models\Shop::isApproved()->isPublished()->get();
    $brands = \App\Models\Brand::isActive()->get();
    $campaigns = \App\Models\Campaign::isPublished()
        ->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))
        ->latest()
        ->get();
    $tags = \App\Models\Tag::get();
@endphp
<div class="init-accordion accordion-container border-collapse">
    {{-- slider --}}
    <div class="ac !mt-0 ">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Slider Section') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="types[]" value="homeSliderImages">
                    <input type="hidden" name="types[]" value="homeSliderLinks">

                    <div class="slider-input-wrapper">
                        @if (getSetting('homeSliderImages') != null)
                            @foreach (json_decode(getSetting('homeSliderImages'), true) as $key => $value)
                                <div class="slider-input">
                                    <x-backend.inputs.file :labelInline="false" :trashBtn="true"
                                        trashParent=".slider-input" label="Image" name="homeSliderImages[]"
                                        value="{{ $value }}" class="mb-3" />
                                    <x-backend.inputs.text :labelInline="false" label="Link" name="homeSliderLinks[]"
                                        value="{{ json_decode(getSetting('homeSliderLinks'), true)[$key] }}"
                                        placeholder="https://www.example.com" :isRequired="false" />
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <a href="javascript:void(0);" class="text-theme-secondary-light" data-toggle="add-more"
                        data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="homeSliderImages[]" value=""  class="mb-3" />
                                            <x-backend.inputs.text :labelInline="false" label="Link" name="homeSliderLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                    </div>'
                        data-target=".slider-input-wrapper">
                        {{ translate('Add Slider') }}
                        <span class="ml-2">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </a>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- featured category --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Featured Categories') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $featuredCategories =
                            getSetting('homeFeaturedCategories') != null
                                ? collect(json_decode(getSetting('homeFeaturedCategories')))
                                : collect();
                    @endphp
                    <div class="pt-2">

                        <input type="hidden" name="types[]" value="showHomeFeaturedCategories">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Featured Categories') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeFeaturedCategories" value="1"
                                isChecked="{{ getSetting('showHomeFeaturedCategories') ?? false }}" />
                        </div>


                        <input type="hidden" name="types[]" value="homeFeaturedCategories">
                        <x-backend.inputs.select :labelInline="false" label="Featured Categories"
                            name="homeFeaturedCategories[]" multiple data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select categories') }}" :isRequired="false">

                            @foreach ($categories as $cat)
                                <x-backend.inputs.select-option name="{{ $cat->collectTranslation('name', $langKey) }}"
                                    value="{{ $cat->id }}"
                                    selected="{{ $featuredCategories->contains($cat->id) ? $cat->id : 0 }}" />

                                @foreach ($cat->childrenCategories as $childCategory)
                                    @include('backend.admin.categories.subCategory', [
                                        'subCategory' => $childCategory,
                                        'langKey' => $langKey,
                                        'category' => null,
                                        'featuredCategories' => $featuredCategories,
                                    ])
                                @endforeach
                            @endforeach
                        </x-backend.inputs.select>

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Home Videos Section --}}
    <div class="ac !mt-0 ">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Home Videos Section') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="types[]" value="homeVideoIds">

                    <div class="video-input-wrapper">
                        @if (getSetting('homeVideoIds') != null)
                            @foreach (json_decode(getSetting('homeVideoIds'), true) as $key => $value)
                                <div class="video-input">
                                    <x-backend.inputs.text :labelInline="false" label="Video ID" name="homeVideoIds[]"
                                        value="{{ $value }}" placeholder="82KKYI2jAwo" :isRequired="false" />
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <a href="javascript:void(0);" class="text-theme-secondary-light" data-toggle="add-more"
                        data-content='<div class="video-input mt-3">
                                                <x-backend.inputs.text :labelInline="false" label="Video ID" name="homeVideoIds[]"
                                                value=""
                                                placeholder="82KKYI2jAwo" :isRequired="false" />
                                        </div>'
                        data-target=".video-input-wrapper">
                        {{ translate('Add Video') }}
                        <span class="ml-2">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </a>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- featured products --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Featured Products') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $featuredProducts =
                            getSetting('homeFeaturedProducts') != null
                                ? collect(json_decode(getSetting('homeFeaturedProducts')))
                                : collect();
                    @endphp
                    <div class="pt-2">

                        <input type="hidden" name="types[]" value="showHomeFeaturedProducts">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Featured Products') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeFeaturedProducts" value="1"
                                isChecked="{{ getSetting('showHomeFeaturedProducts') ?? false }}" />
                        </div>


                        <input type="hidden" name="types[]" value="homeFeaturedProducts">
                        <x-backend.inputs.select :labelInline="false" label="Featured Products"
                            name="homeFeaturedProducts[]" multiple data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select products') }}" :isRequired="false">

                            @foreach ($products as $product)
                                <x-backend.inputs.select-option
                                    name="{{ $product->collectTranslation('name', $langKey) }}"
                                    value="{{ $product->id }}"
                                    selected="{{ $featuredProducts->contains($product->id) ? $product->id : 0 }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <input type="hidden" name="types[]" value="homeFeaturedProductBanner">
                        <x-backend.inputs.file :labelInline="false" label="Image" name="homeFeaturedProductBanner"
                            value="" class="mb-3" />

                        <input type="hidden" name="types[]" value="homeFeaturedProductLink">
                        <x-backend.inputs.text :labelInline="false" label="Link" name="homeFeaturedProductLink"
                            value="" placeholder="https://www.example.com" :isRequired="false" />

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- flash sales --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Flash Sale') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="pt-2">
                        <input type="hidden" name="types[]" value="showHomeFlashSale">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Flash Sale') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeFlashSale" value="1"
                                isChecked="{{ getSetting('showHomeFlashSale') ?? false }}" />
                        </div>

                        <input type="hidden" name="types[]" value="homeFlashSale">
                        <x-backend.inputs.select :labelInline="false" label="Flash Sale" name="homeFlashSale"
                            data-placeholder="{{ translate('Select flash sale campaign') }}" :isRequired="false">

                            @foreach ($campaigns as $campaign)
                                <x-backend.inputs.select-option name="{{ $campaign->name }}"
                                    value="{{ $campaign->id }}" selected="{{ getSetting('homeFlashSale') }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- product section one --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Product Section One') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $homeProductSectionOne =
                            getSetting('homeProductSectionOne') != null
                                ? collect(json_decode(getSetting('homeProductSectionOne')))
                                : collect();
                    @endphp
                    <div class="pt-2">
                        <input type="hidden" name="types[]" value="showHomeProductSectionOne">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Product Section One') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeProductSectionOne" value="1"
                                isChecked="{{ getSetting('showHomeProductSectionOne') ?? false }}" />
                        </div>


                        <input type="hidden" name="types[]" value="homeProductSectionOne">
                        <x-backend.inputs.select :labelInline="false" label="Products" name="homeProductSectionOne[]"
                            multiple data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select products') }}" :isRequired="false">

                            @foreach ($products as $product)
                                <x-backend.inputs.select-option
                                    name="{{ $product->collectTranslation('name', $langKey) }}"
                                    value="{{ $product->id }}"
                                    selected="{{ $homeProductSectionOne->contains($product->id) ? $product->id : 0 }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeProductSectionOneTitle">
                        <x-backend.inputs.text :labelInline="false" label="Section Title"
                            name="homeProductSectionOneTitle"
                            value="{{ getSetting('homeProductSectionOneTitle') ?? '' }}"
                            placeholder="Type section title" :isRequired="false" />

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- full width banner --}}
    <div class="ac !mt-0 ">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Full Width Banner') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="types[]" value="homeFullWidthBanners">
                    <input type="hidden" name="types[]" value="homeFullWidthBannerLinks">

                    <div class="slider-input-wrapper">
                        @if (getSetting('homeFullWidthBanners') != null)
                            @foreach (json_decode(getSetting('homeFullWidthBanners'), true) as $key => $value)
                                <div class="slider-input">
                                    <x-backend.inputs.file :labelInline="false" :trashBtn="true"
                                        trashParent=".slider-input" label="Image" name="homeFullWidthBanners[]"
                                        value="{{ $value }}" class="mb-3" />
                                    <x-backend.inputs.text :labelInline="false" label="Link"
                                        name="homeFullWidthBannerLinks[]"
                                        value="{{ json_decode(getSetting('homeFullWidthBannerLinks'), true)[$key] }}"
                                        placeholder="https://www.example.com" :isRequired="false" />
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <a href="javascript:void(0);" class="text-theme-secondary-light" data-toggle="add-more"
                        data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="homeFullWidthBanners[]" value=""  class="mb-3" />
                                            <x-backend.inputs.text :labelInline="false" label="Link" name="homeFullWidthBannerLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                    </div>'
                        data-target=".slider-input-wrapper">
                        {{ translate('Add Banner') }}
                        <span class="ml-2">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </a>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- product section two --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Product Section Two') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $homeProductSectionTwo =
                            getSetting('homeProductSectionTwo') != null
                                ? collect(json_decode(getSetting('homeProductSectionTwo')))
                                : collect();
                    @endphp
                    <div class="pt-2">
                        <input type="hidden" name="types[]" value="showHomeProductSectionTwo">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Product Section Two') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeProductSectionTwo" value="1"
                                isChecked="{{ getSetting('showHomeProductSectionTwo') ?? false }}" />
                        </div>


                        <input type="hidden" name="types[]" value="homeProductSectionTwo">
                        <x-backend.inputs.select :labelInline="false" label="Products" name="homeProductSectionTwo[]"
                            multiple data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select products') }}" :isRequired="false">

                            @foreach ($products as $product)
                                <x-backend.inputs.select-option
                                    name="{{ $product->collectTranslation('name', $langKey) }}"
                                    value="{{ $product->id }}"
                                    selected="{{ $homeProductSectionTwo->contains($product->id) ? $product->id : 0 }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeProductSectionTwoTitle">
                        <x-backend.inputs.text :labelInline="false" label="Section Title"
                            name="homeProductSectionTwoTitle"
                            value="{{ getSetting('homeProductSectionTwoTitle') ?? '' }}"
                            placeholder="Type section title" :isRequired="false" />

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 4 in a row banners --}}
    <div class="ac !mt-0 ">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('4 Banners in a Row') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="types[]" value="homeFourInRowBanners">
                    <input type="hidden" name="types[]" value="homeFourInRowBannerLinks">

                    <div class="slider-input-wrapper">
                        @if (getSetting('homeFourInRowBanners') != null)
                            @foreach (json_decode(getSetting('homeFourInRowBanners'), true) as $key => $value)
                                <div class="slider-input">
                                    <x-backend.inputs.file :labelInline="false" :trashBtn="true"
                                        trashParent=".slider-input" label="Image" name="homeFourInRowBanners[]"
                                        value="{{ $value }}" class="mb-3" />
                                    <x-backend.inputs.text :labelInline="false" label="Link"
                                        name="homeFourInRowBannerLinks[]"
                                        value="{{ json_decode(getSetting('homeFourInRowBannerLinks'), true)[$key] }}"
                                        placeholder="https://www.example.com" :isRequired="false" />
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <a href="javascript:void(0);" class="text-theme-secondary-light" data-toggle="add-more"
                        data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="homeFourInRowBanners[]" value=""  class="mb-3" />
                                            <x-backend.inputs.text :labelInline="false" label="Link" name="homeFourInRowBannerLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                    </div>'
                        data-target=".slider-input-wrapper">
                        {{ translate('Add Banner') }}
                        <span class="ml-2">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </a>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- featured shops --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Featured Shops') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $featuredShops =
                            getSetting('homeFeaturedShops') != null
                                ? collect(json_decode(getSetting('homeFeaturedShops')))
                                : collect();
                    @endphp
                    <div class="pt-2">

                        <input type="hidden" name="types[]" value="showHomeFeaturedShops">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Featured Shops') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeFeaturedShops" value="1"
                                isChecked="{{ getSetting('showHomeFeaturedShops') ?? false }}" />
                        </div>


                        <input type="hidden" name="types[]" value="homeFeaturedShops">
                        <x-backend.inputs.select :labelInline="false" label="Featured Shops" name="homeFeaturedShops[]"
                            multiple data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select shops') }}" :isRequired="false">

                            @foreach ($shops as $shop)
                                <x-backend.inputs.select-option name="{{ $shop->name }}"
                                    value="{{ $shop->id }}"
                                    selected="{{ $featuredShops->contains($shop->id) ? $shop->id : 0 }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2 in a row banners --}}
    <div class="ac !mt-0 ">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('2 Banners in a Row') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="types[]" value="homeTwoInRowBanners">
                    <input type="hidden" name="types[]" value="homeTwoInRowBannerLinks">

                    <div class="slider-input-wrapper">
                        @if (getSetting('homeTwoInRowBanners') != null)
                            @foreach (json_decode(getSetting('homeTwoInRowBanners'), true) as $key => $value)
                                <div class="slider-input">
                                    <x-backend.inputs.file :labelInline="false" :trashBtn="true"
                                        trashParent=".slider-input" label="Image" name="homeTwoInRowBanners[]"
                                        value="{{ $value }}" class="mb-3" />
                                    <x-backend.inputs.text :labelInline="false" label="Link"
                                        name="homeTwoInRowBannerLinks[]"
                                        value="{{ json_decode(getSetting('homeTwoInRowBannerLinks'), true)[$key] }}"
                                        placeholder="https://www.example.com" :isRequired="false" />
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <a href="javascript:void(0);" class="text-theme-secondary-light" data-toggle="add-more"
                        data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="homeTwoInRowBanners[]" value=""  class="mb-3" />
                                            <x-backend.inputs.text :labelInline="false" label="Link" name="homeTwoInRowBannerLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                    </div>'
                        data-target=".slider-input-wrapper">
                        {{ translate('Add Banner') }}
                        <span class="ml-2">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </a>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- trendy products --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Trendy Products') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $trendyProducts =
                            getSetting('homeTrendyProducts') != null
                                ? collect(json_decode(getSetting('homeTrendyProducts')))
                                : collect();
                    @endphp
                    <div class="pt-2">

                        <input type="hidden" name="types[]" value="showHomeTrendyProducts">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Trendy Products') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeTrendyProducts" value="1"
                                isChecked="{{ getSetting('showHomeTrendyProducts') ?? false }}" />
                        </div>


                        <input type="hidden" name="types[]" value="homeTrendyProducts">
                        <x-backend.inputs.select :labelInline="false" label="Trendy Products"
                            name="homeTrendyProducts[]" multiple data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select products') }}" :isRequired="false">

                            @foreach ($products as $product)
                                <x-backend.inputs.select-option
                                    name="{{ $product->collectTranslation('name', $langKey) }}"
                                    value="{{ $product->id }}"
                                    selected="{{ $trendyProducts->contains($product->id) ? $product->id : 0 }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 3 in a row banners --}}
    <div class="ac !mt-0 ">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('3 Banners in a Row') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="types[]" value="homeThreeInRowBanners">
                    <input type="hidden" name="types[]" value="homeThreeInRowBannerLinks">

                    <div class="slider-input-wrapper">
                        @if (getSetting('homeThreeInRowBanners') != null)
                            @foreach (json_decode(getSetting('homeThreeInRowBanners'), true) as $key => $value)
                                <div class="slider-input">
                                    <x-backend.inputs.file :labelInline="false" :trashBtn="true"
                                        trashParent=".slider-input" label="Image" name="homeThreeInRowBanners[]"
                                        value="{{ $value }}" class="mb-3" />
                                    <x-backend.inputs.text :labelInline="false" label="Link"
                                        name="homeThreeInRowBannerLinks[]"
                                        value="{{ json_decode(getSetting('homeThreeInRowBannerLinks'), true)[$key] }}"
                                        placeholder="https://www.example.com" :isRequired="false" />
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <a href="javascript:void(0);" class="text-theme-secondary-light" data-toggle="add-more"
                        data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="homeThreeInRowBanners[]" value=""  class="mb-3" />
                                            <x-backend.inputs.text :labelInline="false" label="Link" name="homeThreeInRowBannerLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                    </div>'
                        data-target=".slider-input-wrapper">
                        {{ translate('Add Banner') }}
                        <span class="ml-2">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </a>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- category products --}}
    @php
        $homeCategories = collect(json_decode(getSetting('homeCategories'), true));
    @endphp
    <div class="ac !mt-0 ">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Category Products') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="types[]" value="homeCategories">
                    <input type="hidden" name="types[]" value="homeCategoryImages">
                    <input type="hidden" name="types[]" value="homeCategoryLinks">

                    <div class="slider-input-wrapper">
                        @if (getSetting('homeCategories') != null)

                            @foreach ($homeCategories as $key => $value)
                                <div class="slider-input">
                                    <x-backend.inputs.file :labelInline="false" :trashBtn="true"
                                        trashParent=".slider-input" label="Image" name="homeCategoryImages[]"
                                        value="{{ json_decode(getSetting('homeCategoryImages'), true)[$key] }}"
                                        class="mb-3" />
                                    <x-backend.inputs.text :labelInline="false" label="Link"
                                        name="homeCategoryLinks[]"
                                        value="{{ json_decode(getSetting('homeCategoryLinks'), true)[$key] }}"
                                        placeholder="https://www.example.com" :isRequired="false" />

                                    <div class="mt-5">
                                        <input type="hidden" name="homeCategories[]" value="{{ $value }}">
                                        @php
                                            $selectedCat = \App\Models\Category::where('id', $value)->first();
                                        @endphp
                                        <x-backend.inputs.text :labelInline="false" label="Category" name="tempCat[]"
                                            value="{{ $selectedCat->collectTranslation('name', $langKey) }}"
                                            :isRequired="false" :isDisabled="true" />
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <a href="javascript:void(0);" class="text-theme-secondary-light" data-toggle="add-more"
                        data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="homeCategoryImages[]" value=""  class="mb-3" />
                                            <x-backend.inputs.text :labelInline="false" label="Link" name="homeCategoryLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                            <div class="mt-5"> 
<x-backend.inputs.select :labelInline="false" label="Select Category" name="homeCategories[]"> 
@foreach ($categories as $cat)
@if (!$homeCategories->contains($cat->id))
<x-backend.inputs.select-option
name="{{ $cat->collectTranslation('name', $langKey) }}"
value="{{ $cat->id }}"   />
@endif 
@foreach ($cat->childrenCategories as $childCategory)
@include(' backend.admin.categories.subCategory', [
    'subCategory' => $childCategory,
    'langKey' => $langKey,
    'category' => null,
    'homeCategories' => $homeCategories,
])
@endforeach
@endforeach
                        </x-backend.inputs.select>
            </div>
        </div>'
                        data-target=".slider-input-wrapper">
                        {{ translate('Add Category') }}
                        <span class="ml-2">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </a>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- about us --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('About Us') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="pt-2">
                        <input type="hidden" name="types[]" value="showHomeAboutUsSection">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show About Us Section') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeAboutUsSection" value="1"
                                isChecked="{{ getSetting('showHomeAboutUsSection') ?? false }}" />
                        </div>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeAboutUsTitle">
                        <x-backend.inputs.text :labelInline="false" label="Section Title" name="homeAboutUsTitle"
                            value="{{ getSetting('homeAboutUsTitle') ?? '' }}" placeholder="Type section title"
                            :isRequired="false" />

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeAboutUsSubTitle">
                        <x-backend.inputs.text :labelInline="false" label="Section Subtitle" name="homeAboutUsSubTitle"
                            value="{{ getSetting('homeAboutUsSubTitle') ?? '' }}"
                            placeholder="Type section subtitle" :isRequired="false" />

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeAboutUsText">
                        <x-backend.inputs.textarea :labelInline="false" label="Section Description"
                            name="homeAboutUsText" value="{{ getSetting('homeAboutUsText') ?? '' }}"
                            placeholder="Type section description" :isRequired="false" />


                        <input type="hidden" name="types[]" value="homeAboutUsImage">
                        <x-backend.inputs.file :labelInline="false" label="Image" name="homeAboutUsImage"
                            value="{{ getSetting('homeAboutUsImage') }}" class="mb-3" />

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- main category --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Main Categories') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $mainCategories =
                            getSetting('homeMainCategories') != null
                                ? collect(json_decode(getSetting('homeMainCategories')))
                                : collect();
                    @endphp
                    <div class="pt-2">

                        <input type="hidden" name="types[]" value="showHomeMainCategories">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Main Categories') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeMainCategories" value="1"
                                isChecked="{{ getSetting('showHomeMainCategories') ?? false }}" />
                        </div>


                        <input type="hidden" name="types[]" value="homeMainCategories">
                        <x-backend.inputs.select :labelInline="false" label="Main Categories"
                            name="homeMainCategories[]" multiple data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select categories') }}" :isRequired="false">

                            @foreach ($categories as $cat)
                                <x-backend.inputs.select-option
                                    name="{{ $cat->collectTranslation('name', $langKey) }}"
                                    value="{{ $cat->id }}"
                                    selected="{{ $mainCategories->contains($cat->id) ? $cat->id : 0 }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- footer --}}
    <div class="ac !mt-0">
        <h2 class="ac-header">
            <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
                <span class="">{{ translate('Footer') }}</span>
            </button>
        </h2>
        <div class="ac-panel">
            <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
                <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="pt-2">

                        <input type="hidden" name="types[]" value="homeFooterLogo">
                        <x-backend.inputs.file :labelInline="false" label="Logo" name="homeFooterLogo"
                            value="{{ getSetting('homeFooterLogo') }}" />

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterCustomerSupport">
                        <x-backend.inputs.text :labelInline="false" label="Customer Support"
                            name="homeFooterCustomerSupport"
                            value="{{ getSetting('homeFooterCustomerSupport') ?? '' }}" placeholder="(629) 555-0129"
                            :isRequired="false" />

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterAddress">
                        <x-backend.inputs.text :labelInline="false" label="Customer Address" name="homeFooterAddress"
                            value="{{ getSetting('homeFooterAddress') ?? '' }}"
                            placeholder="4517 Washington Ave. Manchester, Kentucky 39495" :isRequired="false" />

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterEmail">
                        <x-backend.inputs.text :labelInline="false" label="Customer Email" name="homeFooterEmail"
                            value="{{ getSetting('homeFooterEmail') ?? '' }}" placeholder="info@epikcart.com"
                            :isRequired="false" />

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="showHomeFacebookLink">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Facebook') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeFacebookLink" value="1"
                                isChecked="{{ getSetting('showHomeFacebookLink') ?? false }}" />
                        </div>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterFacebookLink">
                        <x-backend.inputs.text :labelInline="false" label="Facebook Link" name="homeFooterFacebookLink"
                            value="{{ getSetting('homeFooterFacebookLink') ?? '' }}"
                            placeholder="https://www.facebook.com/epikcoders" :isRequired="false" />


                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="showHomeTwitterLink">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Twitter') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeTwitterLink" value="1"
                                isChecked="{{ getSetting('showHomeTwitterLink') ?? false }}" />
                        </div>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterTwitterLink">
                        <x-backend.inputs.text :labelInline="false" label="Twitter Link" name="homeFooterTwitterLink"
                            value="{{ getSetting('homeFooterTwitterLink') ?? '' }}"
                            placeholder="https://www.twitter.com/epikcoders" :isRequired="false" />

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="showHomeInstagramLink">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Instagram') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeInstagramLink" value="1"
                                isChecked="{{ getSetting('showHomeInstagramLink') ?? false }}" />
                        </div>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterInstagramLink">
                        <x-backend.inputs.text :labelInline="false" label="Instagram Link"
                            name="homeFooterInstagramLink" value="{{ getSetting('homeFooterInstagramLink') ?? '' }}"
                            placeholder="https://www.instagram.com/epikcoders" :isRequired="false" />



                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="showHomeYoutubeLink">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Youtube') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeYoutubeLink" value="1"
                                isChecked="{{ getSetting('showHomeYoutubeLink') ?? false }}" />
                        </div>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterYoutubeLink">
                        <x-backend.inputs.text :labelInline="false" label="Youtube Link" name="homeFooterYoutubeLink"
                            value="{{ getSetting('homeFooterYoutubeLink') ?? '' }}"
                            placeholder="https://www.youtube.com/epikcoders" :isRequired="false" />


                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="showHomeLinkedInLink">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show LinkedIn') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeLinkedInLink" value="1"
                                isChecked="{{ getSetting('showHomeLinkedInLink') ?? false }}" />
                        </div>


                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterLinkedInLink">
                        <x-backend.inputs.text :labelInline="false" label="LinkedIn Link" name="homeFooterLinkedInLink"
                            value="{{ getSetting('homeFooterLinkedInLink') ?? '' }}"
                            placeholder="https://www.linkedin.com/epikcoders" :isRequired="false" />


                        <div class="mt-5"></div>
                        <h5>{{ translate('Quick Links') }}</h5>
                        <input type="hidden" name="types[]" value="homeFooterQuickLinkTitles">
                        <input type="hidden" name="types[]" value="homeFooterQuickLinks">
                        <div class="quick-link-input-wrapper">
                            @if (getSetting('homeFooterQuickLinks') != null)
                                @foreach (json_decode(getSetting('homeFooterQuickLinks'), true) as $key => $value)
                                    <div class="quick-link-input">
                                        <x-backend.inputs.text :labelInline="false" label="Title"
                                            name="homeFooterQuickLinkTitles[]"
                                            value="{{ json_decode(getSetting('homeFooterQuickLinkTitles'), true)[$key] }}"
                                            placeholder="Type title" :isRequired="false" />
                                        <x-backend.inputs.text :labelInline="false" class="mt-3" label="Link"
                                            name="homeFooterQuickLinks[]" value="{{ $value }}"
                                            placeholder="https://www.example.com" :isRequired="false" />
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <a href="javascript:void(0);" class="text-theme-secondary-light mt-3" data-toggle="add-more"
                            data-content='<div class="quick-link-input mt-3">
                                        <x-backend.inputs.text :labelInline="false" label="Title" name="homeFooterQuickLinkTitles[]"  placeholder="Type title" :isRequired="false" /> 
                                        <div class="mt-3"></div>
                                        <x-backend.inputs.text :labelInline="false" label="Link" name="homeFooterQuickLinks[]"  placeholder="https://www.example.com" :isRequired="false" />
                                    </div>'
                            data-target=".quick-link-input-wrapper">
                            {{ translate('Add Quick Link') }}
                            <span class="ml-2">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </a>


                        @php
                            $popularTags =
                                getSetting('homeFooterPopularTags') != null
                                    ? collect(json_decode(getSetting('homeFooterPopularTags')))
                                    : collect();
                        @endphp
                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterPopularTags">
                        <x-backend.inputs.select :labelInline="false" label="Popular Tags"
                            name="homeFooterPopularTags[]" multiple data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select tags') }}" :isRequired="false">
                            @foreach ($tags as $tag)
                                <x-backend.inputs.select-option name="{{ $tag->name }}"
                                    value="{{ $tag->id }}"
                                    selected="{{ $popularTags->contains($tag->id) ? $tag->id : 0 }}" />
                            @endforeach
                        </x-backend.inputs.select>


                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="showHomeFooterPlayStoreLink">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Playstore') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeFooterPlayStoreLink"
                                value="1"
                                isChecked="{{ getSetting('showHomeFooterPlayStoreLink') ?? false }}" />
                        </div>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterPlayStoreLink">
                        <x-backend.inputs.text :labelInline="false" label="Playstore Link"
                            name="homeFooterPlayStoreLink" value="{{ getSetting('homeFooterPlayStoreLink') ?? '' }}"
                            :isRequired="false" />

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="showHomeFooterAppStoreLink">
                        <div class="flex items-center justify-between mb-5">
                            <p>{{ translate('Show Appstore') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="showHomeFooterAppStoreLink"
                                value="1"
                                isChecked="{{ getSetting('showHomeFooterAppStoreLink') ?? false }}" />
                        </div>

                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterAppStoreLink">
                        <x-backend.inputs.text :labelInline="false" label="Appstore Link" name="homeFooterAppStoreLink"
                            value="{{ getSetting('homeFooterAppStoreLink') ?? '' }}" :isRequired="false" />


                        <div class="mt-5"></div>
                        <input type="hidden" name="types[]" value="homeFooterCopyrightText">
                        <x-backend.inputs.text :labelInline="false" label="Copyright Text"
                            name="homeFooterCopyrightText" value="{{ getSetting('homeFooterCopyrightText') ?? '' }}"
                            placeholder="Copyrights | All rights reserved" :isRequired="false" />

                        <input type="hidden" name="types[]" value="homeFooterSecuredPayments">
                        <x-backend.inputs.file :labelInline="false" label="Secured Payments"
                            name="homeFooterSecuredPayments" value="" />

                        <div class="flex justify-end pt-2">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                                class="submit-btn mt-3" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
