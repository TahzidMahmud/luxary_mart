@extends('layouts.admin')

@section('title')
    {{ translate('OTP settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-phone"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('OTP') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('OTP Settings') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        {{-- BULK SMS --}}
        <div class="card ">
            <h4 class="card__title">{{ translate('BULK SMS') }}</h4>
            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="post" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="BULK_SMS_API_KEY">
                    <x-backend.inputs.text label="API KEY" name="BULK_SMS_API_KEY" placeholder="Type BULK SMS API KEY"
                        value="{{ env('BULK_SMS_API_KEY') }}" />

                    <input type="hidden" name="types[]" value="SENDER_ID">
                    <x-backend.inputs.text label="SENDER ID" name="SENDER_ID" placeholder="Type BULK SMS SENDER ID"
                        value="{{ env('SENDER_ID') }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
