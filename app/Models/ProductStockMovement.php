<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStockMovement extends Model
{
    protected $fillable = [
        'province',
        'product_stock_id',
        'type',
        'quantity',
        'ref_type',
        'ref_id',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'ref_id' => 'integer',
    ];

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function reference()
    {
        return $this->morphTo(null, 'ref_type', 'ref_id');
    }
}