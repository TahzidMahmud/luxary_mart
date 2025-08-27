<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes;

    protected $guarded  = [];

    protected $with     = ['productTranslations'];

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function shopInfo()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function scopeFromPublishedShops($query)
    {
        if (config('app.app_mode') == "singleVendor") {
            return $query->where('shop_id', 1);
        } else {
            # publishedShopsIds cache must be reset on shop status changes 
            $publishedShopsIds = Cache::rememberForever('publishedShopsIds', function () {
                return  Shop::isApproved()->isPublished()->pluck('id')->toArray();
            });
            return $query->whereIn('shop_id', $publishedShopsIds);
        }
    }

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', 1);
    }

    public function productTranslations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->productTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function variationCombinations()
    {
        return $this->hasMany(ProductVariationCombination::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function productTaxes()
    {
        return $this->belongsToMany(Tax::class, 'product_taxes', 'product_id', 'tax_id');
    }

    public function badges()
    {
        return $this->hasMany(ProductBadge::class);
    }

    public function productBadges()
    {
        return $this->belongsToMany(Badge::class, 'product_badges', 'product_id', 'badge_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    public function productTags()
    {
        return $this->hasMany(ProductTag::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function campaignProducts()
    {
        return $this->hasMany(CampaignProduct::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_products', 'product_id', 'coupon_id');
    }

    public function couponProducts()
    {
        return $this->hasMany(CouponProduct::class);
    }
}
