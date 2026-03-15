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

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    /**
     * Polymorphic reference (optional)
     */
    public function reference()
    {
        return $this->morphTo(null, 'ref_type', 'ref_id');
    }
}
