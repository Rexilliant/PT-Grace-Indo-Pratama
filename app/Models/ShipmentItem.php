<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shipment_id',
        'product_stock_id',
        'quantity',
    ];

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}
