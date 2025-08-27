<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    # get all resources with pagination
    public function index(Request $request, $message = '')
    {
        $wishlists = auth('sanctum')->user()->wishlists ?? [];
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => $message,
            'result'    => WishlistResource::collection($wishlists)
        ];
    }

    # add new resource 
    public function store(Request $request)
    {
        $userId = auth('sanctum')->user()->id;
        $wishlist = Wishlist::where('user_id', $userId)->where('product_id', $request->productId)->first();

        if (is_null($wishlist)) {
            $wishlist               = new Wishlist;
            $wishlist->user_id      = $userId;
            $wishlist->product_id   = $request->productId;
            $wishlist->save();
            return $this->index($request, translate('Product added to your wishlist'));
        } else {
            $wishlist->delete();
            return $this->index($request, translate('Product removed from your wishlist'));
        }
    }

    # delete resource
    public function destroy(Request $request)
    {
        auth('sanctum')->user()->wishlists()->where('product_id', $request->productId)->delete();
        return $this->index($request, translate('Product removed from your wishlist'));
    }
}
