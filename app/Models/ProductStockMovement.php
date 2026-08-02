<?php

namespace App\Models;

use App\Models\ProductStock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ProductStockMovement extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'warehouse_id',
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
