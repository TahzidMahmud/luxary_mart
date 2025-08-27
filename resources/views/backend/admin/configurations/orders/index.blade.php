@extends('layouts.admin')

@section('title')
    {{ translate('Order Settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    {{-- breadcrumb starts --}}
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Order Settings') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Configurations') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Order Settings') }}</p>
        </div>
    </div>
    {{-- breadcrumb ends --}}

    {{-- information-form starts --}}
    <div class="card max-w-[900px]">
        <h4 class="card__title">{{ translate('Settings Informations') }}</h4>

        <div class="card__content">
            <form action="{{ route('admin.general-settings.update') }}" class="space-y-3" method="POST"
                enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="orderCodePrefix">
                <x-backend.inputs.text label="Order Code Prefix" name="orderCodePrefix" placeholder="Type order code prefix"
                    value="{{ getSetting('orderCodePrefix') }}" />

                <input type="hidden" name="types[]" value="orderCodeStartsFrom">
                <x-backend.inputs.text label="Order Code Starts From" name="orderCodeStartsFrom" placeholder="10001"
                    value="{{ getSetting('orderCodeStartsFrom') }}" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                </div>
            </form>

        </div>
    </div>
    {{-- information-form ends --}}
@endsection
