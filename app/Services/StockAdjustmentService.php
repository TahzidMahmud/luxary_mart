<?php

namespace App\Services;

use App\Models\ProductVariation;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentProductVariation;
use App\Models\Warehouse;

class StockAdjustmentService
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

        $stockAdjustments = StockAdjustment::shop()->latest();
        if ($request->search != null) {
            $search         = $request->search;
            $stockAdjustments = $stockAdjustments->whereHas('warehouse', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
            $searchKey = $request->search;
        }

        $stockAdjustments = $stockAdjustments->paginate($limit);


        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'stockAdjustments'    => $stockAdjustments,
                'searchKey'         => $searchKey,
            ],
        ];

        return $data;
    }

    # Show the form for creating a new resource.
    public static function create()
    {
        $warehouses  = Warehouse::shop()->latest()->get();
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'warehouses'  => $warehouses,
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
            ->whereHas('productVariationStocks', function ($q1) use ($warehouseId) {
                $q1->where('warehouse_id', $warehouseId);
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
            $stockAdjustment = new StockAdjustment;
            StockAdjustmentService::__insertIntoDb($stockAdjustment, $request);

            $data = [
                'status'    => 200,
                'message'   => translate('Stocks have been adjusted successfully'),
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

    # store/update db data
    private static function __insertIntoDb($stockAdjustment, $request)
    {
        $stockAdjustment->shop_id         = shopId();
        $stockAdjustment->warehouse_id    = $request->warehouse_id;
        $stockAdjustment->note            = $request->note;
        $stockAdjustment->save();

        foreach ($request->selectedVariationIds as $key => $productVariationId) {
            $productVariation  = ProductVariation::find((int) $productVariationId);
            $product           = $productVariation->product;

            $stockAdjustmentProductVariation                          = new StockAdjustmentProductVariation;
            $stockAdjustmentProductVariation->product_id              = $product->id;
            $stockAdjustmentProductVariation->stock_adjustment_id     = $stockAdjustment->id;
            $stockAdjustmentProductVariation->product_variation_id    = $productVariationId;
            $stockAdjustmentProductVariation->qty                     = $request->stockQty[$key];
            $stockAdjustmentProductVariation->action                  = $request->actions[$key];
            $stockAdjustmentProductVariation->save();

            $pStock = $productVariation->productVariationStocks()->where('warehouse_id',  $stockAdjustment->warehouse_id)->first();
            if (!is_null($pStock)) {
                if ($request->actions[$key] == "addition") {
                    $pStock->stock_qty  += $request->stockQty[$key];

                    $product->stock_qty += $request->stockQty[$key];
                    $product->save();
                } else {
                    $pStock->stock_qty  -= $request->stockQty[$key];

                    $product->stock_qty -= $request->stockQty[$key];
                    $product->save();
                }
            }
            $pStock->save();
        }
    }
}
