@extends('layouts.admin')

@section('title')
    {{ translate('Product Real Pictures') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Real Pictures') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.products.index') }}" class="font-bold ">{{ translate('Products') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Real Pictures') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- start::product-form -->
    <div class="grid grid-cols-12 md:grid-cols-3 lg:grid-cols-4 gap-3">
        @php
            $galleryImages = collect();
            if (!is_null($product->real_pictures)) {
                $galleryImages = explode(',', $product->real_pictures);
            }
        @endphp

        @foreach ($galleryImages as $image)
            <a href="{{ uploadedAsset($image) }}" download="{{ $image }}"><img src="{{ uploadedAsset($image) }}"
                    alt=""></a>
        @endforeach
    </div>
    <!-- end::product-form -->
@endsection
