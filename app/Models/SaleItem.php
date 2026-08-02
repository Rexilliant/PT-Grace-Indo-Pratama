<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class SaleItem extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'sale_id',
        'product_stock_id',
        'quantity',
        'price',
        'discount',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'discount' => 'integer',
        'subtotal' => 'integer',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

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