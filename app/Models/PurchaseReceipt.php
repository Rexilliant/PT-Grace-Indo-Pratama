<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PurchaseReceipt extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'receipt_number',
        'procurement_id',
        'warehouse_id',
        'received_at',
        'received_by',
        'deleted_by',
        'status',
        'note',
        'total_price',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseReceiptItem::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    protected static function booted(): void
    {
        static::deleting(function ($purchaseReceipt) {
            if (
                !$purchaseReceipt->isForceDeleting()
                && is_null($purchaseReceipt->deleted_by)
                && auth()->check()
            ) {
                $purchaseReceipt->deleted_by = auth()->id();
                $purchaseReceipt->saveQuietly();
            }
        });
    }
}