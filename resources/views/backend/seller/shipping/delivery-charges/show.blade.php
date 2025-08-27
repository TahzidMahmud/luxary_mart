@extends('layouts.seller')

@section('title')
    {{ translate('Delivery Charges') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Delivery Charges') }}
            </span>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-12">
        <div class="md:col-span-7 card theme-table">
            <div class="card__title">
                <button class="toggle-filters toggler max-w-[250px] lg:hidden w-full mb-4 mx-3"
                    data-target="#products-filter">
                    <span>{{ translate('Search And Filter') }}</span>
                    <span class="toggle-filters--icon">
                        <i class="fas fa-chevron-down"></i>
                    </span>
                </button>

                <div class="theme-table__filter hidden lg:flex max-xl:flex-col xl:items-center justify-between gap-3"
                    id="products-filter">
                    <div></div>

                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
                </div>
            </div>

            <div>
                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-theme-primary/10 px-3">
                        <tr>
                            <th>
                                #
                            </th>
                            <th data-breakpoints="xs sm md">
                                {{ translate('Area') }}
                            </th>
                            <th>{{ translate('City') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($areas as $key => $area)
                            <tr>

                                <td>{{ $key + 1 + ($areas->currentPage() - 1) * $areas->perPage() }}</td>

                                <td>{{ $area->name }}</td>
                                <td>
                                    <span
                                        class="px-2 py-1 text-xs bg-theme-secondary text-white rounded-md leading-none">{{ $area->city->name }}
                                    </span>
                                </td>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-5">
                    {{ $areas->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
