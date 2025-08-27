<?php

namespace App\Http\Controllers\Backend\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariation;
use App\Models\ProductVariationStock;
use App\Services\OrderService;
use App\Services\PurchaseOrderService;
use App\Traits\CategoryTrait;
use Illuminate\Http\Request;
use App\Models\Country;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;

        $this->middleware(['permission:view_orders'])->only(['index']);
        $this->middleware(['permission:manage_orders'])->only(['show', 'updateOrderTracking', 'storeOrderUpdates', 'deleteOrderUpdate', 'updatePaymentStatus', 'updateDeliveryStatus', 'updateOrderAddress']);
    }

    # get all resources
    // public function index(Request $request)
    // {
    //     $response = $this->orderService->index($request);

    //     if ($response['status'] == 200) {
    //         return view('backend.admin.orders.index', $response['result']);
    //     }
    //     flash($response['message'] ?? translate('Something went wrong'))->error();
    //     return redirect()->route('admin.dashboard');
    // }
    public function index(Request $request)
    {

        $response = $this->orderService->index($request);
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            # currency settings
            'currency'      => [
                'code'      => getSetting('currencyCode') ?? 'usd',
                'symbol'    => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1  : 0] : 'symbol_first',

                    'show'  => getSetting('currencySymbol') ?? '$'
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals'     => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator'  => getSetting('decimalSeparator') ?? '.',
            ],
            'countries'     => Country::where('is_active', 1)->get(),
        ];

        // $view = view('backend.admin.pos.index', compact('settings'));
        if ($response['status'] == 200) {
            $orders=$response['result'];
            return view('backend.admin.orders.index', compact('settings','orders'));

        }
        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # show orders
    public function show($code)
    {
        $response = $this->orderService->show($code);
        if ($response['status'] == 200) {
            return view('backend.admin.orders.show', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.orders.index');
    }

    # download invoice
    public function downloadInvoice($id)
    {
        // return view('backend.admin.orders.invoice');
        return $this->orderService->downloadInvoice($id);
    }

    # store or update order tracking
    public function updateOrderTracking(Request $request)
    {
        $response = $this->orderService->updateOrderTracking($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # store new order updates
    public function storeOrderUpdates(Request $request)
    {
        $response = $this->orderService->storeOrderUpdates($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # delete order update
    public function deleteOrderUpdate($id)
    {
        $response = $this->orderService->deleteOrderUpdate($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # update payment status
    public function updatePaymentStatus(Request $request)
    {
        $response = $this->orderService->updatePaymentStatus($request);
        if ($response['status'] == 200) {
            return true;
        }
        return false;
    }

    # update delivery status
    public function updateDeliveryStatus(Request $request)
    {
        $response = $this->orderService->updateDeliveryStatus($request);
        if ($response['status'] == 200) {
            return true;
        }
        return false;
    }

    # update Order Address
    public function updateOrderAddress(Request $request)
    {
        $response = $this->orderService->updateOrderAddress($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # removeOrderItem
    public function removeOrderItem($id)
    {
        $response = $this->orderService->removeOrderItem($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # updateQty
    public function updateQty(Request $request)
    {
        $response = $this->orderService->updateQty($request);
        return $response;
    }

    # Show the form for editing the specified resource.
    public function getProducts(Request $request)
    {
        $response = PurchaseOrderService::getProducts($request);
        return view('backend.admin.orders.product-search-results', $response['result']);
    }

    # update order
    public function updateOrder(Request $request)
    {
        $order = Order::whereId((int) $request->order_id)->first();

        if ($order->delivery_status == "delivered" || $order->delivery_status == "cancelled") {
            flash(translate('Delivered & Cancelled Orders can not be updated'))->error();
            return back();
        }

        if ($order->payment_status == "paid") {
            flash(translate('Paid Orders can not be updated'))->error();
            return back();
        }

        // out of stock check
        foreach ($request->selectedVariationIds as $key => $productVariationId) {
            $productVariation   = ProductVariation::withTrashed()->find((int) $productVariationId);
            $stock              = $productVariation->productVariationStocks()->where('warehouse_id',  $order->warehouse_id)->first();
            if (!$stock || $stock->stock_qty < $request->stockQty[$key]) {
                $product = $productVariation->product;
                // notification
                Notification::create([
                    'shop_id'   => $product->shop_id,
                    'for'       => 'shop',
                    'type'      => 'stock',
                    'text'      => 'SKU - ' . $productVariation->sku,
                    'link_info' => $product->id,
                ]);

                flash($product->collectTranslation('name') . ' ' . translate('has limited or no stock'))->error();
                return back();
            }
        }

        foreach ($request->selectedVariationIds as $key => $productVariationId) {
            $productVariation               = ProductVariation::withTrashed()->find((int) $productVariationId);
            $product                        = $productVariation->product;


            $itemTotalPriceWithoutTax       = variationPrice($product, $productVariation, false) * $request->stockQty[$key];
            $itemTotalTax                   = (variationPrice($product, $productVariation) * $request->stockQty[$key]) - $itemTotalPriceWithoutTax;
            $itemTotalDiscount              = $itemTotalPriceWithoutTax - (variationDiscountedPrice($product, $productVariation, false) * $request->stockQty[$key]);

            // add order item [done]
            $orderItem = new OrderItem;
            $orderItem->order_id                = $order->id;
            $orderItem->product_variation_id    = $productVariationId;
            $orderItem->qty                     = $request->stockQty[$key];
            $orderItem->unit_price              = variationPrice($product, $productVariation, false);
            $orderItem->total_tax               = $itemTotalTax;
            $orderItem->total_discount          = $itemTotalDiscount;
            $orderItem->total_price             = $itemTotalPriceWithoutTax + $itemTotalTax - $itemTotalDiscount;
            $orderItem->save();

            $order->amount          += $orderItem->total_price;
            $order->tax_amount      += $orderItem->total_tax;
            $order->discount_amount += $orderItem->total_discount;
            $order->total_amount    += $orderItem->total_price;
            $order->save();

            $orderGroup = $order->orderGroup;
            $transaction = $orderGroup->transaction;
            $transaction->amount        += $orderItem->total_price;
            $transaction->total_amount  += $orderItem->total_price;
            $transaction->save();

            // manage stock and sales
            $product->total_sale_count += $orderItem->qty;
            $product->stock_qty        -= $orderItem->qty;
            $product->save();

            // product variation stock
            $stock = $productVariation->productVariationStocks()->where('warehouse_id', $order->warehouse_id)->first();
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
        }

        flash(translate('Order updated successfully'))->success();
        return back();
    }
}
