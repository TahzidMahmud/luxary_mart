<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use App\Models\Product;

class StockReportController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_stock_transfer'])->only(['index']);
        $this->middleware(['permission:create_stock_transfer'])->only(['create', 'store']);
    }
    public function getName($code,$values){
        $name = '';
        $code_array = array_filter(explode('/', $code));
        $lstKey = array_key_last($code_array);

        foreach ($code_array as $key2 => $comb) {

            $comb = explode(':', $comb);
            if (isset($values[(int)$comb[1]])) {
                $choice_name = $values[$comb[1]];
            } else {
                // Handle the case where the key doesn't exist
                $choice_name = 'Unknown';
            }

            $name .= $choice_name;

            if ($lstKey != $key2) {
                $name .= '-';
            }
        }

        return $name;
    }
    public function generateStockReport()
    {
        $products = Product::with(['variations.productVariationStocks'])->get();
        $variation_values = \App\Models\VariationValue::withTrashed()->get();

        $variation_values = $variation_values->mapWithKeys(function ($item) {
            return [$item->id => $item->name];
        })->toArray();
        $reportData = $products->map(function ($product) use ($variation_values) {
            $totalStockAmount = 0;
            $totalStockValue = 0;
            $variationDetails = [];

            foreach ($product->variations as $variation) {
                $variationStock = $variation->productVariationStocks->sum('stock_qty');

                $variationValue = $variationStock * $variation->price;

                $totalStockAmount += $variationStock;
                $totalStockValue += $variationValue;


                $variationDetails[] = [
                    'name' => $this->getName($variation->code,$variation_values),
                    'stock_qty' => $variationStock
                ];
            }

            return [
                'image' => uploadedAsset($product->thumbnail_image),
                'name' => $product->name,
                'variations' => $variationDetails,
                'total_stock_amount' => $totalStockAmount,
                'total_stock_value' => $totalStockValue,
            ];
        });

        return view('backend.admin.inventory.reports.index', compact('reportData'));
    }


}
