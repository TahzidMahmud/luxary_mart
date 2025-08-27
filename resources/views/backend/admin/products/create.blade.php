@extends('layouts.admin')

@section('title')
    {{ translate('Add New Product') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Add New Product') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.products.index') }}" class="font-bold ">{{ translate('Products') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Add New Product') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->
    <!-- start::product-form -->
    <x-backend.forms.product-form :categories="$categories" :brands="$brands" :units="$units" :variations="$variations" :taxes="$taxes"
        :tags="$tags" :badges="$badges" />
    <!-- end::product-form -->
@endsection


@section('scripts')
    <x-backend.inc.product-scripts />
@endsection
