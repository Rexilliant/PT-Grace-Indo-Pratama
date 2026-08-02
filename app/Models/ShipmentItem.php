<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ShipmentItem extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'shipment_id',
        'product_stock_id',
        'quantity',
    ];

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
