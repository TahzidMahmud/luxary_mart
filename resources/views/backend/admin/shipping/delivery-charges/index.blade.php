@extends('layouts.admin')

@section('title')
    {{ translate('Update Delivery Charges') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Update Delivery Charges') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Zones') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-12">
        <div class="md:col-span-6 card">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                <div></div>
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
            </div>

            <form action="{{ route('admin.delivery-charges.store') }}" method="POST">
                @csrf
                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-theme-primary/10 px-3">
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                {{ translate('Zone') }}
                            </th>

                            <th class="max-w-[130px]">
                                {{ translate('Delivery Charge') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($zones as $key => $zone)
                            <tr>

                                <td>{{ $key + 1 + ($zones->currentPage() - 1) * $zones->perPage() }}</td>

                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="hidden">
                                            <img src="{{ uploadedAsset($zone->banner) }}" alt=""
                                                class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md" />
                                        </div>
                                        <div class=" line-clamp-2">
                                            {{ $zone->name }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" name="zone_ids[]" value="{{ $zone->id }}" />
                                    <x-backend.inputs.number name="delivery_charges[]" class="max-w-[180px]"
                                        placeholder="Type delivery charge"
                                        value="{{ $zone->shippingCharge(shopId()) ? $zone->shippingCharge(shopId())->charge : shop()->default_shipping_charge }}" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pe-8 flex justify-end mt-4">
                    <x-backend.inputs.button buttonText="Save Delivery Charges" type="submit" />
                </div>

            </form>

            <div class="card__footer">
                {{ $zones->links() }}
            </div>
        </div>
    </div>
@endsection
