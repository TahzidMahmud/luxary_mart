@extends('layouts.admin')

@section('title')
    {{ translate('Payouts') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Payouts') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Payouts') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[480px]"
                    placeholder="Type name/email/phone & hit enter">

                    <x-backend.inputs.select name="shopId" class="filterSelect" :isRequired="false" data-search="false">
                        <x-backend.inputs.select-option name="{{ translate('All Shops') }}" value=""
                            selected="{{ Request::get('shopId') }}" />
                        @foreach ($shops as $shop)
                            <x-backend.inputs.select-option name="{{ $shop->name }}" value="{{ $shop->id }}"
                                selected="{{ Request::get('shopId') }}" />
                        @endforeach
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
                            {{ translate('Shop Info') }}
                        </th>
                        <th>
                            {{ translate('Amount') }}
                        </th>

                        <th data-breakpoints="xs sm">
                            {{ translate('Payment Method') }}
                        </th>

                        <th data-breakpoints="xs sm">
                            {{ translate('Transaction Id') }}
                        </th>

                        <th data-breakpoints="xs sm">
                            {{ translate('Date') }}
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($payouts as $key => $payout)
                        @php
                            $shop = $payout->shopInfo;
                        @endphp

                        <tr>
                            <td>{{ $key + 1 + ($payouts->currentPage() - 1) * $payouts->perPage() }}</td>

                            <td>
                                <div class="flex items-center gap-4">
                                    <div class="max-xs:hidden">
                                        <img src="{{ uploadedAsset($shop->logo) }}" alt=""
                                            class="w-[70px] h-[80px] rounded-md" />
                                    </div>
                                    <div>
                                        <div class=" line-clamp-2">
                                            {{ translate('Shop Name') }} : {{ $shop->name }}
                                        </div>
                                        <div class="line-clamp-2">
                                            {{ translate('Seller Name') }} : {{ $shop->user->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ formatPrice($payout->given_amount) }}
                                </div>
                            </td>
                            <td>
                                <div class=" line-clamp-2 capitalize">
                                    {{ $payout->payment_method }}
                                </div>
                            </td>

                            <td>
                                <div class=" line-clamp-2 capitalize">
                                    {{ $payout->payment_details ?? '-' }}
                                </div>
                            </td>


                            <td>
                                <div class=" line-clamp-2">
                                    {{ date('d M, Y', strtotime($payout->created_at)) }}
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $payouts->links() }}
            </div>
        </div>
    </div>
@endsection
