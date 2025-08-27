@extends('layouts.admin')

@section('title')
    {{ translate('Social Media Login Setting') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Social Media Login') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Social Media Settings') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-3">
        <div class="card">
            <h4 class="card__title">{{ translate('Google Login Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="GOOGLE_CLIENT_ID">
                    <x-backend.inputs.text label="Google Client ID" name="GOOGLE_CLIENT_ID" placeholder="Google Client ID"
                        value="{{ config('app.GOOGLE_CLIENT_ID') }}" />

                    <input type="hidden" name="types[]" value="GOOGLE_CLIENT_SECRET">
                    <x-backend.inputs.text label="Google Client Secret" name="GOOGLE_CLIENT_SECRET"
                        placeholder="Google Client Secret" value="{{ config('app.GOOGLE_CLIENT_SECRET') }}" />

                    <input type="hidden" name="types[]" value="GOOGLE_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Google Activation" name="GOOGLE_ACTIVATION"
                        placeholder="Google Activation" value="1"
                        isChecked="{{ config('app.GOOGLE_ACTIVATION') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h4 class="card__title">{{ translate('Facebook Login Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="FACEBOOK_CLIENT_ID">
                    <x-backend.inputs.text label="Facebook Client ID" name="FACEBOOK_CLIENT_ID"
                        placeholder="{{ translate('Facebook Client ID') }}"
                        value="{{ config('app.FACEBOOK_CLIENT_ID') }}" />

                    <input type="hidden" name="types[]" value="FACEBOOK_CLIENT_SECRET">
                    <x-backend.inputs.text label="Facebook Client Secret" name="FACEBOOK_CLIENT_SECRET"
                        placeholder="{{ translate('Facebook Client Secret') }}"
                        value="{{ config('app.FACEBOOK_CLIENT_SECRET') }}" />


                    <input type="hidden" name="types[]" value="FACEBOOK_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Facebook Activation" name="FACEBOOK_ACTIVATION"
                        placeholder="{{ translate('Facebook Activation') }}" value="1"
                        isChecked="{{ config('app.FACEBOOK_ACTIVATION') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
