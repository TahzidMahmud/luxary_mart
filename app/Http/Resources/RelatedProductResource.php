<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RelatedProductResource extends JsonResource
{
    public function toArray($request)
    {
        $user       = apiUser();
        $wishlists  = [];
        $inWishlist = false;
        if ($user != null) {
            $wishlists = $user->wishlists()->pluck('product_id')->toArray();
            if (in_array($this->id, $wishlists)) {
                $inWishlist = true;
            }
        }

        $images = [];
        if (!is_null($this->gallery_images)) {
            $galleryImages = explode(',', $this->gallery_images);

            foreach ($galleryImages as $image) {
                array_push($images, uploadedAsset($image));
            }
        }

        $data = [
            'id'            => (int) $this->id,
            'name'          => $this->collectTranslation('name'),
            'slug'          => $this->slug,
            'thumbnailImg'  => uploadedAsset($this->thumbnail_image),
            'basePrice'     => (float) productBasePrice($this, false),
            'unit'          => $this->unit ? $this->unit->collectTranslation('name') : '',
            'stockQty'      => $this->__getStock($this),
            'hasVariation'  => (int) $this->has_variation,
            'rating'        => getProductRatings($this->productReviews()),
            'inWishlist'    => $inWishlist,
            'badges'        => ProductBadgeResource::collection($this->productBadges)
        ];

        $additionalData = [];
        $data = array_merge($data, $additionalData);
        return $data;
    }

    # get stock -- to check outOfStock as the stock is based on warehouses of selected zone
    private function __getStock($product)
    {
        $stockQty = 0;

        // if product has variations
        if ($product->has_variation == 1) {
            $productVariations = $product->variations;
            foreach ($productVariations as $productVariation) {
                $stockQty += (int) $productVariation?->productVariationStocks()->whereIn('warehouse_id', session('WarehouseIds'))->sum('stock_qty');
            }
        } else {
            // if product does not have any variations
            $productVariation   = $product->variations()->first();
            $stockQty           = $productVariation?->productVariationStocks()->whereIn('warehouse_id', session('WarehouseIds'))->sum('stock_qty');
        }

        return (int) $stockQty;
    }
}
