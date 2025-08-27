<?php

namespace App\Http\Services;

class ProductService
{

    # specific columns
    public function getSpecificColumns()
    {
        return [
            'id',
            'shop_id',
            'name',
            'slug',
            'brand_id',
            'unit_id',
            'thumbnail_image',
            'min_price',
            'max_price',
            'discount_info',
            'stock_qty',
            'is_published',
            'min_purchase_qty',
            'max_purchase_qty',
            'has_variation',
            'has_warranty',
            'reward_points',
        ];
    }

}
