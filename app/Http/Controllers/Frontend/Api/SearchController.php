<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Services\ProductService;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    # get all products with pagination
    public function index(Request $request)
    {
        $products = Product::fromPublishedShops()->isPublished()->with('variations');
        $brands   = Brand::isActive();
        $shops    = Shop::isApproved()->isPublished();

        // by search keyword
        if ($request->searchKey != null) {
            $products   = $products->where('name', 'like', '%' . $request->searchKey . '%');
            $brands     = $brands->where('name', 'like', '%' . $request->searchKey . '%');
            $shops      = $shops->where('name', 'like', '%' . $request->searchKey . '%');
        }

        $columns    = (new ProductService)->getSpecificColumns();
        $products   = $products->take(5)->get($columns);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'         => ProductResource::collection($products),
                'brands'           => BrandResource::collection($brands->take(3)->get()),
                'shops'            => ShopResource::collection($shops->take(3)->get()),
            ]
        ];
    }
}
