<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\ProductVariation;
use Illuminate\Http\Request;

class CartController extends Controller
{
    # get all resources with pagination
    public function index(Request $request, $message = "")
    {
        return $this->__getCartsInfo($message, $request->guestUserId);
    }

    # add new resource 
    public function store(Request $request)
    {
        $message            = "";
        $user               = null;
        $productVariation   = ProductVariation::find((int) $request->productVariationId);

        if (!is_null($productVariation)) {
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
                $cart = Cart::where('user_id', $user->id)->where('warehouse_id', $request->warehouseId)->where('product_variation_id', $productVariation->id)->first();
            } else {
                $cart = Cart::where('guest_user_id', (int) $request->guestUserId)->where('warehouse_id', $request->warehouseId)->where('product_variation_id', $productVariation->id)->first();
            }

            if (!is_null($cart)) {
                $cart->qty      += (int) $request->qty;
                $message        =  translate('Quantity has been increased');
            } else {
                $cart                           = new Cart;
                $cart->warehouse_id             = $request->warehouseId;
                $cart->product_variation_id     = $productVariation->id;
                $cart->qty                      = (int) $request->qty;

                if (!is_null($user)) {
                    $cart->user_id              = $user->id;
                } else {
                    $cart->guest_user_id        = (int) $request->guestUserId;
                }

                $message =  translate('Product has been added to your cart');
            }

            $cart->save();

            return $this->__getCartsInfo($message, $request->guestUserId);
        }

        return [
            'success'   => false,
            'status'    => 404,
            'message'   => translate('Something went wrong, please try again'),
            'result'    => null
        ];
    }

    # update cart
    public function update(Request $request)
    {
        try {
            $cart = Cart::where('id', $request->id)->first();
            if ($request->action == "increase") {
                $product = $cart->productVariation->product;
                if ($product->max_purchase_qty > $cart->qty) {
                    $productVariationStock = $cart->productVariation->productVariationStocks()->where('warehouse_id', $request->warehouseId)->first();
                    $checkStock = 0;
                    if (!is_null($productVariationStock)) {
                        $checkStock = $productVariationStock->stock_qty;
                    }
                    if ($checkStock > $cart->qty) {
                        $cart->qty += 1;
                        $cart->save();
                    }
                } else {
                    $message = translate('You have reached maximum order quantity at a time for this product');
                    return $this->__getCartsInfo($message, $request->guestUserId);
                }
            } elseif ($request->action == "decrease") {
                if ($cart->qty > 1) {
                    $cart->qty -= 1;
                    $cart->save();
                }
            } else {
                $cart->delete();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->__getCartsInfo('', $request->guestUserId);
    }

    # delete resource
    public function destroy(Request $request)
    {
        $cart = Cart::where('id', (int) $request->id);

        if (auth('sanctum')->check()) {
            $cart  = $cart->where('user_id', auth('sanctum')->user()->id)->first();
        } else {
            $cart  = $cart->where('guest_user_id', (int) $request->guestUserId)->first();
        }
        if (!is_null($cart)) {
            $cart->delete();
        }

        return $this->__getCartsInfo('', $request->guestUserId);
    }

    # get carts information
    private function __getCartsInfo($message = '', $guestUserId = null, $toastVariant = 'success')
    {
        if (auth('sanctum')->check()) {
            $carts  = Cart::whereHas('productVariation')->where('user_id', apiUserId())->whereIn('warehouse_id', session('WarehouseIds'))->get();
        } else {
            $carts  = Cart::whereHas('productVariation')->where('guest_user_id', (int) $guestUserId)->whereIn('warehouse_id', session('WarehouseIds'))->get();
        }

        return [
            'success'           => true,
            'status'            => 200,
            'message'           => $message,
            'toastVariant'      => $toastVariant,
            'result'            => [
                'carts'             => CartResource::collection($carts),
            ]
        ];
    }
}
