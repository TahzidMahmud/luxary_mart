<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosCartGroup extends Model
{
    use HasFactory;

    # posCarts
    public function posCarts()
    {
        return $this->hasMany(PosCart::class)->whereHas('productVariation');
    }

    public  function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
