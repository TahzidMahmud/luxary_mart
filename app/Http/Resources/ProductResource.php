<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Traits\CategoryTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Route;


class ProductResource extends JsonResource
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

        $images = $this->thumbnail_image ? [uploadedAsset($this->thumbnail_image)] : [];
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
        if (Route::currentRouteName() == 'api.products.show') {
            $categories = $this->categories;

            // 3 related products
            $relatedProducts = collect();
            if ($categories && count($categories) > 0) {
                $categoryIds = $categories->pluck('id');
                $relatedProducts = Product::whereHas('productCategories', function ($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds)->whereNot('product_id', $this->id);
                })->take(3)->get();
            }

            // valid promoCode
            $coupon = $this->coupons()->where('start_date', '<=', strtotime(date('d-m-Y H:i:s')))->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))->first();

            // root parent category
            $rootCategory = null;
            foreach ($categories as $category) {
                $tempRoot = CategoryTrait::getRootParentCategory($category);
                if ($tempRoot != null) {
                    $rootCategory = $tempRoot;
                }
                if ($category == end($categories) && $rootCategory == null) {
                    $rootCategory = $category;
                }
            }

            $additionalData = [
                'categories'                => CategoryResource::collection($categories),
                'deliveryHours'             => $this->est_delivery_time ? $this->est_delivery_time : translate('Not Available'),
                'codAvailable'              => getSetting('cod_activation') == 1,
                'hasEmi'                    => $this->has_emi,
                'emiInfo'                   => $this->emi_info,
                'hasWarranty'               => $this->has_warranty,
                'warrantyInfo'              => $this->warranty_info,
                'shop'                      => new ShopResource($this->shopInfo),
                'description'               => $this->collectTranslation('description'),
                'images'                    => $images,
                'brand'                     => $this->brand ? new BrandResource($this->brand) : null,
                'variations'                => ProductVariationResource::collection($this->variations),
                'variationCombinations'     => generateVariationCombinations($this->variationCombinations, $this),
                'relatedProducts'           => RelatedProductResource::collection($relatedProducts),
                'promoCode'                 => $coupon ?  new CouponResource($coupon) : null,
                'rootCategory'              => $rootCategory ? new CategoryResource($rootCategory) : null,
                'tags'                      => ProductTagResource::collection($this->tags)
            ];
        }

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
                $stockQty += (int) $productVariation?->productVariationStocks()->whereIn('warehouse_id', session('WarehouseIds') ?? [1])->sum('stock_qty');
            }
        } else {
            // if product does not have any variations
            $productVariation   = $product->variations()->first();
            $stockQty           = $productVariation?->productVariationStocks()->whereIn('warehouse_id', session('WarehouseIds') ?? [1])->sum('stock_qty');
        }

        return (int) $stockQty;
    }
}
