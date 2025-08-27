@extends('layouts.admin')

@section('title')
    {{ translate('General Settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    {{-- breadcrumb starts --}}
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('General Settings') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Configurations') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Settings') }}</p>
        </div>
    </div>
    {{-- breadcrumb ends --}}

    {{-- information-form starts --}}
    <div class="card max-w-[900px]">
        <h4 class="card__title">{{ translate('Settings Informations') }}</h4>

        <div class="card__content">
            <x-backend.forms.settings.information-form />
        </div>
    </div>
    {{-- information-form ends --}}

    {{-- SEO starts --}}
    <div class="card max-w-[900px] mt-5">
        <h4 class="card__title">{{ translate('SEO Informations') }}</h4>

        <div class="card__content">
            <form class="space-y-3" action="{{ route('admin.general-settings.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="meta_title">
                <input type="hidden" name="types[]" value="meta_description">
                <input type="hidden" name="types[]" value="meta_keywords">
                <input type="hidden" name="types[]" value="meta_image">

                <x-backend.inputs.seo metaTitle="{{ getSetting('meta_title') }}"
                    metaDescription="{{ getSetting('meta_description') }}" metaKeywords="{{ getSetting('meta_keywords') }}"
                    metaImage="{{ getSetting('meta_image') }}" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                </div>
            </form>
        </div>
    </div>
    {{-- SEO ends --}}
@endsection
