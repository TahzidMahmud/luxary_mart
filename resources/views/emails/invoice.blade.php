<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ getSetting('systemName') }}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <style media="all">
        @font-face {
            font-family: 'Roboto';
            src: url("{{ asset('fonts/Roboto-Regular.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-size: 0.75rem;
            font-family: 'Roboto';
            font-weight: normal;
            direction: ltr;
            text-align: left;
            padding: 0;
            margin: 0;
            color: #232323;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: 0 .8rem;
        }

        table.padding td {
            padding: .8rem;
        }

        table.sm-padding td {
            padding: .5rem .7rem;
        }

        table.lg-padding td {
            padding: 1rem 1.2rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold
        }
    </style>
</head>

<body>
    <div style="max-width: 650px;padding:20px;margin:0 auto">
        <div style="padding:0px 19px;">
            <table>
                <thead>
                    <tr>
                        <th width="50%"></th>
                        <th width="50%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <img src="{{ uploadedAsset(getSetting('websiteHeaderLogo')) }}"
                                                height="30" style="display:inline-block;margin-bottom:10px">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="" class="bold">{{ getSetting('systemName') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="">{{ getSetting('systemAddress') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="">{{ translate('Email') }}:
                                            {{ getSetting('systemEmail') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="">{{ translate('Phone') }}:
                                            {{ getSetting('systemPhone') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="text-right">
                                <tbody>
                                    <tr>
                                        <td style="font-size: 2rem;" class="bold">{{ translate('INVOICE') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="">
                                            <span class=" ">{{ translate('Order Date') }}:</span>
                                            <span
                                                class="bold">{{ date('d M, Y', strtotime($orderGroup->created_at)) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class=" ">{{ translate('Delivery type') }}:</span>
                                            <span class="bold"
                                                style="text-transform: capitalize">{{ translate('Standard') }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin:8px 8px 15px 8px; clear:both">
            <div style="padding:10px 14px; border:1px solid #DEDEDE;border-radius:3px;width:45%;float:left;">
                <table class="">
                    <tbody>

                        <tr>
                            <td class="bold">{{ translate('Billing address') }}:</td>
                        </tr>
                        <tr>
                            <td class="">{{ $orderGroup->billing_address }}</td>
                        </tr>
                        <tr>
                            <td class="">{{ translate('Phone') }}: {{ $orderGroup->phone }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding:10px 14px; border:1px solid #DEDEDE;border-radius:3px;width:45%;float:right">
                <table class="text-right">
                    <tbody>

                        <tr>
                            <td class="bold">{{ translate('Shipping address') }}:</td>
                        </tr>
                        <tr>
                            <td class="">{{ $orderGroup->shipping_address }}</td>
                        </tr>
                        <tr>
                            <td class="">{{ translate('Phone') }}: {{ $orderGroup->phone }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin:0 8px;border:1px solid #DEDEDE;border-radius:3px;padding:0 7px">
            <table class="padding">
                <thead>
                    <tr>
                        <td width="50%" class="text-left bold">{{ translate('Product') }}</td>
                        <td width="10%" class="text-left bold">{{ translate('Qty') }}</td>
                        <td width="10%" class="text-left bold">{{ translate('U.Price') }}</td>
                        <td width="10%" class="text-left bold">{{ translate('U.Tax') }}</td>
                        <td width="10%" class="text-left bold">{{ translate('U.Discount') }}</td>
                        <td width="10%" class="text-right bold">{{ translate('Total') }}</td>
                    </tr>
                </thead>
            </table>
        </div>
        <div style="margin:8px;">
            <table class="lg-padding" style="border-collapse: collapse">
                <tr>
                    <th width="35%" class="text-left"></th>
                    <th width="10%" class="text-left"></th>
                    <th width="10%" class="text-left"></th>
                    <th width="10%" class="text-left"></th>
                    <th width="10%" class="text-right"></th>
                    <th width="10%" class="text-right"></th>
                </tr>
                <tbody class="strong">
                    @foreach ($orderGroup->orders as $order)
                        @foreach ($order->orderItems as $key => $item)
                            @php
                                $product = $item->productVariation->product;
                            @endphp
                            @if ($product != null)
                                <tr>
                                    <td style="border-bottom:1px solid #DEDEDE;">
                                        <span style="display: block">{{ $product->CollectTranslation('name') }}</span>
                                        @foreach (generateVariationCombinations($item->productVariation->combinations) as $variation)
                                            <span style="margin-right:10px">
                                                <span class="">{{ $variation['name'] }}</span>:
                                                @foreach ($variation['values'] as $value)
                                                    {{ $value['name'] }}
                                                @endforeach
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="" style="border-bottom:1px solid #DEDEDE;">
                                        {{ $item->qty }}</td>
                                    <td class="" style="border-bottom:1px solid #DEDEDE;">
                                        {{ formatPrice($item->unit_price) }}</td>
                                    <td class="" style="border-bottom:1px solid #DEDEDE;">
                                        {{ formatPrice($item->total_tax / $item->qty) }}</td>
                                    <td class="text-right bold"
                                        style="border-bottom:1px solid #DEDEDE;padding-right:20px;">
                                        {{ formatPrice($item->total_discount / $item->qty) }}</td>
                                    <td class="text-right bold"
                                        style="border-bottom:1px solid #DEDEDE;padding-right:20px;">
                                        {{ formatPrice($item->total_price) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
            $transaction = $orderGroup->transaction;
        @endphp
        <div style="margin:15px 8px;clear:both">
            <div style="float: left; width:43%;padding:14px 20px;">
            </div>
            <div style="float: right; width:43%;padding:14px 20px; border:1px solid #DEDEDE;border-radius:3px;">
                <table class="text-right sm-padding" style="border-collapse:collapse">
                    <tbody>
                        <tr>
                            <td class="text-left" style="border-bottom:1px dotted #B8B8B8">
                                {{ translate('Sub Total') }}</td>
                            <td class="bold" style="border-bottom:1px dotted #B8B8B8">
                                {{ formatPrice($transaction->amount) }}</td>
                        </tr>
                        <tr class="">
                            <td class="text-left" style="border-bottom:1px dotted #B8B8B8">
                                {{ translate('Total Tax') }}</td>
                            <td class="bold" style="border-bottom:1px dotted #B8B8B8">
                                {{ formatPrice($transaction->tax_amount) }}</td>
                        </tr>
                        <tr>
                            <td class="text-left" style="border-bottom:1px dotted #B8B8B8">
                                {{ translate('Shipping Cost') }}</td>
                            <td class="bold" style="border-bottom:1px dotted #B8B8B8">
                                {{ formatPrice($transaction->shipping_charge_amount) }}</td>
                        </tr>
                        <tr>
                            <td class="text-left" style="border-bottom:1px dotted #B8B8B8">
                                {{ translate('Discount') }}</td>
                            <td class="bold" style="border-bottom:1px dotted #B8B8B8">
                                {{ formatPrice($transaction->discount_amount) }}</td>
                        </tr>

                        <tr class="">
                            <td class="text-left" style="border-bottom:1px solid #DEDEDE">
                                {{ translate('Coupon Discount') }}</td>
                            <td class="bold" style="border-bottom:1px solid #DEDEDE">
                                {{ formatPrice($transaction->coupon_discount_amount) }}</td>
                        </tr>
                        <tr>
                            <td class="text-left bold">{{ translate('Grand Total') }}</td>
                            <td class="bold">{{ formatPrice($transaction->total_amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>
