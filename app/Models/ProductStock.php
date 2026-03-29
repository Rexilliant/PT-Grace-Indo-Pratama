<?php

namespace App\Models;

use App\Models\ProductionBatch;
use App\Models\ProductStockMovement;
use App\Models\ProductVariant;
use App\Models\ShipmentItem;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStock extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'product_variant_id',
        'warehouse_id',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
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
        return $this->hasMany(ShipmentItem::class);
    }

    public function movements()
    {
        return $this->hasMany(ProductStockMovement::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
