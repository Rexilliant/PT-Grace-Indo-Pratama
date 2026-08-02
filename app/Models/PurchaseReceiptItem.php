<?php

namespace App\Models;

use App\Models\PurchaseReceipt;
use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;


class PurchaseReceiptItem extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = [
        'purchase_receipt_id',
        'raw_material_id',
        'quantity_received',
    ];

    public function purchaseReceipt()
    {
        return $this->belongsTo(PurchaseReceipt::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
