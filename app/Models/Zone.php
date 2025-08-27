<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function shippingCharge($shopId)
    {
        return $this->hasOne(ZoneShippingCharge::class)->where('shop_id', $shopId)->first();
    }

    public function warehouseZones($shopId)
    {
        return $this->hasOne(WarehouseZone::class)->where('shop_id', $shopId)->first();
    }
}
