<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('INVOICE') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;500&family=Hanuman:wght@300;400;700&family=Hind+Siliguri:wght@400;500&family=Kanit:wght@400;500&family=Open+Sans:wght@400;500&family=Roboto:wght@400;500&display=swap');

        * {
            box-sizing: border-box;

        }

        body {
            font-size: 13px;
            font-family: '<?php echo $font_family; ?>' !important;
            direction: <?php echo $direction; ?>;
            text-align: <?php echo $default_text_align; ?>;
        }


        pre,
        p {
            padding: 0;
            margin: 0;

        }

        table {
            border-collapse: collapse;
        }

        td,
        th {
            text-align: left;

        }

        .visibleMobile {
            display: none;

        }

        .hiddenMobile {
            display: block;

        }

        .text-end {
            text-align: right;
        }


        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .container {
            padding-top: 20px;
            background-color: #fff;
        }

        .billing-info {
            padding-right: 20px;
        }

        .billing-info p {
            margin-bottom: 5px;
        }

        .shipping-info {
            padding-left: 20px;
        }

        .shipping-info p {
            margin-bottom: 5px;
        }

        .table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .table th {
            border: 1px solid #ccc;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .product-table th,
        .product-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .product-table img {
            height: 50px;
        }

        .totals-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .totals-table th,
        .totals-table td {
            padding: 8px;
            text-align: left;
        }

        .totals-table th {
            width: 60%;
            font-weight: normal;
        }

        .totals-table td {
            width: 40%;
            text-align: right;
        }

        hr {
            margin-bottom: 10px;
            border: none;
            border-top: 1px solid #f0f0f0;
        }

        .border-label {
            border: 1px solid green;
            padding: 5px;
            width: 90px;
            text-align: center;
        }

        .text-muted {
            color: #5a5a5a;
        }
    </style>
</head>

<body>
    @php
        $orderGroup = $order->orderGroup;
        $user = $order->user;
    @endphp

    <div class="billing-info">
        <div style="margin-bottom: 16px;">
            <div style="float: left; width: 50%;">
                <img src="{{ uploadedAsset(getSetting('websiteHeaderLogo')) }}"
                    height="70" alt="Logo">
            </div>
            <div style="float: left; width: 50%;">
                <p style="font-size: 30px; margin: 0;">MEMO</p>
                <p style="font-size: 16px; margin: 0;">Invoice No:
                    {{ getSetting('order_code_prefix') }}{{ $order->order_code }}</p>
            </div>
        </div>

        <div>
            <div style="float: left; width: 50%;">
                <div class="border-label" style="margin-bottom:10px; width: 80px;">
                    Billing From
                </div>

                <p style="margin-bottom:5px;">{{ getSetting('systemName') }}</p>
                <p style="margin-bottom:5px;">{{ getSetting('systemAddress') }}</p>
                <p style="margin-bottom:5px;">Mobile: {{ getSetting('systemPhone') }}</p>
                <p style="margin-bottom:5px;">Email: {{ getSetting('systemEmail') }}</p>
                <p style="margin-bottom:5px;">Website:
                    <a href="{{ env('APP_URL') }}">{{ env('APP_URL') }}</a>
                </p>
            </div>
            <div style="float: left; width: 50%;">
                <p style="margin-bottom:5px;">
                    Billing Date:
                    {{ date('d M, Y', strtotime($order->order_receiving_date)) }}
                </p>

                <p class="border-label" style="margin-bottom:5px; width: 60px;">Ship To</p>
                <p style="margin-bottom:5px;">Name: {{ $user?->name ?? '' }}</p>
                <p style="margin-bottom:5px;">Phone: {{ $orderGroup->phone ?? '' }}</p>
                <p style="margin-bottom:5px;">
                    Address: {{ $orderGroup->shipping_address }}
                </p>
                <p style="margin-bottom:5px;">
                    Direction: {{ $orderGroup->direction }}
                </p>
            </div>
        </div>
    </div>

    <hr>

    <div style="margin-bottom: 20px;">
        <div style="float:left; width: 60%;">
            Delivery Type: Cash On Delivery
        </div>
        <div style="float:left; width: 40%;">
            <p style="margin-bottom: 5px; text-transform: capitalize;">Payment Status: {{ $order->payment_status }}</p>
            <p>Payment Method: Cash On Delivery</p>
        </div>
    </div>

    <div>
        <table class="product-table">
            <thead>
                <tr>
                    <th width="5%">SL.</th>
                    <th width="5%">Image</th>
                    <th width="50%">Product</th>
                    <th width="10%">Quantity</th>
                    <th width="15%">Rate</th>
                    <th width="15%">Amount</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($order->orderItems as $key => $item)
                    @php
                        $product = $item->productVariation->product;
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>
                            <img height="60"
                                src="{{ $product->thumbnail_image ? uploadedAsset($product->thumbnail_image) : asset('images/image-error.png') }}"
                                alt="Product Image">
                        </td>
                        <td>
                            <p>{{ $product->CollectTranslation('name') }}</p>
                            <p class="text-muted">
                                @foreach (generateVariationCombinations($item->productVariation->combinations) as $variation)
                                    <span class="fs-xs">
                                        {{ $variation['name'] }}:
                                        @foreach ($variation['values'] as $value)
                                            {{ $value['name'] }}
                                        @endforeach
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    </span>
                                @endforeach
                            </p>
                        </td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ formatPrice($item->unit_price) }}</td>
                        <td>{{ formatPrice($item->unit_price * $item->qty) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px; margin-bottom: 30px;">
        <div style="float:left; width:70%;">
            <br>
        </div>
        <div style="float:left; width:30%;">
            <table style="width: 100%;">
                <tr>
                    <td style="padding-bottom: 10px;">Sub Total:</td>
                    <td style="text-align: right;">{{ formatPrice($order->amount) }}</td>
                </tr>
                {{-- <tr>
                    <td style="padding-bottom: 10px;">Tax:</td>
                    <td style="text-align: right;">{{ formatPrice($order->tax_amount) }}</td>
                </tr> --}}
                <tr>
                    <td style="padding-bottom: 10px;">Total Discount:</td>
                    <td style="text-align: right;">{{ formatPrice($order->discount_amount) }}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">Shipping Cost:</td>
                    <td style="text-align: right;">{{ formatPrice($order->shipping_charge_amount) }}</td>
                </tr>
                @if ($order->coupon_discount_amount > 0)
                    <tr class="">
                        <td>
                            {{ translate('Coupon Discount') }}</td>
                        <td style="text-align: right;">
                            {{ formatPrice($order->coupon_discount_amount) }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding-bottom: 10px;">Grand Total:</td>
                    <td style="text-align: right;">{{ formatPrice($order->total_amount) }}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">Advance:</td>
                    <td style="text-align: right;">{{ formatPrice($order->advance_payment) }}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">Due:</td>
                    <td style="text-align: right;">
                        {{ formatPrice($order->total_amount - $order->advance_payment) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <hr>
    <div style="padding: 10px 0px;">
        <div style="float:left; width: 50%;">Received By</div>
        <div style="float:left; width: 50%;">Authorized By</div>
    </div>
    <hr>

</body>

</html>
