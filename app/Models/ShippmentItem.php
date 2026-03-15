<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippmentItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shippment_id',
        'product_stock_id',
        'quantity',
    ];

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}
