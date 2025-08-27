<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\UserAddress;
use App\Models\WarehouseZone;
use App\Models\ZoneShippingCharge;
use App\Notifications\OrderPlacedNotification;
use App\Services\OrderService;
use App\Traits\CategoryTrait;
use DB;
use Illuminate\Http\Request;
use Notification as GlobalNotification;

class CheckoutController extends Controller
{
    # get shipping charges for shops of the address
    public function getShippingCharge(Request $request)
    {

        $shopIds = $request->shopIds;
        $address = UserAddress::findOrFail((int) $request->addressId);

        if (!is_null($address)) {
            $coupons = $request->coupons ? Coupon::whereIn('code', $request->coupons)->get() : collect();
            $zoneId  = $address->area->zone_id;

            $shippingChargeAmount   = 0;

            foreach ($shopIds as $shopId) {
                $isFreeShipping = false;

                foreach ($coupons as $coupon) {
                    if ($coupon->shop_id == $shopId && $coupon->is_free_shipping) {
                        $isFreeShipping = true;
                    }
                }

                if (!$isFreeShipping) {
                    $shippingCharge = ZoneShippingCharge::where('shop_id', $shopId)->where('zone_id', $zoneId)->first();

                    if ($shippingCharge) {
                        $shippingChargeAmount += $shippingCharge->charge;
                    } else {
                        $shop = Shop::find((int) $shopId);
                        if ($shop) {
                            $shippingChargeAmount += $shop->default_shipping_charge;
                        }
                    }
                }
            }

            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => $shippingChargeAmount
            ];
        }
    }

    # store new order
    public function store(Request $request)
    {
        // user 
        $user = apiUser();

        // shipping address not selected
        if (!$request->shippingAddressId) {
            return response()->json([
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Please select shipping address'),
                'result'    => null
            ], 403);
        }

        // billing address not selected
        if (!$request->billingAddressId) {
            return response()->json([
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Please select billing address'),
                'result'    => null
            ], 403);
        }

        // shipment is not available
        $shippingAddress = UserAddress::find($request->shippingAddressId);

        if (!$shippingAddress) {
            return response()->json([
                'success'   => false,
                'status'    => 403,
                'message'   => translate('We could not find your address in our system'),
                'result'    => null
            ], 403);
        }

        if (!$shippingAddress->area->zone) {
            return response()->json([
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Sorry, delivery is not available in this shipping address'),
                'result'    => null
            ], 403);
        }

        // carts
        $carts  = Cart::whereHas('productVariation')->whereIn('id', $request->cartIds)->where('user_id', apiUserId())->whereIn('warehouse_id', session('WarehouseIds'))->get();

        // empty cart
        if (count($carts) == 0) {
            return response()->json([
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Your cart is empty. Please select a product'),
                'result'    => null
            ], 403);
        }

        // out of stock check
        foreach ($carts as $cart) {
            $stock = $cart->productVariation->productVariationStocks()->where('warehouse_id', $cart->warehouse_id)->first();
            if (!$stock || $stock->stock_qty < $cart->qty) {
                $productVariation = $cart->productVariation;
                $product = $productVariation->product;

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

        // get coupon data based on request
        $coupons = collect();
        if ($request->couponCodes && !empty($request->couponCodes)) {
            $coupons = Coupon::where(function ($query) use ($request) {
                foreach ($request->couponCodes as $couponCode) {
                    $query->orWhere('code', $couponCode);
                }
            })->get();
        }

        // start order submission
        DB::beginTransaction();

        try {
            $orderGroup                         = new OrderGroup;
            $orderGroup->user_id                = $user->id;
            $orderGroup->name                   = $request->name;
            $orderGroup->email                  = $request->email;
            $orderGroup->phone                  = $request->phone;
            $orderGroup->alternative_phone      = $request->alternativePhone;
            $orderGroup->note                   = $request->note;
            $orderGroup->shipping_address_id    = $request->shippingAddressId;
            $orderGroup->billing_address_id     = $request->billingAddressId;
            $orderGroup->save();

            if ($orderGroup->shippingAddress) {
                $address = $orderGroup->shippingAddress;
                $orderGroup->shipping_address_type  = $address->type;
                $orderGroup->direction  = $address->direction;
                $orderGroup->shipping_address = $address->address . ", " . $address->area->name . ", " . $address->city->name . ", " . $address->state->name . ", " . $address->postal_code . ", " . $address->country->name;
            }

            if ($orderGroup->billingAddress) {
                $address = $orderGroup->billingAddress;
                $orderGroup->billing_address_type  = $address->type;
                $orderGroup->billing_address = $address->address . ", " . $address->area->name . ", " . $address->city->name . ", " . $address->state->name . ", " . $address->country->name;
            }

            $grandTotal             = 0;

            $grandSubtotal          = 0;
            $grandTax               = 0;
            $grandShippingCharge    = 0;
            $grandDiscount          = 0;
            $grandCouponDiscount    = 0;

            if ($request->shopIds) {
                foreach ($request->shopIds as $shopId) {

                    $shop = Shop::find((int) $shopId);

                    $shopCarts  = Cart::where('user_id', $user->id)
                        ->whereIn('warehouse_id', session('WarehouseIds'))
                        ->whereHas('productVariation', function ($q) use ($shopId) {
                            $q->whereHas('product', function ($q) use ($shopId) {
                                $q->where('shop_id', $shopId);
                            });
                        })
                        ->get();

                    if (count($shopCarts) > 0) {
                        // store order here
                        $order = new Order;
                        $order->user_id         = $user->id;
                        $order->order_group_id  = $orderGroup->id;
                        $order->shop_id         = $shopId;
                        $order->warehouse_id    = $shopCarts->first()->warehouse_id;
                        $order->save();

                        $shopSubTotal       = 0;
                        $shopTax            = 0;
                        $shopShippingCharge = 0;
                        $shopDiscount       = 0;
                        $shopCouponDiscount = 0;

                        // shipping charge
                        $shippingCharge = ZoneShippingCharge::where('shop_id', $shopId)->where('zone_id', $shippingAddress->area->zone_id)->first();
                        if ($shippingCharge) {
                            $shopShippingCharge = $shippingCharge->charge;
                        } else {
                            $shopShippingCharge = $shop->default_shipping_charge;
                        }

                        // shop coupon check
                        $coupon = null;
                        if ($request->couponCodes && !empty($request->couponCodes)) {
                            $coupon = $coupons->firstWhere('shop_id', $shopId);

                            //  discount calculation & coupon usage & free shipping
                            if ($coupon) {
                                $shopCouponDetails = (new CouponController)->calculateDiscount($shopCarts, $coupon);

                                if ($shopCouponDetails['applicable'] == true) {

                                    // free shipping
                                    if ($shopCouponDetails['freeShipping'] == true) {
                                        $shopShippingCharge = 0;
                                    }

                                    $shopCouponDiscount = $shopCouponDetails['amount'];

                                    $coupon_usage               = new CouponUsage;
                                    $coupon_usage->user_id      = apiUserId();
                                    $coupon_usage->order_id     = $order->id;
                                    $coupon_usage->coupon_code  = $coupon->code;
                                    $coupon_usage->save();

                                    $order->coupon_id  = $coupon->id;
                                }
                            }
                        }

                        foreach ($shopCarts as $shopCart) {
                            $productVariation       = $shopCart->productVariation;
                            $product                = $productVariation->product;

                            $itemTotalPriceWithoutTax    = variationPrice($product, $productVariation, false) * $shopCart->qty;
                            $itemTotalTax                = (variationPrice($product, $productVariation) * $shopCart->qty) - $itemTotalPriceWithoutTax;
                            $itemTotalDiscount           = $itemTotalPriceWithoutTax - (variationDiscountedPrice($product, $productVariation, false) * $shopCart->qty);

                            $shopSubTotal   += $itemTotalPriceWithoutTax;
                            $shopTax        += $itemTotalTax;
                            $shopDiscount   += $itemTotalDiscount;

                            // add order item [done]
                            $orderItem = new OrderItem;
                            $orderItem->order_id                = $order->id;
                            $orderItem->product_variation_id    = $shopCart->product_variation_id;
                            $orderItem->qty                     = $shopCart->qty;
                            $orderItem->unit_price              = variationPrice($product, $productVariation, false);
                            $orderItem->total_tax               = $itemTotalTax;
                            $orderItem->total_discount          = $itemTotalDiscount;
                            $orderItem->total_price             = $itemTotalPriceWithoutTax + $itemTotalTax - $itemTotalDiscount;
                            $orderItem->save();

                            // manage stock and sales
                            $product->total_sale_count += $orderItem->qty;
                            $product->stock_qty        -= $orderItem->qty;
                            $product->save();

                            // product variation stock
                            $stock = $productVariation->productVariationStocks()->where('warehouse_id', $shopCart->warehouse_id)->first();
                            $stock->stock_qty -= $orderItem->qty;
                            $stock->save();

                            // category sales count for only root category
                            $categories = $product->categories;
                            $added = [];
                            foreach ($categories as $cat) {
                                $rootParentCategory = CategoryTrait::getRootParentCategory($cat);
                                if (!in_array($rootParentCategory->id, $added)) {
                                    $rootParentCategory->total_sale_count += $orderItem->qty;
                                    $rootParentCategory->total_sale_amount += $orderItem->total_price;
                                    $rootParentCategory->save();
                                    array_push($added, $rootParentCategory->id);
                                }
                            }

                            // sales brands count
                            $brand = $product->brand;
                            if ($brand) {
                                $brand->total_sale_count += $orderItem->qty;
                                $brand->total_sale_amount += $orderItem->total_price;
                                $brand->save();
                            }
                        }

                        $order->amount                  = $shopSubTotal;
                        $order->tax_amount              = $shopTax;
                        $order->shipping_charge_amount  = $shopShippingCharge;
                        $order->discount_amount         = $shopDiscount;
                        $order->coupon_discount_amount  = $shopCouponDiscount;
                        $order->total_amount            = $shopSubTotal + $shopTax + $shopShippingCharge - $shopDiscount - $shopCouponDiscount;
                        $order->save();

                        # ---------------------> commissions calculated in payment success <---------------------

                        // order update
                        OrderUpdate::create([
                            'order_id'  => $order->id,
                            'user_id'   => apiUserId(),
                            'status'    => 'Order Placed',
                            'note'      => 'Order has been placed.',
                        ]);

                        // notification
                        Notification::create([
                            'shop_id'   => $shopId,
                            'for'       => 'shop',
                            'type'      => 'order',
                            'text'      => 'Invoice ' . $order->order_code,
                            'link_info' => $order->order_code,
                        ]);

                        // grand summary
                        $grandSubtotal          += $shopSubTotal;
                        $grandTax               += $shopTax;
                        $grandShippingCharge    += $shopShippingCharge;
                        $grandDiscount          += $shopDiscount;
                        $grandCouponDiscount    += $shopCouponDiscount;
                    }
                }

                $grandTotal =  $grandSubtotal + $grandTax + $grandShippingCharge - $grandDiscount - $grandCouponDiscount;
                // transaction
                $transaction                            = new Transaction;
                $transaction->amount                    = $grandSubtotal;
                $transaction->tax_amount                = $grandTax;
                $transaction->shipping_charge_amount    = $grandShippingCharge;
                $transaction->discount_amount           = $grandDiscount;
                $transaction->coupon_discount_amount    = $grandCouponDiscount;
                $transaction->total_amount              = $grandTotal;
                $transaction->payment_method            = $request->paymentMethod;
                $transaction->save();

                $orderGroup->transaction_id = $transaction->id;
                $orderGroup->save();

                // clear user's cart
                Cart::destroy($request->cartIds);
            }
            // complete transaction
            DB::commit();


            // send notifications if cod or cardOD
            if ($request->paymentMethod == "cash_on_delivery" ||  $request->paymentMethod == "card_on_delivery") {
                try {
                    // OrderPlaced::dispatch($orderGroup);
                    GlobalNotification::send($user, new OrderPlacedNotification($orderGroup));
                } catch (\Exception $e) {
                }
            }

            try {
                if ($order) {
                    sendSMSViaBulkSmsBd($user->phone, "Your order has been placed successfully. Your invoice number: " . $order->order_code . ". " . env('APP_NAME'));
                }
            } catch (\Exception $e) {
                //dd($e->getMessage());
            }

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Your order has been placed successfully'),
                'result'    => [
                    'orderCode'     => $orderGroup->code,
                    'goToPayment'   => $request->paymentMethod != "cash_on_delivery" &&  $request->paymentMethod != "card_on_delivery" ? true : false,
                    'paymentMethod' => $request->paymentMethod,
                ]
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success'   => false,
                'status'    => 500,
                'message'   => translate('Something went wrong'),
                'result'    => null
            ], 500);
        }
    }

    # payment success
    public static function paymentSuccess($payment_details = null, $orderGroupId = null)
    {
        if ($orderGroupId == null) {
            $orderGroupId = session('order_group_id');
        }

        $orderGroup  = OrderGroup::where('id', $orderGroupId)->first();
        $orders      = $orderGroup->orders;

        foreach ($orders as $order) {
            calculateCommission($order);
            $order->payment_status = 'paid';
            $order->save();
        }

        $transaction = $orderGroup->transaction;
        $transaction->status = "paid";
        $transaction->payment_details = $payment_details ? json_encode($payment_details) : null;
        $transaction->save();

        try {
            GlobalNotification::send($orderGroup->user, new OrderPlacedNotification($orderGroup));
        } catch (\Exception $e) {
        }

        clearPaymentSession();
        $redirect_to = route('home') . "/orders/success/" . $orderGroup->code;
        return redirect($redirect_to);
    }

    # payment failed 
    public static function paymentFailed($orderGroupId = null)
    {
        if ($orderGroupId == null) {
            $orderGroupId = session('order_group_id');
        }

        $orderGroup = OrderGroup::where('id', $orderGroupId)->first();

        if (!is_null($orderGroup)) {
            $transaction = $orderGroup->transaction;
            $orders      = $orderGroup->orders;

            $orderService = new OrderService;

            foreach ($orders as $order) {
                $order->delivery_status = 'cancelled';
                $order->save();
                $orderService->addQtyToStock($order);
            }

            $transaction->status = "failed";
            $transaction->save();
        }

        clearPaymentSession();
        $redirect_to = route('home') . "/orders/failed";
        return redirect($redirect_to);
    }

    # update zone
    public function updateZone(Request $request)
    {
        // carts
        $carts  = Cart::whereHas('productVariation')->where('user_id', apiUserId())->get();

        // stock check
        foreach ($carts as $cart) {
            $productVariation = $cart->productVariation;
            $product = $productVariation->product;
            $shopId  = $product->shop_id;

            $warehouseZone = WarehouseZone::where('shop_id', $shopId)->where('zone_id', $request->newZoneId)->first();
            if (is_null($warehouseZone)) {
                $cart->delete();
                continue;
            }

            $stock = $productVariation->productVariationStocks()->where('warehouse_id', $warehouseZone->warehouse_id)->first();

            if (is_null($stock)) {
                $cart->delete();
                continue;
            }

            if ($stock->stock_qty == 0) {
                $cart->delete();
                continue;
            }

            if ($stock->stock_qty < $cart->qty) {
                $cart->qty = $stock->stock_qty;
                $cart->save();

                // notification
                Notification::create([
                    'shop_id'   => $shopId,
                    'for'       => 'shop',
                    'type'      => 'stock',
                    'text'      => 'SKU - ' . $productVariation->sku,
                    'link_info' => $product->id,
                ]);
                continue;
            }

            $cart->warehouse_id = $warehouseZone->warehouse_id;
            $cart->save();
        }
        $cartController = new CartController;
        return $cartController->index($request, translate('Cart updated successfully'));
    }
}
