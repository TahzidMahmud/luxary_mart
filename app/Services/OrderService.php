<?php

namespace App\Services;

use App\Models\CommissionHistory;
use App\Models\Language;
use App\Models\ModeratorCommission;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use Config;
use PDF;

class OrderService
{
    # get shop orders
    public function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $orders = Order::shopOrders()->latest();

        // filter by delivery status
        if ($request->deliveryStatus != null) {
            $orders->where('delivery_status', $request->deliveryStatus);
        }

        // filter by payment status
        if ($request->paymentStatus != null) {
            $orders->where('payment_status', $request->paymentStatus);
        }

        if ($request->search != null) {
            $searchKey = $request->search;
            $orders = $orders->whereHas('user', function ($q) use ($searchKey) {
                $q->where('name', 'like', '%' . $searchKey . '%');
            })->orWhere('order_code',  'like', '%' .  $searchKey  . '%');
        }

        if ($request->customerId != null) {
            $orders->where('user_id', $request->customerId);
        }

        $orders = $orders->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'searchKey'     => $searchKey,
                'orders'        => $orders
            ],
        ];

        return $data;
    }

    # show orders
    public function show($code)
    {
        try {
            Notification::where('link_info', $code)->where('type', 'order')->update([
                'is_read' => 1
            ]);
        } catch (\Throwable $th) {
        }

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [],
        ];

        $order = Order::shopOrders()->whereOrderCode($code)->first();
        if (is_null($order)) {
            $data = [
                'status'    => 404,
                'message'   => translate('Order not found'),
                'result'    => [],
            ];
            return $data;
        }

        $orderGroup = $order->orderGroup;
        $orderUpdates = $order->orderUpdates;
        $orderItems = $order->orderItems;

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'order'         => $order,
                'orderGroup'    => $orderGroup,
                'orderUpdates'  => $orderUpdates,
                'orderItems'    => $orderItems
            ],
        ];

        return $data;
    }

    # download invoice
    public function downloadInvoice($id)
    {
        if (session()->has('locale')) {
            $language_code = session()->get('locale', Config::get('app.locale'));
        } else {
            $language_code = env('DEFAULT_LANGUAGE');
        }

        if (getSetting('currencyCode')) {
            $currency_code = getSetting('currencyCode');
        } else {
            $currency_code = env('DEFAULT_CURRENCY');
        }

        if (Language::where('code', $language_code)->first()->is_rtl == 1) {
            $direction = 'rtl';
            $default_text_align = 'right';
            $reverse_text_align = 'left';
        } else {
            $direction = 'ltr';
            $default_text_align = 'left';
            $reverse_text_align = 'right';
        }

        if ($currency_code == 'BDT' || $currency_code == 'bdt' || $language_code == 'bd' || $language_code == 'bn') {
            # bengali font
            $font_family = "'Hind Siliguri','freeserif'";
        } elseif ($currency_code == 'KHR' || $language_code == 'kh') {
            # khmer font
            $font_family = "'Khmeros','sans-serif'";
        } elseif ($currency_code == 'AMD') {
            # Armenia font
            $font_family = "'arnamu','sans-serif'";
        } elseif ($currency_code == 'AED' || $currency_code == 'EGP' || $language_code == 'sa' || $currency_code == 'IQD' || $language_code == 'ir') {
            # middle east/arabic font
            $font_family = "'XBRiyaz','sans-serif'";
        } else {
            # general for all
            $font_family = "'Roboto','sans-serif'";
        }

        $order = Order::shopOrders()->whereId((int)$id)->first();
        return PDF::loadView('backend.admin.orders.invoice', [
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'default_text_align' => $default_text_align,
            'reverse_text_align' => $reverse_text_align
        ], [], [])->stream(getSetting('order_code_prefix') . $order->order_code . '.pdf');
    }

    # store new order updates
    public function storeOrderUpdates($request)
    {
        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [],
        ];

        $order = Order::shopOrders()->whereId($request->id)->first();

        if (is_null($order)) {
            $data = [
                'status'    => 404,
                'message'   => translate('Order not found'),
                'result'    => [],
            ];
            return $data;
        }

        // order update
        OrderUpdate::create([
            'order_id'      => $order->id,
            'user_id'       => userId(),
            'type'          => 'custom',
            'status'        => $request->status,
            'note'          => $request->note,
            'created_by'    => userId(),
        ]);

        $data = [
            'status'    => 200,
            'message'   => translate('Order note published successfully'),
            'result'    => [],
        ];

        return $data;
    }

    # update order tracking
    public function updateOrderTracking($request)
    {
        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [],
        ];

        $order = Order::shopOrders()->whereId($request->id)->first();

        if (is_null($order)) {
            $data = [
                'status'    => 404,
                'message'   => translate('Order not found'),
                'result'    => [],
            ];
            return $data;
        }

        $order->courier_name    = $request->courier_name;
        $order->tracking_number = $request->tracking_number;
        $order->tracking_url    = $request->tracking_url;
        $order->save();

        $data = [
            'status'    => 200,
            'message'   => translate('Order tracking info updated successfully'),
            'result'    => [],
        ];

        return $data;
    }

    # delete order update
    public function deleteOrderUpdate($id)
    {
        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [],
        ];

        $orderUpdate = OrderUpdate::whereId($id)->first();
        if (is_null($orderUpdate)) {
            $data = [
                'status'    => 404,
                'message'   => translate('Note not found'),
                'result'    => [],
            ];
            return $data;
        }
        if (!$orderUpdate->order || $orderUpdate->order->shop_id != shopId()) {
            $data = [
                'status'    => 404,
                'message'   => translate('This order does not belongs to you'),
                'result'    => [],
            ];
            return $data;
        }

        $orderUpdate->delete();

        $data = [
            'status'    => 200,
            'message'   => translate('Order note deleted successfully'),
            'result'    => [],
        ];
        return $data;
    }

    # update payment status 
    public function updatePaymentStatus($request)
    {
        $order = Order::shopOrders()->whereId((int)$request->order_id)->first();
        $order->payment_status = $request->status;
        $order->save();

        $this->addCommissionHistory($order);

        if (user()->user_type == "seller" && $order->delivery_status != "cancelled") {
            if ($request->status == "paid") {
                $this->addShopBalance($order);
            } else {
                $this->reduceShopBalance($order);
            }
        }

        OrderUpdate::create([
            'order_id'      => $order->id,
            'user_id'       => userId(),
            'status'        => ucwords(str_replace('_', ' ', $request->status)),
            'note'          => 'Payment status updated to ' . ucwords(str_replace('_', ' ', $request->status)) . '.',
            'created_by'    => userId(),
        ]);

        // todo::['mail notification']
        $data = [
            'status'    => 200,
            'message'   => translate('Payment status updated successfully'),
            'result'    => [],
        ];

        return $data;
    }

    # update delivery status
    public function updateDeliveryStatus($request)
    {
        $order      = Order::shopOrders()->whereId((int)$request->order_id)->first();

        $createdBy  = $order->createdBy;
        $orderItems = $order->orderItems;

        $this->addCommissionHistory($order);

        if ($order->delivery_status != "confirmed" && $request->status == "confirmed") {

            try {
                $orderUser = $order->user;
                if ($orderUser) {
                    sendSMSViaBulkSmsBd($orderUser->phone, "Your order has been Confirmed. Your invoice number: " . $order->order_code . ". " . env('APP_NAME'));
                }
            } catch (\Exception $e) {
                //dd($e->getMessage());
            }
        }

        if ($order->delivery_status != "cancelled" && $request->status == "cancelled") {
            $this->addQtyToStock($order);

            if (user()->user_type == "seller") {
                $this->reduceShopBalance($order);
            }
        }

        if ($order->delivery_status == "cancelled" && $request->status != "cancelled") {
            $this->removeQtyFromStock($order);

            if (user()->user_type == "seller") {
                $this->addShopBalance($order);
            }
        }

        if ($order->delivery_status != "delivered" && $request->status == "delivered") {
            if ($createdBy && $createdBy->hasRole('Moderator')) {
                foreach ($orderItems as $orderItem) {
                    $productVariation   = $orderItem->productVariation;
                    $product            = $productVariation->product;

                    // moderator commissions 
                    $moderatorCommission                        = new ModeratorCommission;
                    $moderatorCommission->user_id               = $createdBy->id;
                    $moderatorCommission->product_id            = $product->id;
                    $moderatorCommission->product_variation_id  = $productVariation->id;
                    $moderatorCommission->invoice_no            = $order->order_code;
                    $moderatorCommission->total_amount          = $orderItem->total_price;
                    $moderatorCommission->commission_rate       = $product->commission_rate;
                    $moderatorCommission->commission_amount     = (float) ($product->commission_rate * $orderItem->total_price) / 100;
                    $moderatorCommission->due_amount            = $moderatorCommission->commission_amount;
                    $moderatorCommission->save();
                }
            }
        }

        if ($order->delivery_status == "delivered" && $request->status != "delivered") {
            // remove all commissions if order is not delivered
            ModeratorCommission::where('invoice_no', $order->order_code)->delete();
        }

        $order->delivery_status = $request->status;
        $order->save();

        OrderUpdate::create([
            'order_id'      => $order->id,
            'user_id'       => userId(),
            'status'        => ucwords(str_replace('_', ' ', $request->status)),
            'created_by'    => userId(),
            'note'          => 'Delivery status updated to ' . ucwords(str_replace('_', ' ', $request->status)) . '.',
        ]);

        // todo::['mail notification']
        $data = [
            'status'    => 200,
            'message'   => translate('Delivery status updated successfully'),
            'result'    => [],
        ];

        return $data;
    }

    # updateOrderAddress
    public function updateOrderAddress($request)
    {
        $order = Order::shopOrders()->whereId((int)$request->id)->first();

        if (is_null($order)) {
            $data = [
                'status'    => 404,
                'message'   => translate('Order not found'),
                'result'    => [],
            ];
            return $data;
        }

        $orderGroup = $order->orderGroup;
        if ($request->type == "shipping_address") {
            $orderGroup->shipping_address = $request->address;
            $orderGroup->direction = $request->direction;
            $orderGroup->phone = $request->phone;
        } else {
            $orderGroup->billing_address = $request->address;
        }
        $orderGroup->save();

        $data = [
            'status'    => 200,
            'message'   => translate('Address updated successfully'),
            'result'    => [],
        ];

        return $data;
    }


    # add qty to stock 
    public function addQtyToStock($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $stock = $orderItem->productVariation->productVariationStocks()->where('warehouse_id', $order->warehouse_id)->first();
            if ($stock) {
                $stock->stock_qty += $orderItem->qty;
                $stock->save();
            }

            $product = $orderItem->productVariation->product;
            $product->total_sale_count -= $orderItem->qty;
            $product->save();

            // todo:: handle category, brands sale count & amount
            // if ($product->categories()->count() > 0) {
            //     foreach ($product->categories as $category) {
            //         $category->total_sale_count -= $orderItem->qty;
            //         $category->save();
            //     }
            // }
        }
    }

    # remove qty from stock  
    public function removeQtyFromStock($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $stock = $orderItem->productVariation->productVariationStocks()->where('warehouse_id', $order->warehouse_id)->first();
            if ($stock) {
                $stock->stock_qty -= $orderItem->qty;
                $stock->save();
            }

            $product = $orderItem->productVariation->product;
            $product->total_sale_count += $orderItem->qty;
            $product->save();
            // todo:: handle category, brads sale count & amount
            // if ($product->categories()->count() > 0) {
            //     foreach ($product->categories as $category) {
            //         $category->total_sale_count += $orderItem->qty;
            //         $category->save();
            //     }
            // }
        }
    }

    # reduceShopBalance
    public function reduceShopBalance($order)
    {
        $commission = $order->commissionHistory;
        $shop = $order->shop;
        $shop->current_balance -= $commission->shop_earning_amount;
        $shop->save();
    }

    # addShopBalance
    public function addShopBalance($order)
    {
        $commission = $order->commissionHistory;
        $shop = $order->shop;
        $shop->current_balance += $commission->shop_earning_amount;
        $shop->save();
    }

    # addShopBalance
    public function addCommissionHistory($order)
    {
        $commission = $order->commissionHistory;
        if (is_null($commission)) {
            $shop = $order->shop;

            $adminCommissionRate = $shop->admin_commission_percentage;
            $adminCommission     = 0;
            $sellerEarning       = $order->total_amount;

            if ($adminCommissionRate > 0) {
                $adminCommission = ($adminCommissionRate * $sellerEarning) / 100;
                $sellerEarning   -= $adminCommission;
            }

            $commission                                 = new CommissionHistory();
            $commission->shop_id                        = $shop->id;
            $commission->order_id                       = $order->id;
            $commission->admin_commission_percentage    = $adminCommissionRate;
            $commission->amount                         = $order->total_amount;
            $commission->admin_earning_amount           = $adminCommission;
            $commission->shop_earning_amount            = $sellerEarning;
            $commission->save();
        }
    }

    # removeOrderItem
    public function removeOrderItem($id)
    {
        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [],
        ];

        $orderItem  = OrderItem::whereId($id)->first();

        if (is_null($orderItem)) {
            $data = [
                'status'    => 404,
                'message'   => translate('Order item not found'),
                'result'    => [],
            ];
            return $data;
        }

        $order          = $orderItem->order;
        $orderGroup     = $order->orderGroup;
        $transaction    = $orderGroup->transaction;

        # transaction
        $transaction->amount -= $orderItem->total_price;
        $transaction->total_amount -= $orderItem->total_price;
        $transaction->save();

        # order
        $order->amount          -= $orderItem->total_price;
        $order->total_amount    -= $orderItem->total_price;
        $order->save();

        # stock
        $stock = $orderItem->productVariation->productVariationStocks()->where('warehouse_id', $order->warehouse_id)->first();
        $stock->stock_qty += $orderItem->qty;
        $stock->save();

        # product sales
        $product = $orderItem->productVariation->product;
        $product->total_sale_count -= $orderItem->qty;
        $product->save();

        if ($order->delivery_status == "delivered") {
            $data = [
                'status'    => 404,
                'message'   => translate('Can not remove item from delivered order'),
                'result'    => [],
            ];
            return $data;
        }

        if ($order->payment_status == "paid") {
            $data = [
                'status'    => 404,
                'message'   => translate('Can not remove item from paid order'),
                'result'    => [],
            ];
            return $data;
        }

        OrderUpdate::create([
            'order_id'      => $order->id,
            'user_id'       => userId(),
            'status'        => 'Removed',
            'created_by'    => userId(),
            'note'          => $product->name . ' (x' . $orderItem->qty . ') ' . 'has been removed from order.',
        ]);

        $orderItem->delete();

        $data = [
            'status'    => 200,
            'message'   => translate('Order item removed successfully'),
            'result'    => [],
        ];
        return $data;
    }


    # updateQty 
    public function updateQty($request)
    {
        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [],
        ];

        $orderItem  = OrderItem::whereId($request->id)->first();

        if (is_null($orderItem)) {
            $data = [
                'status'    => 404,
                'message'   => translate('Order item not found'),
                'result'    => [],
            ];
            return $data;
        }

        $oldOrderItem   = clone ($orderItem);
        $order          = $orderItem->order;
        $orderGroup     = $order->orderGroup;
        $transaction    = $orderGroup->transaction;

        $orderItem->qty         = $request->qty;
        $orderItem->total_price = $request->qty * $orderItem->unit_price;

        $addition   = true;
        $priceDiff  = 0;
        if ($oldOrderItem->qty > $orderItem->qty) {
            $priceDiff  = $oldOrderItem->total_price - $orderItem->total_price;
            $addition   = false;
        } else {
            $priceDiff  = $orderItem->total_price - $oldOrderItem->total_price;
        }


        $stock = $orderItem->productVariation->productVariationStocks()->where('warehouse_id', $order->warehouse_id)->first();

        $productVariation = $orderItem->productVariation;
        $product = $productVariation->product;

        if (!$stock) {
            return response()->json([
                'success'   => false,
                'status'    => 403,
                'message'   => $product->collectTranslation('name') . ' ' . translate('has limited or no stock'),
                'result'    => null
            ], 403);
        }
        if ($addition) {
            // check stock
            $stockDiff = $request->qty - $oldOrderItem->qty;

            if (!$stock || $stock->stock_qty < $stockDiff) {
                // notification
                Notification::create([
                    'shop_id'   => $product->shop_id,
                    'for'       => 'shop',
                    'type'      => 'stock',
                    'text'      => 'SKU - ' . $productVariation->sku,
                    'link_info' => $product->id,
                ]);

                return response()->json([
                    'success'   => false,
                    'status'    => 403,
                    'message'   => $product->collectTranslation('name') . ' ' . translate('has limited or no stock'),
                    'result'    => null
                ], 403);
            }
        }

        if ($order->delivery_status == "delivered") {
            $data = [
                'status'    => 404,
                'message'   => translate('Can not remove item from delivered order'),
                'result'    => [],
            ];
            return $data;
        }

        if ($order->payment_status == "paid") {
            $data = [
                'status'    => 404,
                'message'   => translate('Can not remove item from paid order'),
                'result'    => [],
            ];
            return $data;
        }


        # transaction 
        $transaction->amount        = $addition ? $transaction->amount + $priceDiff : $transaction->amount - $priceDiff;
        $transaction->total_amount  = $addition ? $transaction->total_amount + $priceDiff : $transaction->total_amount - $priceDiff;
        $transaction->save();

        # order
        $order->amount          = $addition ? $order->amount + $priceDiff : $order->amount - $priceDiff;
        $order->total_amount    = $addition ? $order->total_amount + $priceDiff : $order->total_amount - $priceDiff;
        $order->save();

        # stock
        if ($stock) {
            $stock->stock_qty   = $addition ? $stock->stock_qty + ($orderItem->qty - $oldOrderItem->qty) : $stock->stock_qty - ($oldOrderItem->qty - $orderItem->qty);
            $stock->save();
        }

        # product sales
        $product = $orderItem->productVariation->product;
        $product->total_sale_count = $addition ? $product->total_sale_count + ($orderItem->qty - $oldOrderItem->qty) : $product->total_sale_count - ($oldOrderItem->qty - $orderItem->qty);
        $product->save();

        OrderUpdate::create([
            'order_id'      => $order->id,
            'user_id'       => userId(),
            'status'        => 'Qty Updated',
            'created_by'    => userId(),
            'note'          => $product->name . ' (Qty updated from ' . $oldOrderItem->qty . ' to ' . $orderItem->qty . ') ',
        ]);

        $data = [
            'status'    => 200,
            'message'   => translate('Qty updated successfully'),
            'result'    => [],
        ];

        $orderItem->save();

        return $data;
    }
}
