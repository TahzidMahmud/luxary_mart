@extends('layouts.admin')

@section('title')
    {{ translate('Create Stock Adjustment') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Create Stock Adjustment') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.stockAdjustments.index') }}" class="font-bold ">{{ translate('Stock Adjustments') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Create Stock Adjustment') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="card__content">
            <x-backend.forms.stock-adjustment-form :warehouses="$warehouses" />
        </div>
    </div>
@endsection

@section('scripts')
    <x-backend.inc.stock-adjustments-scripts />
@endsection
