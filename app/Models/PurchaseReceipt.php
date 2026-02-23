<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PurchaseReceipt extends Model implements Hasmedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'receipt_number',
        'procurement_id',
        'province',
        'received_at',
        'received_by',
        'status',
        'note',
        'total_price',
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

    protected $casts = [
        'received_at' => 'datetime',
    ];
}
