@extends('layouts.admin')

@section('title')
    {{ translate('Create Stock Transfer') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Create Stock Transfer') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.stockTransfers.index') }}" class="font-bold ">{{ translate('Stock Transfer') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Create Stock Transfer') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="card__content">
            <x-backend.forms.stock-transfer-form :warehouses="$warehouses" />
        </div>
    </div>
@endsection

@section('scripts')
    <x-backend.inc.stock-transfers-scripts />
@endsection
