@extends('layouts.admin')

@section('title')
    {{ translate('Orders Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Order details') }}
            </span>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid 3xl:grid-cols-4 gap-10">
        <div class="3xl:col-span-3 space-y-10">
            <div class="card">
                <h4 class="card__title">{{ translate('Order Information') }}</h4>

                <div class="grid lg:grid-cols-2 xl:grid-cols-10 max-lg:divide-y xl:divide-x divide-border">
                    <div class="xl:col-span-3 py-8 px-3 lg:px-6">
                        <p class="text-muted">{{ translate('INVOICE') }}</p>
                        <p class="text-xl ">{{ getSetting('orderCodePrefix') }}{{ $order->order_code }}</p>
                        <x-backend.inputs.link href="{{ route('admin.orders.downloadInvoice', $order->id) }}"
                            variant="secondary" target="_blank" class="mt-3">
                            {{ translate('DOWNLOAD INVOICE') }}
                        </x-backend.inputs.link>

                        <p class="mt-4">
                            <span class="mr-3 text-xs text-muted">{{ translate('Order Created Date') }}:</span>
                            {{ date('d M, Y', strtotime($order->created_at)) }}
                        </p>
                        <p class="mt-4">
                            <span class="mr-3 text-xs text-muted">{{ translate('Order Receiving Date') }}:</span>
                            {{ $order->order_receiving_date ? date('d M, Y', strtotime($order->order_receiving_date)) : '' }}
                        </p>
                        <p class="mt-4">
                            <span class="mr-3 text-xs text-muted">{{ translate('Order Shipment Date') }}:</span>
                            {{ $order->order_shipment_date ? date('d M, Y', strtotime($order->order_shipment_date)) : '' }}
                        </p>

                        <p class="mt-10 mb-5 text-muted">{{ translate('CUSTOMER INFORMATION') }}</p>
                        <table class="w-full [&_td]:py-2">
                            <tr>
                                <td class="text-xs text-muted">{{ translate('Name') }}:</td>
                                <td class="text-sm">{{ $orderGroup->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-muted">{{ translate('Phone') }}:</td>
                                <td class="text-sm">{{ $orderGroup->phone }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-muted">{{ translate('Email') }}:</td>
                                <td class="text-sm">
                                    {{ $orderGroup->email }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="xl:col-span-4 py-8 px-3 lg:px-9">
                        <p class="text-muted mb-6">{{ translate('DELIVERY INFORMATION') }}</p>

                        <div class="space-y-7">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-muted text-xs">{{ translate('BILLING ADDRESS') }}</p>
                                    <a href="javascript:void(0);" data-type="billing_address"
                                        data-address="{{ $orderGroup->billing_address }}" data-id="{{ $order->id }}"
                                        data-micromodal-trigger="order-address-modal"
                                        class="text-base text-theme-secondary order-address-modal">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                </div>
                                <p class=" mb-2.5">{{ $orderGroup->billing_address }}</p>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-muted text-xs">{{ translate('SHIPPING ADDRESS') }}</p>

                                    <a href="javascript:void(0);" data-type="shipping_address"
                                        data-address="{{ $orderGroup->shipping_address }}" data-id="{{ $order->id }}"
                                        data-direction="{{ $orderGroup->direction }}"
                                        data-phone="{{ $orderGroup->phone }}" data-micromodal-trigger="order-address-modal"
                                        class="text-base text-theme-secondary order-address-modal">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                </div>
                                <p class=" mb-2.5">{{ $orderGroup->shipping_address }}</p>
                                <p class="text-muted">{{ $orderGroup->direction }}</p>
                                <p class="text-muted">{{ translate('Phone') }}: {{ $orderGroup->phone }}</p>
                            </div>

                            <table class="[&_td]:py-1">
                                <tr>
                                    <td class="text-xs text-muted">{{ translate('Payment Type') }}:</td>
                                    <td class="text-sm">
                                        <span
                                            class="ms-4">{{ ucfirst(str_replace('_', ' ', $orderGroup->transaction->payment_method)) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="xl:col-span-3 py-8 px-3 lg:px-9 ">
                        <p class="text-muted mb-6">{{ translate('ORDER TRACKING') }}</p>

                        <form action="{{ route('admin.orders.updateOrderTracking') }}" class="space-y-3" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $order->id }}">
                            <div>
                                <x-backend.inputs.text label="Courier Name" :labelInline="false" name="courier_name"
                                    placeholder="Type courier name" value="{{ $order->courier_name }}" />
                            </div>
                            <div>
                                <x-backend.inputs.text label="Tracking Number" :labelInline="false" name="tracking_number"
                                    placeholder="Type tracking number" value="{{ $order->tracking_number }}" />
                            </div>
                            <div>
                                <x-backend.inputs.text label="Tracking URL" :labelInline="false" name="tracking_url"
                                    placeholder="Type tracking url" value="{{ $order->tracking_url }}" />
                            </div>

                            <div>
                                <x-backend.inputs.button type="submit" variant="secondary" buttonText="SAVE DATA" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <h4 class="card__title border-b-0">{{ translate('Product Details') }}</h4>

                <table class="footable">
                    <thead>
                        <tr>
                            <th>{{ translate('PRODUCT') }}</th>
                            <th data-breakpoints="xs sm lg">{{ translate('UNIT PRICE') }}</th>
                            <th class=" text-center">{{ translate('QTY') }}</th>
                            <th data-breakpoints="xs sm lg">{{ translate('Total TAX') }}</th>
                            <th data-breakpoints="xs sm lg">{{ translate('Discount') }}</th>
                            <th data-breakpoints="">{{ translate('SUB TOTAL') }}</th>
                            <th data-breakpoints="" class="text-right">{{ translate('Action') }}</th>
                        </tr>
                    </thead>

                    <tbody class="[&_td]:py-3">
                        @foreach ($orderItems as $item)
                            @php
                                $variation = $item->productVariation;
                                $product = $variation->product;
                            @endphp
                            <tr>
                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="max-xs:hidden border border-border rounded-md p-2 aspect-square">
                                            <img src="{{ uploadedAsset($product->thumbnail_image) }}"
                                                class="w-12 h-12 lg:w-[70px] lg:h-[70px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">
                                        </div>
                                        <div class=" line-clamp-2 max-w-[230px]">
                                            {{ $product->collectTranslation('name') }} @if ($product->has_variation)
                                                -
                                                {{ generateVariationName($variation->code) }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="font-medium">{{ formatPrice($item->unit_price) }}</td>
                                <td class="font-medium text-center">
                                    <input class="theme-input max-w-[50px] px-2 text-center update_qty" type="text"
                                        data-id="{{ $item->id }}" value="{{ $item->qty }}"
                                        @disabled($order->delivery_status == 'delivered' || $order->payment_status == 'paid' ? true : false)>
                                </td>
                                <td class="font-medium">{{ formatPrice($item->total_tax) }}</td>
                                <td class="font-medium">{{ formatPrice($item->total_discount) }}</td>
                                <td class="font-medium">{{ formatPrice($item->total_price) }}</td>
                                <td class="font-medium text-right">
                                    @if ($order->delivery_status != 'delivered' && $order->payment_status != 'paid')
                                        <a class="text-red-400 hover:text-rose-600 confirm-modal"
                                            href="javascript:void(0);"
                                            data-href="{{ route('admin.orders.removeOrderItem', $item->id) }}"
                                            data-title="{{ translate('Are you sure you want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="DELETE" data-micromodal-trigger="confirm-modal">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="ms-4 md:ms-12 my-3">
                    {{-- @if ($order->delivery_status != 'delivered' && $order->payment_status != 'paid')
                        <a href="javascript:void(0);" class="text-theme-secondary-light update-order-modal"
                            data-micromodal-trigger="update-order-modal">
                            {{ translate('Add More Products') }}
                            <span class="ml-2">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </a>
                    @endif --}}

                    {{-- modal --}}
                    <div class="modal micromodal-slide" id="update-order-modal" aria-hidden="true">
                        <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
                        <div class="modal__container max-w-max min-w-[800px]" role="dialog" aria-modal="true"
                            aria-labelledby="update-order-modal-title">
                            <header class="modal__header">
                                <h2 class="modal__title">
                                    {{ translate('Add Products To Order') }}
                                </h2>
                                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                                    data-micromodal-close></button>
                            </header>
                            <main class="modal__content min-h-[180px] w-full">
                                <div id="update-order">
                                    <x-backend.forms.update-order-form :order="$order" />
                                </div>
                            </main>
                        </div>
                    </div>
                    {{-- modal ends --}}

                </div>

                <div class="max-w-[300px] space-y-4 md:space-y-6 ml-auto pr-4 lg:pr-6 xl:pr-12">
                    <div class="flex justify-between uppercase">
                        <span>{{ translate('Subtotal') }}</span>
                        <span>{{ formatPrice($order->amount) }}</span>
                    </div>

                    <div class="flex justify-between uppercase">
                        <span>{{ translate('TAX') }}</span>
                        <span>{{ formatPrice($order->tax_amount) }}</span>
                    </div>
                    <div class="flex justify-between uppercase">
                        <span>{{ translate('Delivery charge') }}</span>
                        <span>{{ formatPrice($order->shipping_charge_amount) }}</span>
                    </div>
                    <div class="flex justify-between uppercase">
                        <span>{{ translate('Discount') }}</span>
                        <span>{{ formatPrice($order->discount_amount) }}</span>
                    </div>
                    @if ($order->coupon_id)
                        <div class="flex justify-between uppercase">
                            <span>{{ translate('Coupon Discount') }}</span>
                            <span>{{ formatPrice($order->coupon_discount_amount) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between uppercase">
                        <span>{{ translate('Total') }}</span>
                        <span class="text-base font-bold text-theme-secondary">
                            {{ formatPrice($order->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between uppercase">
                        <span>{{ translate('Advance') }}</span>
                        <span class="text-base font-bold text-green-500">
                            {{ formatPrice($order->advance_payment) }}</span>
                    </div>
                    <div class="flex justify-between uppercase">
                        <span>{{ translate('Due') }}</span>
                        <span class="text-base font-bold text-red-500">
                            {{ formatPrice($order->total_amount - $order->advance_payment) }}</span>
                    </div>
                </div>

                <br>
                <br>
            </div>
        </div>

        <div class="col-span-1">
            <div class="card">
                <h4 class="card__title">{{ translate('Order Updates') }}</h4>

                {{-- order status --}}
                <div class="card__content py-6">
                    <x-backend.inputs.select label="Payment Status" name="status" wrapperClass="col-span-1"
                        groupClass="grid-cols-2 mb-2" id="update_payment_status">
                        <x-backend.inputs.select-option value="paid" selected="{{ $order->payment_status }}"
                            name="Paid" />
                        <x-backend.inputs.select-option value="unpaid" selected="{{ $order->payment_status }}"
                            name="Unpaid" />
                    </x-backend.inputs.select>

                    <x-backend.inputs.select label="Delivery Status" name="status" wrapperClass="col-span-1"
                        groupClass="grid-cols-2 mb-2" id="update_delivery_status">
                        <x-backend.inputs.select-option value="order_placed" selected="{{ $order->delivery_status }}"
                            name="Order Placed" />
                        <x-backend.inputs.select-option value="processing" selected="{{ $order->delivery_status }}"
                            name="Processing" />
                        <x-backend.inputs.select-option value="confirmed" selected="{{ $order->delivery_status }}"
                            name="Confirmed" />
                        <x-backend.inputs.select-option value="shipped" selected="{{ $order->delivery_status }}"
                            name="Shipped" />
                        <x-backend.inputs.select-option value="delivered" selected="{{ $order->delivery_status }}"
                            name="Delivered" />
                        <x-backend.inputs.select-option value="cancelled" selected="{{ $order->delivery_status }}"
                            name="Cancelled" />

                    </x-backend.inputs.select>
                </div>

                {{-- add note --}}
                <div class="card__content py-6 border-t border-border">
                    <form action="{{ route('admin.orders.storeOrderUpdates') }}" class="space-y-3" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $order->id }}">
                        <div>
                            <label class="mb-2.5">{{ translate('Title') }}</label>
                            <x-backend.inputs.text name="status" placeholder="Title" />
                        </div>
                        <div>
                            <label class="mb-2.5">{{ translate('Text') }}</label>
                            <x-backend.inputs.textarea name="note" placeholder="Text" rows="2" />
                        </div>

                        <div>
                            <x-backend.inputs.button type="submit" variant="secondary">
                                {{ translate('PUBLISH NOTE') }}
                            </x-backend.inputs.button>
                    </form>
                </div>

                {{-- previous notes --}}
                <h5 class="text-muted my-8">{{ translate('PREVIOUS NOTES') }}</h5>
                <div class="flex flex-col">
                    @foreach ($orderUpdates as $note)
                        <div class="relative flex gap-4 pb-9 [&:last-child>span]:hidden"><span
                                class="absolute left-[5px] z-0 h-full border-l border-border border-dashed"></span>
                            <div><span class="relative w-3 h-3 inline-block rounded-full bg-theme-secondary-light"></span>
                            </div>
                            <div class="grow">
                                <div class="flex items-center justify-between">
                                    <h6 class=" font-public-sans mb-1 capitalize">{{ $note->status }}</h6>
                                    @if ($note->type != 'default')
                                        <a href="javascript:void(0);"
                                            data-href="{{ route('admin.orders.deleteOrderUpdate', $note->id) }}"
                                            data-title="{{ translate('Are you sure want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="POST" data-micromodal-trigger="confirm-modal"
                                            class="text-base text-theme-alert confirm-modal">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    @endif
                                </div>

                                <p class="text-muted text-xs mb-4">{{ $note->note }}</p>

                                <time class="text-xs">
                                    {{ date('d M, Y', strtotime($note->created_at)) }}

                                    <span class="text-muted inline-block ml-1.5 uppercase">
                                        {{ date('h:i A', strtotime($note->created_at)) }}
                                    </span>
                                </time>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        "use strict";
        // payment status
        $('#update_payment_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('admin.orders.updatePaymentStatus') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                notifyMe('success', '{{ translate('Payment status has been updated') }}');
                window.location.reload();
            });
        });

        // delivery status 
        $('#update_delivery_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            $.post('{{ route('admin.orders.updateDeliveryStatus') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                notifyMe('success', '{{ translate('Delivery status has been updated') }}');
                window.location.reload();
            });
        });
    </script>
    <x-backend.inc.update-order-scripts />
@endsection
