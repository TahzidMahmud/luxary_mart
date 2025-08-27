@extends('layouts.admin')

@section('title')
    {{ translate('All States') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('All States') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('States') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]">
                    <x-backend.inputs.select name="countryId" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All Countries') }}" value=""
                            selected="" />
                        @foreach ($countries as $country)
                            <x-backend.inputs.select-option name="{{ $country->name }}" value="{{ $country->id }}"
                                selected="{{ Request::get('countryId') ? Request::get('countryId') : 0 }}" />
                        @endforeach
                    </x-backend.inputs.select>
                </x-backend.forms.search-form>
            </div>

            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-theme-primary/10 px-3">
                    <tr>
                        <th data-breakpoints="xs sm">
                            #
                        </th>
                        <th>
                            {{ translate('Name') }}
                        </th>
                        <th data-breakpoints="xs sm">{{ translate('Country') }}</th>
                        <th>{{ translate('Show/Hide') }}</th>
                        <th data-breakpoints="xs sm" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($states as $key => $state)
                        <tr>

                            <td>{{ $key + 1 + ($states->currentPage() - 1) * $states->perPage() }}</td>

                            <td>{{ $state->name }}</td>
                            <td><span
                                    class="px-2 py-1 text-xs bg-theme-secondary text-white rounded-md leading-none">{{ $state->country?->name }}</span>
                            </td>
                            <td>
                                <x-backend.inputs.checkbox toggler="true" data-route="{{ route('admin.states.status') }}"
                                    name="isActiveCheckbox" value="{{ $state->id }}"
                                    data-status="{{ $state->is_active }}"
                                    isChecked="{{ (int) $state->is_active == 1 }}" />
                            </td>

                            <td>
                                <div class="option-dropdown" tabindex="0">
                                    <div class="option-dropdown__toggler bg-theme-secondary/10 text-theme-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            @can('edit_states')
                                                <li>
                                                    <a href="{{ route('admin.states.edit', ['state' => $state->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('delete_states')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.states.destroy', $state->id) }}" />
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $states->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_states')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New State') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.state-form :countries="$countries" />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
