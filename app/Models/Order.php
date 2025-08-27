<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Order::first() == null) {
                $model->order_code = getSetting('orderCodeStartsFrom') != null ? (int) getSetting('orderCodeStartsFrom') : 10001;
            } else {
                $max = (int) Order::max('order_code');

                if (getSetting('orderCodeStartsFrom') && $max < getSetting('orderCodeStartsFrom')) {
                    $model->order_code = getSetting('orderCodeStartsFrom');
                } else {
                    $model->order_code = $max + 1;
                }
            }
        });
    }

    // protected $dispatchesEvents = [ 'created' => OrderPlaced::class];

    public function scopeShopOrders($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function scopeIsPlaced($query)
    {
        return $query->where('delivery_status', 'order_placed');
    }

    public function scopeIsConfirmed($query)
    {
        return $query->where('delivery_status', 'confirmed');
    }

    public function scopeIsProcessing($query)
    {
        return $query->where('delivery_status', 'processing');
    }

    public function scopeIsShipped($query)
    {
        return $query->where('delivery_status', 'shipped');
    }

    public function scopeIsDelivered($query)
    {
        return $query->where('delivery_status', 'delivered');
    }

    public function scopeIsCancelled($query)
    {
        return $query->where('delivery_status', 'cancelled');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function orderGroup()
    {
        return $this->belongsTo(OrderGroup::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderUpdates()
    {
        return $this->hasMany(OrderUpdate::class)->latest();
    }

    public function commissionHistory()
    {
        return $this->hasOne(CommissionHistory::class);
    }
}
