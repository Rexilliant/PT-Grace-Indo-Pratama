<?php

namespace App\Models;

use App\Models\ProductStock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStockMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'warehouse_id',
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
