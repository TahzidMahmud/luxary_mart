<?php

namespace App\Services;

use App\Models\ProductVariation;
use App\Models\ProductVariationStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProductVariation;
use App\Models\PurchaseReturnOrder;
use App\Models\PurchaseReturnOrderProductVariation;
use App\Models\Supplier;
use App\Models\Warehouse;

class PurchaseOrderService
{
    # get data
    public static function index($request, $limit)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;

        $purchaseOrders = PurchaseOrder::shop()->latest();
        if ($request->search != null) {
            $search         = str_replace('#PO-', '', $request->search);
            $search         = str_replace('PO-', '', $search);
            $purchaseOrders = $purchaseOrders->where('reference_code', 'like', '%' . $search . '%');
            $searchKey = $request->search;
        }

        $purchaseOrders = $purchaseOrders->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'purchaseOrders'    => $purchaseOrders,
                'searchKey'         => $searchKey,
            ],
        ];

        return $data;
    }

    # Show the form for creating a new resource.
    public static function create()
    {
        $suppliers   = Supplier::shop()->latest()->get();
        $warehouses  = Warehouse::shop()->latest()->get();
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'suppliers'  => $suppliers,
                'warehouses' => $warehouses,
            ],
        ];
        return $data;
    }

    # Show the form for editing the specified resource.
    public static function getProducts($request)
    {
        $warehouseId = $request->warehouseId;
        $selectedVariationIds  = $request->selectedVariationIds ?? [];

        $productVariations = ProductVariation::whereNotIn('id', $selectedVariationIds)
            ->whereHas('product', function ($q) use ($request) {
                $q->shop()->where('name', 'like', '%' . $request->searchKey . '%');
            })
            ->paginate(perPage(20));

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'productVariations'     => $productVariations,
                'selectedVariationIds'  => $selectedVariationIds,
                'warehouseId'           => $warehouseId,
            ],
        ];

        return $data;
    }

    # add new data
    public static function store($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {
            $purchaseOrder                  = new PurchaseOrder;
            PurchaseOrderService::__insertIntoDb($purchaseOrder, $request);
            $data = [
                'status'    => 200,
                'message'   => translate('Purchase order has been created successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # return view of edit form
    public static function edit($request, $id)
    {
        try {

            $purchaseOrder = PurchaseOrder::findOrFail((int)$id);
            if ($purchaseOrder->return) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Returned order can not be updated'),
                    'result'    => [],
                ];
                return $data;
            }

            if ($purchaseOrder->status == "cancelled") {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Cancelled order can not be updated'),
                    'result'    => [],
                ];
                return $data;
            }

            $suppliers   = Supplier::shop()->latest()->get();
            $warehouses  = Warehouse::shop()->latest()->get();
            $data = [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'purchaseOrder'      => $purchaseOrder,
                    'suppliers'          => $suppliers,
                    'warehouses'         => $warehouses,
                ],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # add new data
    public static function update($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {

            $purchaseOrder = PurchaseOrder::findOrFail((int) $id);

            if ($purchaseOrder->return) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Returned order can not be updated'),
                    'result'    => [],
                ];
                return $data;
            }

            if ($purchaseOrder->status == "cancelled") {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Cancelled order can not be updated'),
                    'result'    => [],
                ];
                return $data;
            }

            PurchaseOrderService::__insertIntoDb($purchaseOrder, $request);
            $data = [
                'status'    => 200,
                'message'   => translate('Purchase Order has been updated successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # delete data
    public static function destroy($id)
    {
        try {

            $purchaseOrder = PurchaseOrder::where('id', $id)->first();
            if (!is_null($purchaseOrder)) {

                if ($purchaseOrder->status == "received") {
                    $data = [
                        'success'   => false,
                        'status'    => 403,
                        'message'   => translate('Received order can not be cancelled'),
                        'result'    => [],
                    ];
                    return $data;
                }

                $purchaseOrder->status = "cancelled";
                $purchaseOrder->save();
            }

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Purchase order has been cancelled successfully'),
                'result'    => null
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # store/update db data
    private static function __insertIntoDb($purchaseOrder, $request)
    {
        if ($request->warehouse_id) {
            // means new resource
            $purchaseOrder->warehouse_id    = $request->warehouse_id;
        } else {
            // updating -- as in update method warehouse_id is not in requests
            $purchaseOrderProductVariations = $purchaseOrder->orders;
            foreach ($purchaseOrderProductVariations as $purchaseOrderProductVariation) {
                $productVariationStock = ProductVariationStock::where('product_variation_id', $purchaseOrderProductVariation->product_variation_id)->where('warehouse_id', $purchaseOrder->warehouse_id)->first();
                if (!is_null($productVariationStock)) {
                    # if purchaseOrder status is received - remove qty from stocks
                    if ($purchaseOrder->status == "received") {
                        $productVariationStock->stock_qty -= $purchaseOrderProductVariation->qty;
                        $productVariationStock->save();

                        $product                = $purchaseOrderProductVariation->product;
                        $product->stock_qty     -= $purchaseOrderProductVariation->qty;
                        $product->save();
                    }
                }

                # delete purchaseOrderProductVariations
                $purchaseOrderProductVariation->delete();
            }
        }

        $purchaseOrder->shop_id         = shopId();
        $purchaseOrder->supplier_id     = $request->supplier_id;
        $purchaseOrder->date            = strtotime($request->date);
        $purchaseOrder->status          = $request->status;
        $purchaseOrder->sub_total       = array_sum($request->subtotal) ?? 0;
        $purchaseOrder->tax_percentage  = $request->tax;
        $purchaseOrder->tax_value       = ($purchaseOrder->sub_total *  $request->tax) / 100;
        $purchaseOrder->shipping        = $request->shipping;
        $purchaseOrder->discount        = $request->discount;
        $purchaseOrder->grand_total     = $purchaseOrder->sub_total + $purchaseOrder->tax_value + $purchaseOrder->shipping - $purchaseOrder->discount;
        $purchaseOrder->due             = $purchaseOrder->grand_total;
        $purchaseOrder->note            = $request->note;
        $purchaseOrder->save();

        // purchase order items
        $grandTotal = 0;
        foreach ($request->selectedVariationIds as $key => $productVariationId) {
            $productVariation               = ProductVariation::withTrashed()->find((int) $productVariationId);
            $product                        = $productVariation->product;

            $prevStock = 0;
            $pStock = $productVariation->productVariationStocks()->where('warehouse_id',  $purchaseOrder->warehouse_id)->first();
            if (!is_null($pStock)) {
                $prevStock = $pStock->stock_qty;
                if ($purchaseOrder->status == "received") {
                    $pStock->stock_qty += $request->stockQty[$key];

                    $product->stock_qty += $request->stockQty[$key];
                    $product->save();
                }
            } else {
                $pStock = new ProductVariationStock;
                $pStock->product_variation_id  = $productVariationId;
                $pStock->warehouse_id          = $purchaseOrder->warehouse_id;
                if ($purchaseOrder->status == "received") {
                    $pStock->stock_qty  += $request->stockQty[$key];

                    $product->stock_qty += $request->stockQty[$key];
                    $product->save();
                } else {
                    $pStock->stock_qty = 0;
                }
            }
            $pStock->save();

            $unitDiscount                   = variationPrice($product, $productVariation, false) - variationDiscountedPrice($product, $productVariation, false);
            $unitTax                        =  variationPrice($product, $productVariation) - variationPrice($product, $productVariation, false);

            $purchaseVariation              = new PurchaseOrderProductVariation;

            $purchaseVariation->prev_stock              = $prevStock;
            $purchaseVariation->product_id              = $product->id;
            $purchaseVariation->purchase_order_id       = $purchaseOrder->id;
            $purchaseVariation->product_variation_id    = $productVariationId;
            $purchaseVariation->base_unit_price         = $productVariation->price;
            $purchaseVariation->unit_price              = $request->unitPrice[$key];
            $purchaseVariation->qty                     = $request->stockQty[$key];
            $purchaseVariation->sub_total               = $purchaseVariation->unit_price *  $purchaseVariation->qty;
            $purchaseVariation->discount                = $unitDiscount *  $purchaseVariation->qty;
            $purchaseVariation->tax                     = $unitTax *  $purchaseVariation->qty;
            $purchaseVariation->grand_total             =  $purchaseVariation->sub_total + $purchaseVariation->tax - $purchaseVariation->discount;
            $purchaseVariation->save();

            $grandTotal += $purchaseVariation->grand_total;
        }

        $purchaseOrder->grand_total     = $grandTotal + $purchaseOrder->tax_value + $purchaseOrder->shipping - $purchaseOrder->discount;

        $payments   = $purchaseOrder->payments;
        $totalPaid  = $payments->sum('paid_amount');
        $purchaseOrder->due = $purchaseOrder->grand_total - $totalPaid;

        if ($purchaseOrder->due > 0) {
            $purchaseOrder->payment_status = "unpaid";
        } else {
            $purchaseOrder->payment_status = "paid";
        }


        $purchaseOrder->save();
    }

    # Display a listing of the resource.
    public static function returnIndex($request, $limit = 15)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];


        $searchKey = null;

        $returnOrders = PurchaseReturnOrder::shop()->latest();
        if ($request->search != null) {
            $search         = str_replace('#PR-', '', $request->search);
            $search         = str_replace('PR-', '', $search);
            $returnOrders = $returnOrders->where('reference_code', 'like', '%' . $search . '%');
            $searchKey = $request->search;
        }

        $returnOrders = $returnOrders->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'returnOrders'    => $returnOrders,
                'searchKey'       => $searchKey,
            ],
        ];

        return $data;
    }

    # Show the form for returning a new resource.
    public static function returnCreate($request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail((int)$id);
        if ($purchaseOrder->status == "cancelled") {
            $data = [
                'status'    => 403,
                'message'   => translate('Cancelled order can not be returned'),
                'result'    => [],
            ];
            return $data;
        }

        if ($purchaseOrder->status != "received") {
            $data = [
                'status'    => 403,
                'message'   => translate('Only received orders can be returned'),
                'result'    => [],
            ];
            return $data;
        }

        $suppliers   = Supplier::shop()->latest()->get();
        $warehouses  = Warehouse::shop()->latest()->get();

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'purchaseOrder' => $purchaseOrder,
                'suppliers'     => $suppliers,
                'warehouses'    => $warehouses,
            ],
        ];
        return $data;
    }

    # add new data
    public static function returnStore($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {
            $purchaseOrder                    = PurchaseOrder::findOrFail((int) $id);
            $purchaseOrderProductVariations   = $purchaseOrder->orders;

            if (count($purchaseOrderProductVariations) !== count($request->selectedVariationIds)) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Something went wrong'),
                    'result'    => [],
                ];
                return $data;
            }

            if ($purchaseOrder->status == "cancelled") {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Cancelled order can not be returned'),
                    'result'    => [],
                ];
                return $data;
            }

            if ($purchaseOrder->status != "received") {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Only received orders can be returned'),
                    'result'    => [],
                ];
                return $data;
            }

            $returnOrder = $purchaseOrder->return;
            $oldReturnOrderProductVariations = collect();

            if (is_null($returnOrder)) {
                $returnOrder = new PurchaseReturnOrder;
            } else {
                $oldReturnOrderProductVariations = $returnOrder->orders;
            }

            foreach ($oldReturnOrderProductVariations as  $oldReturnOrderProductVariation) {
                if ($returnOrder->status == "completed") {
                    // add to stock again which were previously returned
                    $productVariationStock = ProductVariationStock::where('product_variation_id', $oldReturnOrderProductVariation->product_variation_id)->where('warehouse_id', $purchaseOrder->warehouse_id)->first();

                    if (!is_null($productVariationStock)) {
                        $productVariationStock->stock_qty += $oldReturnOrderProductVariation->returned_qty;
                        $productVariationStock->save();

                        $product = $oldReturnOrderProductVariation->product;
                        $product->stock_qty += $oldReturnOrderProductVariation->returned_qty;
                        $product->save();
                    }
                }
                $oldReturnOrderProductVariation->delete();
            }

            // update with new requests
            $returnOrder->purchase_order_id  = $purchaseOrder->id;
            $returnOrder->shop_id            = $purchaseOrder->shop_id;
            $returnOrder->supplier_id        = $purchaseOrder->supplier_id;
            $returnOrder->warehouse_id       = $purchaseOrder->warehouse_id;
            $returnOrder->date               = strtotime($request->date);
            $returnOrder->status             = $request->status;
            $returnOrder->sub_total          = array_sum($request->subtotal) ?? 0;
            $returnOrder->tax_percentage     = $request->tax;
            $returnOrder->tax_value          = ($returnOrder->sub_total *  $request->tax) / 100;
            $returnOrder->shipping           = $request->shipping;
            $returnOrder->discount           = $request->discount;
            $returnOrder->grand_total        = $returnOrder->sub_total + $returnOrder->tax_value + $returnOrder->shipping - $returnOrder->discount;
            $returnOrder->due                = $returnOrder->grand_total;
            $returnOrder->note               = $request->note;
            $returnOrder->save();

            $grandTotal = 0;
            foreach ($purchaseOrderProductVariations as $key => $purchaseOrderProductVariation) {

                $productVariation   = ProductVariation::withTrashed()->find((int) $purchaseOrderProductVariation->product_variation_id);
                $product            = $productVariation->product;

                $pStock             = $productVariation->productVariationStocks()->where('warehouse_id',  $purchaseOrder->warehouse_id)->first();
                if (!is_null($pStock)) {
                    if ($returnOrder->status == "completed") {
                        $pStock->stock_qty -= $request->stockQty[$key];

                        $product->stock_qty        -= $request->stockQty[$key];
                        $product->save();
                    }
                    $pStock->save();
                }

                $unitDiscount  = $purchaseOrderProductVariation->discount / $purchaseOrderProductVariation->qty;
                $unitTax       =  $purchaseOrderProductVariation->tax / $purchaseOrderProductVariation->qty;

                $returnOrderProductVariation = new PurchaseReturnOrderProductVariation;
                $returnOrderProductVariation->product_id                = $product->id;
                $returnOrderProductVariation->purchase_return_order_id  = $returnOrder->id;
                $returnOrderProductVariation->purchase_order_id         = $purchaseOrder->id;
                $returnOrderProductVariation->product_variation_id      = $productVariation->id;
                $returnOrderProductVariation->base_unit_price           = $productVariation->price;
                $returnOrderProductVariation->unit_price                = $request->unitPrice[$key];
                $returnOrderProductVariation->qty                       = $purchaseOrderProductVariation->qty;
                $returnOrderProductVariation->returned_qty              = $request->stockQty[$key];
                $returnOrderProductVariation->sub_total                 = $returnOrderProductVariation->unit_price *  $returnOrderProductVariation->returned_qty;
                $returnOrderProductVariation->discount                  = $unitDiscount *  $returnOrderProductVariation->returned_qty;
                $returnOrderProductVariation->tax                       = $unitTax *  $returnOrderProductVariation->returned_qty;
                $returnOrderProductVariation->grand_total               =  $returnOrderProductVariation->sub_total + $returnOrderProductVariation->tax - $returnOrderProductVariation->discount;
                $returnOrderProductVariation->save();

                $grandTotal += $returnOrderProductVariation->grand_total;
            }
            $returnOrder->grand_total     = $grandTotal + $returnOrder->tax_value + $returnOrder->shipping - $returnOrder->discount;

            $payments   = $returnOrder->payments;
            $totalPaid  = $payments->sum('paid_amount');

            $returnOrder->due             = $returnOrder->grand_total - $totalPaid;

            if ($returnOrder->due > 0) {
                $returnOrder->payment_status = "unpaid";
            } else {
                $returnOrder->payment_status = "paid";
            }
            $returnOrder->save();

            $data = [
                'status'    => 200,
                'message'   => translate('Purchase return has been updated successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }
}
