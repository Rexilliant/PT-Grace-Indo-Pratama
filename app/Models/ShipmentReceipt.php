<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentReceipt extends Model
{
    use SoftDeletes;

    protected $table = 'shipment_receipts';

    protected $fillable = [
        'shipment_id',
        'status',
        'received_by_id',
        'received_at',
        'approved_by_id',
        'approved_at',
        'rejected_by_id',
        'rejected_at',
        'reject_reason',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    public function items()
    {
        return $this->hasMany(ShipmentReceiptItem::class);
    }
}
