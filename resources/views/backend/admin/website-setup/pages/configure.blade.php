@extends('layouts.admin')

@section('title')
    {{ translate('Update Page') }} | {{ getSetting('systemName') }}
@endsection

@section('p-class')
    !ps-2
@endsection

@section('content')
    <div class="grid xl:grid-cols-12 gap-8">
        <div class="xl:col-span-3">
            <div class="card">
                <div class="bg-theme-primary text-foreground rounded-t py-5 px-8 text-md">
                    {{ translate($page?->title . ' Editor') }}
                </div>
                @if ($page->slug == 'homepage')
                    <x-backend.forms.homepage-form langKey="{{ $lang_key }}" />
                @endif

            </div>
        </div>
        <div class="xl:col-span-9">
            <!-- start::dashboard breadcrumb -->
            <div class="dashboard-nav py-4 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm sm:text-base font-bold">
                        {{ translate('Preview') }}
                    </span>
                </div>
            </div>
            <!-- end::dashboard breadcrumb -->
            <div class="card h-[calc(100vh-200px)] sticky top-3"> <!-- Adjusted min-h class -->
                <iframe src="{{ url('/') }}" class="w-full h-full" id="homepage-iframe"
                    title="Embedded Webpage"></iframe>
            </div>
        </div>
    </div>
@endsection
