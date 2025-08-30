@extends('layouts.admin')

@section('title')
    {{ translate('Delivery Partners Setting') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Delivery Partners') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Delivery Partners') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-1 gap-3">
        <div class="card">
            <h4 class="card__title">{{ translate('SteadFast Credentials') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="STEADFAST_API_KEY">
                    <x-backend.inputs.text label="STEADFAST_API_KEY" name="STEADFAST_API_KEY" placeholder="STEADFAST_API_KEY"
                        value="{{ config('app.STEADFAST_API_KEY') }}" />

                    <input type="hidden" name="types[]" value="STEADFAST_SECRET_KEY">
                    <x-backend.inputs.text label="STEADFAST_SECRET_KEY" name="STEADFAST_SECRET_KEY"
                        placeholder="STEADFAST_SECRET_KEY" value="{{ config('app.STEADFAST_SECRET_KEY') }}" />

                    <input type="hidden" name="types[]" value="STEADFAST_BASE_URL">
                    <x-backend.inputs.text label="STEADFAST_BASE_URL" name="STEADFAST_BASE_URL"
                        placeholder="STEADFAST_BASE_URL" value="{{ config('app.STEADFAST_BASE_URL') }}" />

                    <input type="hidden" name="types[]" value="STEADFAST_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="STEADFAST_ACTIVATION" name="STEADFAST_ACTIVATION"
                        placeholder="STEADFAST_ACTIVATION" value="1"
                        isChecked="{{ config('app.STEADFAST_ACTIVATION') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h4 class="card__title">{{ translate('Redx Credentials') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="REDX_API_KEY">
                    <x-backend.inputs.text label="REDX_API_KEY" name="REDX_API_KEY"
                        placeholder="{{ translate('REDX_API_KEY') }}"
                        value="{{ config('app.REDX_API_KEY') }}" />

                    <input type="hidden" name="types[]" value="REDX_BASE_URL">
                    <x-backend.inputs.text label="REDX_BASE_URL" name="REDX_BASE_URL"
                        placeholder="{{ translate('REDX_BASE_URL') }}"
                        value="{{ config('app.REDX_BASE_URL') }}" />

                    <input type="hidden" name="types[]" value="REDX_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Meta Pixel Activation" name="REDX_ACTIVATION"
                        placeholder="{{ translate('RedX Activation') }}" value="1"
                        isChecked="{{ config('app.REDX_ACTIVATION') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h4 class="card__title">{{ translate('Patahao Credentials') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="PATHAO_CLIENT_ID">
                    <x-backend.inputs.text label="PATHAO_CLIENT_ID" name="PATHAO_CLIENT_ID"
                        placeholder="{{ translate('PATHAO_CLIENT_ID') }}"
                        value="{{ config('app.PATHAO_CLIENT_ID') }}" />

                    <input type="hidden" name="types[]" value="PATHAO_CLIENT_SECRET">
                    <x-backend.inputs.text label="PATHAO_CLIENT_SECRET" name="PATHAO_CLIENT_SECRET"
                        placeholder="{{ translate('PATHAO_CLIENT_SECRET') }}"
                        value="{{ config('app.PATHAO_CLIENT_SECRET') }}" />

                    <input type="hidden" name="types[]" value="PATHAO_USERNAME">
                    <x-backend.inputs.text label="PATHAO_USERNAME" name="PATHAO_USERNAME"
                        placeholder="{{ translate('PATHAO_USERNAME') }}"
                        value="{{ config('app.PATHAO_USERNAME') }}" />
                    <input type="hidden" name="types[]" value="PATHAO_PASSWORD">
                    <x-backend.inputs.text label="PATHAO_PASSWORD" name="PATHAO_PASSWORD"
                        placeholder="{{ translate('PATHAO_PASSWORD') }}"
                        value="{{ config('app.PATHAO_PASSWORD') }}" />
                    <input type="hidden" name="types[]" value="PATHAO_BASE_URL">
                    <x-backend.inputs.text label="PATHAO_BASE_URL" name="PATHAO_BASE_URL"
                        placeholder="{{ translate('PATHAO_BASE_URL') }}"
                        value="{{ config('app.PATHAO_BASE_URL') }}" />
                    <input type="hidden" name="types[]" value="PATHAO_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Meta Pixel Activation" name="PATHAO_ACTIVATION"
                        placeholder="{{ translate('Pathao Activation') }}" value="1"
                        isChecked="{{ config('app.PATHAO_ACTIVATION') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
