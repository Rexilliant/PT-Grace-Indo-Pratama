<?php

namespace App\Models;

use App\Models\ProductionBatch;
use App\Models\ProductVariant;
use App\Models\ShippmentItem;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_variant_id',
        'province',
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
}
