<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_variant_id',
        'warehouse_id',
        'stock',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function productionBatches()
    {
        return $this->hasMany(ProductionBatch::class);
    }

    public function shipmentItems()
    {
        return $this->hasMany(ShippmentItem::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
