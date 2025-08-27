<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pos\PosCartGroupResource;
use App\Http\Resources\Pos\PosCartResource;
use App\Models\Country;
use App\Models\OrderGroup;
use App\Models\PosCart;
use App\Models\PosCartGroup;
use App\Services\OrderService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    # dashboard
    public function index(Request $request)
    {
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $posCarts = collect();
        $warehouseId    = null;
        $posCartGroup   = null;
        // edit pos order
        if ($request->orderGroupId) {
            $orderGroup = OrderGroup::whereId($request->orderGroupId)->first();
            if ($orderGroup && $orderGroup->is_pos_order) {
                $transaction = $orderGroup->transaction;
                $posCartGroup = new PosCartGroup;
                $posCartGroup->user_id  = userId();
                $posCartGroup->customer_id          = $orderGroup->user_id;
                $posCartGroup->shipping_address_id  = $orderGroup->shipping_address_id;
                $posCartGroup->discount             = $transaction->discount_amount ?? 0;
                $posCartGroup->shipping             = $transaction->shipping_charge_amount ?? 0;
                $posCartGroup->advance              = $transaction->advance_payment ?? 0;
                $posCartGroup->payment_method       = $transaction->payment_method;
                $posCartGroup->note                 = $orderGroup->note;
                $posCartGroup->save();

                // orders
                $orders = $orderGroup->orders;
                foreach ($orders as  $order) {
                    $orderItems = $order->orderItems;
                    foreach ($orderItems as $orderItem) {
                        $posCart                        = new PosCart;
                        $posCart->warehouse_id          = $order->warehouse_id;
                        $posCart->qty                   = $orderItem->qty;
                        $posCart->product_variation_id  = $orderItem->product_variation_id;
                        $posCart->pos_cart_group_id     = $posCartGroup->id;
                        $posCart->save();

                        $posCarts->push($posCart);
                    }

                    $warehouseId = $order->warehouse_id;

                    // re-stock
                    $orderService = new OrderService;
                    $orderService->addQtyToStock($order);
                    $order->delivery_status = "cancelled";
                    $order->save();

                    $posCartGroup->order_receiving_date = $order->order_receiving_date;
                    $posCartGroup->order_shipment_date  = $order->order_shipment_date;
                    $posCartGroup->save();
                }
            }
        }

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
            'posCartGroup'  => $posCartGroup ? new PosCartGroupResource($posCartGroup) : null,
            'posWarehouseId'   => $warehouseId,
        ];

        $view = view('backend.admin.pos.index', compact('settings'));

        return $view;
    }
}
