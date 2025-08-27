@extends('layouts.admin')

@section('title')
    {{ translate('Purchase Orders') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Purchase Order List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Purchase Order List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card theme-table">
        <div
            class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
            @can('create_purchase_orders')
                <x-backend.inputs.link href="{{ route('admin.purchase-orders.create') }}"> <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span> {{ translate('Add New') }}</x-backend.inputs.link>
            @else
                <div></div>
            @endcan

            <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]"
                placeholder="Type reference code & hit enter" />
        </div>

        <table class="product-list-table footable w-full">
            <thead class="uppercase text-left bg-theme-primary/10">
                <tr>
                    <th data-breakpoints="xs sm">
                        #
                    </th>
                    <th data-breakpoints="xs sm">
                        {{ translate('Date') }}
                    </th>
                    <th data-breakpoints="xs sm">
                        {{ translate('Reference') }}
                    </th>
                    <th data-breakpoints="xs sm md">
                        {{ translate('Supplier') }}
                    </th>
                    <th>
                        {{ translate('Warehouse') }}
                    </th>
                    <th>
                        {{ translate('Status') }}
                    </th>
                    <th>
                        {{ translate('Total') }}
                    </th>

                    <th data-breakpoints="xs sm">
                        {{ translate('Payment Status') }}
                    </th>

                    <th data-breakpoints="xs sm md">
                        {{ translate('Paid') }}
                    </th>
                    <th data-breakpoints="xs sm md">
                        {{ translate('Due') }}
                    </th>
                    <th data-breakpoints="xs sm" class="w-[180px]">
                        {{ translate('Options') }}
                    </th>
                </tr>
            </thead>
            <tbody class="po-order-tbody">
                @foreach ($purchaseOrders as $key => $purchaseOrder)
                    @php
                        $returnOrder = $purchaseOrder->return;
                    @endphp
                    <tr>
                        <td>{{ $key + 1 + ($purchaseOrders->currentPage() - 1) * $purchaseOrders->perPage() }}</td>
                        <td>
                            <div class=" line-clamp-2">
                                {{ date('d M, Y', $purchaseOrder->date) }}
                            </div>
                        </td>

                        <td>#PO-{{ $purchaseOrder->reference_code }}</td>
                        <td>{{ $purchaseOrder->supplier?->name ?? '-' }}</td>
                        <td>{{ $purchaseOrder->warehouse?->name ?? '-' }}</td>

                        <td>
                            <div class="flex flex-wrap items-center gap-1">
                                <div
                                    class="px-2 py-1 text-xs text-white rounded-md leading-none capitalize {{ poStatusBadge($purchaseOrder->status) }}">
                                    {{ $purchaseOrder->status }}
                                </div>
                                @if ($returnOrder)
                                    <div
                                        class="px-2 py-1 text-xs text-white rounded-md leading-none capitalize bg-orange-500">
                                        {{ translate('Returned') }}
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td>{{ formatPrice($purchaseOrder->grand_total) }}</td>

                        <td>
                            <span
                                class="px-2 py-1 text-xs {{ $purchaseOrder->payment_status == 'paid' ? 'bg-green-500 ' : 'bg-red-500' }} text-white rounded-md leading-none capitalize">{{ $purchaseOrder->payment_status }}
                            </span>
                        </td>

                        <td>{{ formatPrice($purchaseOrder->paid) }}</td>
                        <td>{{ formatPrice($purchaseOrder->due) }}</td>

                        <td>
                            <div class="option-dropdown w-full" tabindex="0">
                                <div class="option-dropdown__toggler bg-theme-secondary/10 text-theme-secondary">
                                    <span>{{ translate('Actions') }}</span>
                                </div>

                                <div class="option-dropdown__options">
                                    <ul>
                                        @if ($purchaseOrder->status != 'cancelled')
                                            @if (!$returnOrder)
                                                @can('edit_purchase_orders')
                                                    <li>
                                                        <a href="{{ route('admin.purchase-orders.edit', ['purchase_order' => $purchaseOrder->id]) }}"
                                                            class="option-dropdown__option">
                                                            {{ translate('Edit') }}
                                                        </a>
                                                    </li>
                                                @endcan
                                            @endif

                                            @if ($purchaseOrder->status == 'received')
                                                @can('create_return_purchase_orders')
                                                    <li>
                                                        <a href="{{ route('admin.purchase-return.create', ['id' => $purchaseOrder->id]) }}"
                                                            class="option-dropdown__option">
                                                            {{ translate('Purchase Return') }}
                                                        </a>
                                                    </li>
                                                @endcan
                                            @endif


                                            @php
                                                $payments = $purchaseOrder->payments;
                                                $totalPaid = $payments->sum('paid_amount');
                                                $due = $purchaseOrder->due;
                                            @endphp

                                            @if ($due != 0)
                                                @can('make_purchase_order_payments')
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                            class="option-dropdown__option purchase-order-payment-modal"
                                                            data-micromodal-trigger="purchase-order-payment-modal"
                                                            data-payable-id="{{ $purchaseOrder->id }}"
                                                            data-payable-type="App\Models\PurchaseOrder"
                                                            data-payable-amount="{{ $due }}">
                                                            {{ translate('Make Payment') }}
                                                        </a>
                                                    </li>
                                                @endcan
                                            @endif
                                        @endif

                                        @can('show_purchase_order_payments')
                                            <li>
                                                <a href="javascript:void(0);"
                                                    class="option-dropdown__option purchase-order-payment-histories-modal"
                                                    data-payable-id="{{ $purchaseOrder->id }}"
                                                    data-payable-type="App\Models\PurchaseOrder"
                                                    data-micromodal-trigger="purchase-order-payment-histories-modal">
                                                    {{ translate('Show Payments') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('edit_purchase_orders')
                                            @if ($purchaseOrder->status != 'received' && $purchaseOrder->status != 'cancelled')
                                                <x-backend.inputs.delete-link btnText="Cancel Purchase"
                                                    title="Are you sure want to cancel?"
                                                    text="Purchase order will be cancelled & it can not be undone."
                                                    class="text-red-500"
                                                    href="{{ route('admin.purchase-orders.destroy', $purchaseOrder->id) }}" />
                                            @endif
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
            {{ $purchaseOrders->links() }}
        </div>
    </div>
@endsection
