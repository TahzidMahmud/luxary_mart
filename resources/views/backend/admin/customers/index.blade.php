@extends('layouts.admin')

@section('title')
    {{ translate('Customer List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Customer List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Customer List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5">
            <div class="card theme-table">
                <div
                    class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

                    <div></div>

                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[480px]"
                        placeholder="Type name/email/phone & hit enter">

                        <x-backend.inputs.select name="sortBy" class="filterSelect" :isRequired="false">
                            <x-backend.inputs.select-option name="{{ translate('Orders') }}" value=""
                                selected="{{ Request::get('sortBy') }}" />
                            <x-backend.inputs.select-option name="{{ translate('$ - High to Low') }}"
                                value="amountHighToLow" selected="{{ Request::get('sortBy') }}" />
                            <x-backend.inputs.select-option name="{{ translate('$ - Low to High') }}"
                                value="amountLowToHigh" selected="{{ Request::get('sortBy') }}" />
                            <x-backend.inputs.select-option name="{{ translate('N - High to Low') }}" value="qtyHighToLow"
                                selected="{{ Request::get('sortBy') }}" />
                            <x-backend.inputs.select-option name="{{ translate('N - Low to High') }}" value="qtyLowToHigh"
                                selected="{{ Request::get('sortBy') }}" />

                        </x-backend.inputs.select>
                    </x-backend.forms.search-form>
                </div>

                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-theme-primary/10">
                        <tr>
                            <th data-breakpoints="xs sm">
                                #
                            </th>
                            <th>
                                {{ translate('Name') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Email') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Phone') }}
                            </th>
                            <th>
                                {{ translate('Orders') }}
                            </th>
                            <th>
                                {{ translate('Banned') }}
                            </th>
                            <th data-breakpoints="xs sm" class="text-end">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $key => $customer)
                            <tr>
                                <td>{{ $key + 1 + ($customers->currentPage() - 1) * $customers->perPage() }}</td>

                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="max-xs:hidden">
                                            <img src="{{ uploadedAsset($customer->avatar) }}" alt=""
                                                class="w-[70px] h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                        </div>
                                        <div class=" line-clamp-2">
                                            {{ $customer->name }}
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class=" line-clamp-2">
                                        {{ $customer->email ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class=" line-clamp-2">
                                        {{ $customer->phone ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="text-dark underline line-clamp-2">
                                        <a href="{{ route('admin.orders.index') }}?customerId={{ $customer->id }}">
                                            {{ formatPrice($customer->orders()->sum('total_amount')) }}
                                            ({{ $customer->orders()->count() }})
                                        </a>
                                    </div>
                                </td>

                                <td>
                                    @can('edit_customers')
                                        <x-backend.inputs.checkbox toggler="true"
                                            data-route="{{ route('admin.customers.toggleBan') }}" variant="danger"
                                            name="isActiveCheckbox" value="{{ $customer->id }}"
                                            data-status="{{ $customer->is_banned }}"
                                            isChecked="{{ (int) $customer->is_banned == 1 }}" />
                                    @endcan
                                </td>

                                <td class="text-end">
                                    @can('delete_customers')
                                        <a href="javascript:void(0);"
                                            data-href="{{ route('admin.customers.destroy', $customer->id) }}"
                                            data-title="{{ translate('Are you sure want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="DELETE" data-micromodal-trigger="confirm-modal"
                                            class="confirm-modal text-red-500 text-lg ms-2">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card__footer">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
