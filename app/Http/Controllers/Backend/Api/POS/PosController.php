<?php

namespace App\Http\Controllers\Backend\Api\POS;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Resources\Pos\PosBrandResource;
use App\Http\Resources\Pos\PosCartGroupResource;
use App\Http\Resources\Pos\PosCartResource;
use App\Http\Resources\Pos\PosCategoryResource;
use App\Http\Resources\Pos\PosProductVariationResource;
use App\Http\Resources\Pos\PosWarehouseResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\PosCart;
use App\Models\PosCartGroup;
use App\Models\ProductVariation;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Warehouse;
use App\Models\Notification;
use App\Traits\CategoryTrait;
use Hash;
use DB;
use Illuminate\Http\Request;

class PosController extends Controller
{
    # warehouses, categories, brands
    public function filterData(Request $request)
    {
        $warehouses     = Warehouse::shop()->where('is_active', 1)->get(['id', 'name']);
        $brands         = Brand::isActive()->get(['id', 'name']);
        $categories     = Category::get(['id', 'name']);

        return response()->json([
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'warehouses'    => PosWarehouseResource::collection($warehouses),
                'brands'        => PosBrandResource::collection($brands),
                'categories'    => PosCategoryResource::collection($categories)
            ]
        ], 200);
    }

    # get data
    public function index(Request $request)
    {
        $limit              = $request->limit ?? perPage();
        $warehouseId        = $request->warehouseId;

        $productVariations  = ProductVariation::whereHas('product', function ($q) use ($request) {
            $q->shop();

            if ($request->brandId) {
                $q->where('brand_id', $request->brandId);
            }

            if ($request->categoryId) {
                $q->whereHas('productCategories', function ($query) use ($request) {
                    return $query->where('category_id', $request->categoryId);
                });
            }

            if ($request->searchKey != null) {
                $q->where('name', 'like', '%' . $request->searchKey . '%');
            }
        });

        $productVariations->with(['productVariationStocks' => function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }]);

        if ($request->searchKey != null) {
            $productVariations = $productVariations->orWhere('code', 'like', '%' . $request->searchKey . '%');
        }

        $productVariations = $productVariations->paginate($limit);

        return response()->json([
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => PosProductVariationResource::collection($productVariations)->response()->getData(true)
        ], 200);
    }

    # customers
    public function customers(Request $request)
    {
        if (user()->user_type != "admin") {
            $customers      = User::customers()->where('shop_id', shopId())->get(['id', 'name', 'phone']);
        } else {
            $customers      = User::customers()->get(['id', 'name', 'phone']);
        }
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $customers
        ], 200);
    }

    # store customer
    public function storeCustomer(Request $request)
    {
        $customer = null;
        if ($request->email) {
            $customer = User::where('email', $request->email)->first();
        }

        if (is_null($customer) && $request->phone) {
            $customer = User::where('phone', $request->phone)->first();
        }

        if (is_null($customer)) {
            $customer                        = new User;
            $customer->password              = Hash::make($request->password ?? '123456');
            $customer->shop_id               = shopId();
            $customer->verification_code     = rand(100000, 999999);
            $customer->email_or_otp_verified = 1;
            $customer->email_verified_at     = date('Y-m-d H:m:s');
        }

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->save();

        $this->__newAddresses($request, $customer->id);

        $customers  = User::customers()->get(['id', 'name']);
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'customers'     => $customers,
                'newCustomer'   => $customer
            ]
        ], 200);
    }

    # customer addresses
    public function customerAddresses($id)
    {
        $customer = User::whereId($id)->first();
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'addresses' => AddressResource::collection($customer->addresses()->latest()->get()),
                'countries' => Country::where('is_active', 1)->get(['id', 'name'])
            ]
        ], 200);
    }

    # store address
    public function storeAddress(Request $request)
    {
        $customer = User::where('id', $request->id)->first();
        $this->__newAddresses($request, $customer->id);
        return $this->customerAddresses($customer->id);
    }

    # new address
    private function __newAddresses(Request $request, $customerId)
    {
        if ($request->countryId != null && $request->stateId != null && $request->cityId != null && $request->areaId != null && $request->address != null) {
            $address = new UserAddress;
            $address->user_id       = $customerId;
            $address->country_id    = $request->countryId;
            $address->state_id      = $request->stateId;
            $address->city_id       = $request->cityId;
            $address->area_id       = $request->areaId;
            $address->postal_code   = $request->postalCode;
            $address->address       = $request->address;
            $address->type          = $request->type;
            $address->direction     = $request->direction;
            $address->is_default    = 0;
            $address->save();
        }
    }

    # add to pos cart
    public function addToPosCart(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null
        ];

        $posCart                        = new PosCart;
        $posCart->warehouse_id          = $request->warehouseId;
        $posCart->qty                   = $request->qty;
        $posCart->product_variation_id  = $request->productVariationId;

        if ($request->firstCartProduct) {
            $posCartGroup           = new PosCartGroup;
            $posCartGroup->user_id  = userId();
            $posCartGroup->save();
            $data["result"]["posCartGroupId"] = $posCartGroup->id;

            $posCart->pos_cart_group_id = $posCartGroup->id;
        } else {
            $posCart->pos_cart_group_id = $request->posCartGroupId;
        }
        $posCart->save();

        $posCarts = PosCart::where('pos_cart_group_id', $posCart->pos_cart_group_id)->get();

        $data["result"]["posCarts"] = PosCartResource::collection($posCarts);

        return response()->json($data, 200);
    }

    # updatePosCart
    public function updatePosCart(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null
        ];

        $posCart = PosCart::whereId($request->id)->first();
        if ($posCart) {
            if ($request->action == "increase") {
                $posCart->qty += 1;
                $posCart->save();
            } else if ($request->action == "decrease") {
                if ($posCart->qty > 1) {
                    $posCart->qty -= 1;
                    $posCart->save();
                }
            } else {
                $posCart->delete();
            }
        }

        if ($posCart) {
            $posCarts = PosCart::where('pos_cart_group_id', $posCart->pos_cart_group_id)->get();
            $data["result"]["posCarts"] = PosCartResource::collection($posCarts);
        } else {
            $data["result"]["posCarts"] = [];
        }


        return response()->json($data, 200);
    }

    # indexHoldPosCartGroup
    public function indexHoldPosCartGroup(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null
        ];

        $posCartGroups = PosCartGroup::where('user_id', userId())->where('on_hold', 1)->get();

        $data["result"]  = PosCartGroupResource::collection($posCartGroups);
        return response()->json($data, 200);
    }

    # holdPosCartGroup
    public function holdPosCartGroup(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null
        ];

        $posCartGroup = PosCartGroup::whereId($request->posCartGroupId)->first();
        if ($posCartGroup) {
            $posCartGroup->on_hold = 1;
            $posCartGroup->customer_id          = $request->customerId;
            $posCartGroup->shipping_address_id  = $request->shippingAddressId;
            $posCartGroup->discount             = $request->discount ?? 0;
            $posCartGroup->shipping             = $request->shipping ?? 0;
            $posCartGroup->advance              = $request->advance ?? 0;
            $posCartGroup->payment_method       = $request->paymentMethod;
            $posCartGroup->order_receiving_date = $request->orderReceivingDate;
            $posCartGroup->order_shipment_date  = $request->orderShipmentDate;
            $posCartGroup->note                 = $request->note;
            $posCartGroup->save();
        }

        return response()->json($data, 200);
    }

    # deleteHoldPosCartGroup
    public function deleteHoldPosCartGroup($id)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null
        ];

        $posCartGroup = PosCartGroup::whereId($id)->first();
        if ($posCartGroup) {
            $posCartGroup->posCarts()->delete();
            $posCartGroup->delete();
        }

        return response()->json($data, 200);
    }

    # storeOrder
    public function storeOrder(Request $request)
    {
        $posCartGroup   = PosCartGroup::where('id', $request->posCartGroupId)->first();
        $posCarts       = $posCartGroup->posCarts;

        // empty cart
        if (count($posCarts) == 0) {
            return response()->json([
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Your cart is empty. Please select a product'),
                'result'    => null
            ], 403);
        }

        // out of stock check
        foreach ($posCarts as $cart) {
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


        // start order submission
        DB::beginTransaction();

        try {
            $authUser   = user();
            $customer   = null;

            if ($request->customerId) {
                $customer  = User::where('id', $request->customerId)->first();
            }

            $orderGroup                         = new OrderGroup;
            $orderGroup->is_pos_order           = 1;
            $orderGroup->user_id                = $request->customerId;
            $orderGroup->name                   = $customer?->name;
            $orderGroup->email                  = $customer?->email;
            $orderGroup->phone                  = $customer?->phone;
            $orderGroup->note                   = $request->note;
            $orderGroup->shipping_address_id    = $request->shippingAddressId;
            $orderGroup->save();

            if ($orderGroup->shippingAddress) {
                $address                            = $orderGroup->shippingAddress;
                $orderGroup->shipping_address_type  = $address->type;
                $orderGroup->direction              = $address->direction;
                $orderGroup->shipping_address       = $address->address . ", " . $address->area->name . ", " . $address->city->name . ", " . $address->state->name . ", " . $address->postal_code . ", " . $address->country->name;
            }


            $grandTotal             = 0;

            $grandSubtotal          = 0;
            $grandTax               = 0;
            $grandShippingCharge    = 0;
            $grandDiscount          = 0;
            $grandCouponDiscount    = 0;

            // store order here
            $order = new Order;
            $order->user_id                 = $request->customerId;
            $order->pos_order               = 1;
            $order->order_group_id          = $orderGroup->id;
            $order->shop_id                 = shopId();
            $order->order_receiving_date    = $request->orderReceivingDate;
            $order->order_shipment_date     = $request->orderShipmentDate;
            $order->created_by              = $authUser->id;
            $order->save();

            $shopSubTotal       = 0;
            $shopTax            = 0;
            $shopShippingCharge = $request->shippingCharge ?? 0;
            $shopDiscount       = $request->discount ?? 0;
            $shopCouponDiscount = 0;

            foreach ($posCarts as $posCart) {
                $productVariation       = $posCart->productVariation;
                $product                = $productVariation->product;

                $itemTotalPriceWithoutTax    = variationPrice($product, $productVariation, false) * $posCart->qty;
                $itemTotalTax                = (variationPrice($product, $productVariation) * $posCart->qty) - $itemTotalPriceWithoutTax;
                $itemTotalDiscount           = $itemTotalPriceWithoutTax - (variationDiscountedPrice($product, $productVariation, false) * $posCart->qty);

                $shopSubTotal   += $itemTotalPriceWithoutTax;
                $shopTax        += $itemTotalTax;
                $shopDiscount   += $itemTotalDiscount;

                // add order item [done]
                $orderItem = new OrderItem;
                $orderItem->order_id                = $order->id;
                $orderItem->product_variation_id    = $posCart->product_variation_id;
                $orderItem->qty                     = $posCart->qty;
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
                $stock = $productVariation->productVariationStocks()->where('warehouse_id', $posCart->warehouse_id)->first();
                if ($stock) {
                    $stock->stock_qty -= $orderItem->qty;
                    $stock->save();
                }

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

                $order->warehouse_id        = $posCart->warehouse_id;
            }

            $order->amount                  = $shopSubTotal;
            $order->tax_amount              = $shopTax;
            $order->shipping_charge_amount  = $shopShippingCharge;
            $order->discount_amount         = $shopDiscount;
            $order->coupon_discount_amount  = $shopCouponDiscount;
            $order->advance_payment         = $request->advancePayment ?? 0;
            $order->total_amount            = $shopSubTotal + $shopTax + $shopShippingCharge - $shopDiscount - $shopCouponDiscount;
            $order->save();

            // grand summary
            $grandSubtotal          += $shopSubTotal;
            $grandTax               += $shopTax;
            $grandShippingCharge    += $shopShippingCharge;
            $grandDiscount          += $shopDiscount;
            $grandCouponDiscount    += $shopCouponDiscount;

            $grandTotal =  $grandSubtotal + $grandTax + $grandShippingCharge - $grandDiscount - $grandCouponDiscount;

            // transaction
            $transaction                            = new Transaction;
            $transaction->amount                    = $grandSubtotal;
            $transaction->tax_amount                = $grandTax;
            $transaction->shipping_charge_amount    = $grandShippingCharge;
            $transaction->discount_amount           = $grandDiscount;
            $transaction->coupon_discount_amount    = $grandCouponDiscount;
            $transaction->advance_payment           = $request->advancePayment ?? 0;
            $transaction->total_amount              = $grandTotal;
            $transaction->payment_method            = $request->paymentMethod;
            $transaction->save();

            $orderGroup->transaction_id = $transaction->id;
            $orderGroup->save();

            // if cash or card - paid order
            if ($request->paymentMethod == "cash" ||  $request->paymentMethod == "card") {
                $this->__paymentSuccess(null, $orderGroup->id);
            }

            // clear cart
            $posCartGroup->posCarts()->delete();
            $posCartGroup->delete();

            // delete old order group & orders
            if ($request->orderGroupId && !empty($request->orderGroupId)) {

                $orderGroup = OrderGroup::whereId($request->orderGroupId)->first();
                if ($orderGroup && $orderGroup->is_pos_order) {
                    // orders
                    $orders = $orderGroup->orders;
                    foreach ($orders as  $orderToDelete) {
                        $orderToDelete->orderItems()->delete();
                        $orderToDelete->delete();
                    }
                    $orderGroup->delete();
                }
            }

            // complete transaction
            DB::commit();

            try {
                if ($customer) {
                    sendSMSViaBulkSmsBd($customer->phone, "Your order has been placed successfully. Your invoice number: " . $order->order_code . ". " . env('APP_NAME'));
                }
            } catch (\Exception $e) {
                //dd($e->getMessage());
            }

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Order has been placed successfully'),
                'result'    => [
                    'orderId'   => $order->id
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

    # paid order
    private function __paymentSuccess($payment_details = null, $orderGroupId = null)
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
    }
}
