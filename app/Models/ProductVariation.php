<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function combinations()
    {
        return $this->hasMany(ProductVariationCombination::class)->withTrashed();
    }

    public function productVariationStocks()
    {
        return $this->hasMany(ProductVariationStock::class);
    }

    public function campaignProducts()
    {
        return $this->hasMany(CampaignProduct::class);
    }
}
