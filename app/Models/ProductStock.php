<?php

namespace App\Models;

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
}