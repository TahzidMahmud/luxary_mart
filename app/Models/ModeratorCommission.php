<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeratorCommission extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }


    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }


    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class)->withTrashed();
    }
}
