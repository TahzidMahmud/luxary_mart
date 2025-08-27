<?php

namespace App\Services;

use App\Models\ProductVariation;
use App\Models\ProductVariationStock;
use App\Models\StockTransfer;
use App\Models\StockTransferProductVariation;

class StockTransferService
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

        $stockTransfers = StockTransfer::shop()->latest();
        if ($request->search != null) {
            $search         =  $request->search;
            $stockTransfers = $stockTransfers->whereHas('fromWarehouse', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('toWarehouse', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
            $searchKey = $request->search;
        }

        $stockTransfers = $stockTransfers->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'stockTransfers'    => $stockTransfers,
                'searchKey'         => $searchKey,
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

            if ($request->from_warehouse_id == $request->to_warehouse_id) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Stocks can not be transferred in same warehouse'),
                    'result'    => [],
                ];
                return $data;
            }
            $stockTransfer = new StockTransfer;
            StockTransferService::__insertIntoDb($stockTransfer, $request);
            $data = [
                'status'    => 200,
                'message'   => translate('Stocks have been transferred successfully'),
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
    private static function __insertIntoDb($stockTransfer, $request)
    {
        $stockTransfer->shop_id             = shopId();
        $stockTransfer->from_warehouse_id   = $request->from_warehouse_id;
        $stockTransfer->to_warehouse_id     = $request->to_warehouse_id;
        $stockTransfer->note                = $request->note;
        $stockTransfer->save();

        foreach ($request->selectedVariationIds as $key => $productVariationId) {
            $productVariation  = ProductVariation::find((int) $productVariationId);
            $product           = $productVariation->product;

            $stockTransferProductVariation                          = new StockTransferProductVariation;
            $stockTransferProductVariation->product_id              = $product->id;
            $stockTransferProductVariation->stock_transfer_id       = $stockTransfer->id;
            $stockTransferProductVariation->product_variation_id    = $productVariationId;
            $stockTransferProductVariation->qty                     = $request->stockQty[$key];
            $stockTransferProductVariation->save();

            $fromWarehouseStock = $productVariation->productVariationStocks()->where('warehouse_id',  $stockTransfer->from_warehouse_id)->first();
            $fromWarehouseStock->stock_qty -= $request->stockQty[$key];
            $fromWarehouseStock->save();

            $pStock = $productVariation->productVariationStocks()->where('warehouse_id',  $stockTransfer->to_warehouse_id)->first();
            if (!is_null($pStock)) {
                $pStock->stock_qty += $request->stockQty[$key];
            } else {
                $pStock = new ProductVariationStock;
                $pStock->product_variation_id  = $productVariationId;
                $pStock->warehouse_id          = $stockTransfer->to_warehouse_id;
                $pStock->stock_qty         += $request->stockQty[$key];
            }
            $pStock->save();
        }
    }
}
